<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10
 * Time: 12:05
 */

namespace App\Logic;


use App\Http\Response\ApiResponse;
use App\Model\EthToken;
use App\Model\OutsideCoinTradeOrder;
use App\Server\HuoBiServer\LibServer\LibServer;
use App\Server\HuoBiServer\Server\HuobiServer;
use App\Server\OutsideTrade\Dao\OutsideWalletDao;
use App\Traits\RedisTool;
use App\Traits\Tools;
use Illuminate\Support\Facades\DB;

class UserOutsideWalletLogic
{
    use
    ApiResponse,RedisTool,Tools;
    protected $walletDao;
    protected $huoBiServer;

    protected $coinTradeOrder;

    public function __construct(OutsideWalletDao $outsideWalletDao,OutsideCoinTradeOrder $outsideCoinTradeOrder)
    {

        $this->walletDao = $outsideWalletDao;
        $this->coinTradeOrder = $outsideCoinTradeOrder;
    }




    public function getBalance($data)
    {

        $balance = $this->walletDao->getUsableBalance($data['user_id'],$data['coin_id']);
        return $this->successWithData(['balance'=>$balance]);
    }


    //返回钱包首页数据
    public function index($userId)
    {
        //1.货币的列表
        $walletList = $this->walletDao->walletIndex($userId);//dd($walletList);

        $totalBalance = $this->getTotalBalance($walletList);
        foreach ($walletList as $key=>$value){
            $walletList[$key]['wallet_usable_balance'] = bcadd($value['wallet_usable_balance'],$value['wallet_withdraw_balance'],8);
        }
        return $this->successWithData(['walletList'=>$walletList,'totalBalance'=>$totalBalance[0],'totalCNY'=>$totalBalance[1]]);

    }

    /*计算总的余额近似值,全部换算成btc*/
    public function getTotalBalance($walletList)
    {
        $this->huoBiServer = new HuobiServer(new LibServer());
//        $usdtId = $this->getCoinId('USDT');
        $totalCny = 0;
//        $totalUSDT = 0;
        foreach ($walletList as $wallet){
            switch ($wallet['coin_name']['coin_name']){
//                case 'ICIC':
//                    $exchangeCny = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
//                    $totalCny = bcadd($exchangeCny,$totalCny,2);
//                    break;
                case 'USDT':
                    $usdtRate = DB::table('coin_exchange_rate')->where(['virtual_coin_id'=>$wallet['coin_id']])->first()->rate;
                    $exchangeCny = bcmul($usdtRate,bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
                    $totalCny = bcadd($exchangeCny,$totalCny,2);
                    break;
                default:
                    if (EthToken::where('coin_id',$wallet['coin_id'])->first()){
                        $exchangeCny = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
                        $totalCny = bcadd($exchangeCny,$totalCny,2);
                    }else {
                        $exchangeCny = bcmul($this->huoBiServer->getOneMerged(strtolower($wallet['coin_name']['coin_name'] . 'usdt'))['CNY_price'], bcadd(bcadd($wallet['wallet_usable_balance'], $wallet['wallet_withdraw_balance'], 8), $wallet['wallet_freeze_balance'], 8), 6);
//                    $exchangeCny = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
                        $totalCny = bcadd($exchangeCny, $totalCny, 2);
                    }
                    break;
            }
        }

        return [bcdiv($totalCny,$usdtRate,2),$totalCny];
    }




    /*获取指定账户的余额详情*/
    public function getWalletDetail($userId,$walletId)
    {
        if (! ($result = $this->walletDao->getRecordById($walletId,$userId)))
            return $this->parameterError();

        $result['wallet_total_balance'] = bcadd($result['wallet_usable_balance'],$result['wallet_withdraw_balance'],8);
        //$result['wallet_total_balance'] = (string)number_format(($result['wallet_usable_balance'] + $result['wallet_freeze_balance']),8,'.','');
        return $this->successWithData(['walletDetail'=>$result]);
    }

    /*充值信息接口*/
    public function getRechargeMsg($userId,$walletId)
    {

        $data = $this->walletDao->getRechargeMsg($userId,$walletId);
//        $data['transfer_msg'] = '1.资金划转手续费为0.7%';
        if ($data['coin_fees']['recharge_on_off_status'])
            return $this->successWithData($data);

        return $this->responseByENCode('STATUS_CODE_CANNOT_HANDLE',$data['coin_name']['coin_name'].'充值暂未开放');
    }

    /*提币信息接口*/
    public function getWithdrawMsg($userId,$walletId)
    {
        $data = $this->walletDao->getWithdrawMsg($userId,$walletId);
        $data['wallet_usable_balance'] = $data['wallet_withdraw_balance'];
//        $data['transfer_msg'] = '1.资金划转手续费为0.7%';
        if ($data['coin_fees']['withdraw_on_off_status'])
            return $this->successWithData($data);

        return $this->responseByENCode('STATUS_CODE_CANNOT_HANDLE',$data['coin_name']['coin_name'].'提币暂未开放');
    }


    //提币
    public function withdraw($userId,$walletId,$toAddress,$amount)
    {//dd($this->getAddressType($toAddress));
        $result = $this->walletDao->getRecordById($walletId,$userId);
        if (! $result) return $this->parameterError();//检查账户和userid对不对的上

        if (strcasecmp($result['wallet_address'],$toAddress) == 0)
            return $this->responseByENCode('STATUS_CODE_CANNOT_TRANSFER_SELF','不能给自己转账');

        if (!$result['coin_fees']['withdraw_on_off_status'])
            return $this->responseByENCode('STATUS_CODE_CANNOT_HANDLE',$result['coin_name']['coin_name'].'提币暂不开放');

        if (
            (bccomp($result['coin_fees']['withdraw_min'],$amount,8)==1)
            || (bccomp($amount,$result['coin_fees']['withdraw_max'],8)==1)
        ){
            return $this->responseByENCode('STATUS_CODE_AMOUNT_ERROR','限额'.$result['coin_fees']['withdraw_min'].'到'.$result['coin_fees']['withdraw_max']);
        }

        //检查数据库的可用余额是否大于提取的金额amount
        $totalAmount = bcadd($amount,$result['coin_fees']['fixed_fee'],8);
        if (bccomp($totalAmount,$result['wallet_withdraw_balance'],8) == 1)
            return $this->responseByENCode('STATUS_CODE_NOTSUFFICIENT_FUNDS','余额不足');
//dd($result);

        if (! $this->validateAddress($result['coin_name']['coin_name'],$toAddress))
            return $this->responseByENCode('STATUS_CODE_ADDRESS_ERROR','地址不正确');
        return $this->withdrawLogic($walletId,$toAddress,$amount,$result['coin_fees']['fixed_fee']);
    }


    private function withdrawLogic($walletId,$toAddress,$amount,$fees)
    {//dd(1);
        DB::beginTransaction();
        $wallet = $this->walletDao->getWalletById($walletId,['*'],1);
        $totalAmount = bcadd($amount,$fees,8);
        $transferType = $this->getAddressType($toAddress);
        $result1 = $this->coinTradeOrder->saveOneRecord($wallet->user_id,$wallet->coin_id,'','',$toAddress,$amount,1,$fees,$transferType);
        $result2 = $this->walletDao->subWithdrawBalance($walletId,$totalAmount);
        $result3 = $this->walletDao->addFreezeBalance($walletId,$totalAmount);
        if ($result1 && $result2 && $result3){
            DB::commit(); return $this->success();
        }
        DB::rollBack();return $this->error();

    }


    private function validateAddress($coinName,$address)
    {
        switch ($coinName){
            case 'BTC':
                return $this->isBTCAddress($address);
                break;
            case 'ETH':
               return $this->isETHAddress($address);
                break;
            case 'USDT':
                return $this->isBTCAddress($address);
                break;
            default:
                return $this->isETHAddress($address);
                break;
        }
    }




}