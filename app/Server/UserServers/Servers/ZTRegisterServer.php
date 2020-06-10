<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 10:44
 */

namespace App\Server\UserServers\Servers;

use App\Events\UserRegisterBehavior;
use App\Handlers\Helpers;
use App\Server\UserServers\Interfaces\RegisterServerInterface;
use App\Server\UserServers\Dao\UserDao;
use App\Traits\RedisTool;
use Auth;
use App\Traits\Tools;
use function GuzzleHttp\Promise\is_fulfilled;
use Illuminate\Support\Facades\DB;
use App\Server\CoinServer;
use App\Server\CoinServers\GethServer;
use App\Server\CoinServers\GethTokenServer;
use App\Server\CoinServers\BitCoinServer;
use App\Server\UserServers\Dao\WalletDetailDao;
use App\Model\CoinType;
use App\Model\UserTradeDatum;

class ZTRegisterServer implements RegisterServerInterface
{
    use RedisTool,Tools;
    private $userDao;
//    private $walletDetail;
    private $coinType;
    private $walletDetailDao;
    private $userTradeDatum;
    public function __construct()
    {
        $this->userDao = new UserDao();
        $this->walletDetailDao = new WalletDetailDao();
        $this->coinType = new CoinType();
        $this->userTradeDatum = new UserTradeDatum();
    }


    public function checkRegisterCode($phone,$code)
    {
        $record = $this->userDao->getOneDataByPhone($phone);
        if ($record) return 0;//已存在手机号

        if (! $result = $this->checkCode($phone,$code)){
            return 1;//请重新发送验证码
        }elseif ($result == 1){
            return 2;//正确
        }else{
            return 3;//错误
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 用户数据入库
     */
    public function saveUserMsg(array $data,$single = 0)
    {

//        if ($this->userDao->getOneData(['user_name'=>$data['nickname']],['user_id'])){
//            return 0;//昵称被占用
//        }

        if ($this->userDao->getOneDataByPhone($data['phone'])){
            return 1;
        }

        if (isset($data['invitation_code'])){
            $pUser = $this->userDao->getOneData(['user_Invitation_code'=>$data['invitation_code']],['user_id']);
            if (!$pUser) return 2;
            $data['pid'] = $pUser->user_id;
        }

        $data['user_Invitation_code'] = $this->createInviteCode();
        $data['user_reg_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['is_new'] = 1;
//dd($data);
        try{
            DB::beginTransaction();
            if ($user = $this->userDao->saveUserMsg($data)){

                $rand = rand(1,9999);

                $data = $this->pjData($user->toArray());
                $data['token'] = $single ? Auth::guard('api')->claims(['rand'=>$rand])->fromUser($user) : Auth::guard('api')->fromUser($user);
                $this->createWallet($user->user_id);//创建钱包
                //$this->createDatum($user->user_id);//创建交易信息
                DB::commit();
//                if(isset($user->pid)) (new Helpers())->increaseCoinNum($user->pid);//邀请奖励

                if ($single) $this->stringSetex('SINGLE:POINT_TOKEN'.$data['user_id'],86400,$rand);

//                event(new UserRegisterBehavior($user->user_id));
                return $data;
                //return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'注册成功','data'=>$data]);
            }
            DB::rollBack();
            return 3;
            //return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'注册失败']);
        }catch (\Exception $e){
            DB::rollBack();
            return 4;
            //return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'注册失败']);
        }

    }

    private function pjData($data)
    {
        $data['is_business'] = 0;
        $data['user_auth_level'] = 0;
        return $data;
    }


    public function createDatum($userId)
    {
        return $this->userTradeDatum->saveOneRecord($userId);
    }


    /*创建钱包*/
    public function createWallet(int $userId)
    {
        $coinType = $this->coinType->getAllCoinType();

        foreach ($coinType as $coin){
            if (! $this->walletDetailDao->getOneRecord($userId,$coin['coin_id'])){
                switch ($coin['coin_name']){
                    case 'BTC':
//                        $address = (new BitCoinServer())->getAccountAddress('btc_user_'.$userId);
                        (new CoinServer())->createNewAccount($userId,$coin['coin_id'],'btc_user_'.$userId,'BTC');
                        break;
                    case 'ETH':
                        $password = 'eth_pass_' . $userId;
//                        $address = (new GethServer())->newAccount($password);
                        (new CoinServer())->createNewAccount($userId,$coin['coin_id'],'','ETH','',$password);
                        break;
                }
            }
        }
        //dd($coinType);
        foreach ($coinType as $coin){
            if (! $this->walletDetailDao->getOneRecord($userId,$coin['coin_id'])){
                switch ($coin['coin_name']){
//                    case 'ICIC':
//                        (new CoinServer())->createNewAccount($userId,$coin['coin_id'],'','BABC');
//                        break;
//                    case 'USDT':
//                        (new CoinServer())->createNewAccount($userId,$coin['coin_id'],'','USDT');
//                        break;
                    default:
                        (new CoinServer())->createNewAccount($userId,$coin['coin_id'],'',$coin['coin_name']);
                        break;
                }
            }
        }

    }


}