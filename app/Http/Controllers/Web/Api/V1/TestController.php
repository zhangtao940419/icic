<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/6/27
 * Time: 13:35
 */
/*用于测试的控制器*/
namespace App\Http\Controllers\Web\Api\V1;

use App\Events\SendEmailEvent;
use App\Exceptions\ApiException;
use App\Handlers\ExcelHtml;
use App\Handlers\ExchangeHelper;
use App\Handlers\Helpers;
use App\Http\Response\ApiResponse;
use App\Jobs\OrePoolDayAutoFree;
use App\Jobs\StoReturn;
use App\Jobs\transfer_lock_auto_free;
use App\Model\ContractActivity;
use App\Model\ContractPriceFloat;
use App\Model\ContractUserBuyRecords;
use App\Model\InsideSetting;
use App\Model\InsideTradeBuy;
use App\Model\InsideTradeSell;
use App\Model\StoCoinData;
use App\Model\User;
use App\Model\UserBuyStoCoinRecord;
use App\Model\UserIdentify;
use App\Server\AdminCoinServer;
use App\Server\AdminOutsideCoinServer;
use App\Server\CoinServers\GethServer;
use App\Server\CoinServers\OmnicoreServer;
use App\Server\IdentifyVerifyServers\BankCard;
use App\Server\IdentifyVerifyServers\Identify;
use App\Server\InsideTrade\InsideTradeServer;
use App\Server\MessagePushServers\MessagePush;
use App\Server\PlcServer\PlcServer;
use App\Server\SMSServers\Servers\HQYServer;
use App\Server\UserServers\Dao\WalletDetailDao;
use App\Traits\QiNiuFileTool;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Traits\RedisTool;
use App\Server\CoinServers\BitCoinServer;
use App\Traits\Tools;
use Illuminate\Support\Facades\Mail;
use App\Model\BankList;
use App\Model\EthToken;
use App\Server\CoinServers\GethTokenServer;
use App\Model\C2CTrade;
use App\Model\WalletDetail;
use App\Model\OmnicoreToken;
use Illuminate\Support\Facades\File;
use App\Model\InsideTradeOrder;

class TestController extends Controller{

    use RedisTool,Tools,ApiResponse,QiNiuFileTool;
    private $auth;

    public function __construct(JWTAuth $auth){

        $this->auth =$auth;
    }

    public function index(OmnicoreServer $omnicoreServer, Request $request, PlcServer $chatServer, BankCard $bankCard, InsideTradeServer $insideTradeServer, WalletDetail $walletDetail)
    {
//        $records = (new UserBuyStoCoinRecord())->where(['data_id'=>19])->get();
//        foreach ($records as $record){
//            dispatch(new StoReturn($record->record_id));
//        }


dd(1);

        dd($this->changeTo_Other_Coin(8));


        dd(array_merge([1],[2]));

        dd((new ExchangeHelper())->getCoinPrice());

        dispatch(new transfer_lock_auto_free(112,1));

        dd($insideTradeServer->getAllCoin());

        dd($bankCard->checkBankCardId('622622310135519900000'));





        dd($chatServer->transferToChat('0xed29f548ae30476a738268d099391356b75c864s','0xd162b4bbc5f910d4d1e32e4b2b5ddd16b902ce88',600,'ICIC'));


        $re = $this->sendEmailMessage('775072322@qq.com',151551);dd($re);
        //dd(1);
       // $result = (new AdminCoinServer())->checkWithdrawCoin(,,1);dd($result);

        dd($omnicoreServer->getAccountAddress('test1'));

        //$propertyId = OmnicoreToken::where('coin_id',5)->value('property_id');dd($propertyId);

        $result = $omnicoreServer->send('mpZAbQoqNUwAJEhisNyTiqDAqyAtYAWAmE','mtoKHXYRHUemBiXQoy3GcQbaceeCCTXQ5z','2','0.1');
        dd($result);


//event(new SendEmailEvent('775072322@qq.com',151551));
        $this->sendEmailMessage('775072322@qq.com',151551);
return $this->success();
//        dd(0.000000000000001+0.000000000000078);
//        dd($this->auth->parseToken()->authenticate()->toArray());
//        return '6564';


    }

    //excel
    public function test1(InsideTradeBuy $insideTradeBuy,InsideTradeSell $insideTradeSell,User $user,UserIdentify $userIdentify,WalletDetail $walletDetail,PlcServer $plcServer,InsideSetting $insideSetting
    ,ContractPriceFloat $contractPriceFloat)
    {
        dd(file_get_contents('https://api.etherscan.io/api?module=account&action=balance&address=0xa70468ab520B7fFEBbE0816474CC1e1e2648026e&tag=latest'));

        $re = app('sms')->setProvider(new HQYServer())->setSignature('TTS')->sendCodeMsg(15574832499,123456);
        dd($re);



        dd($contractPriceFloat->check_total_num());

        dd(getRandFloatNumber(100.11,-10,-1));
        dd((new ContractUserBuyRecords())->open(77));

        dd($insideSetting->getFee(120,8));
        dd($plcServer->checkIsAddress('0x884549a902902450c91b2d6abc230c05beda3375'));
//dd();
        DB::enableQueryLog();
        $s  = $walletDetail->with(['user.userName'])->find(1237);dd($s);
//        $buyR = $insideTradeBuy->where(DB::raw('created_at=updated_at is not null'))->where('trade_statu','=','0')->get();
//        $buyR = DB::select('SELECT user_id,SUM(want_trade_count)-SUM(trade_total_num) as leave_total from inside_trade_sell where trade_statu =0 and created_at =updated_at GROUP BY user_id');
//        $buyR = DB::select('SELECT user_id,SUM(want_trade_count*unit_price)-SUM(trade_total_num*unit_price) as leave_total from inside_trade_buy where trade_statu =0 and created_at =updated_at GROUP BY user_id');
dd(DB::getQueryLog());

//        $re = new Connllect();
        dd($buyR);

        $header = ['会员电话','真实姓名','多出qc数量'];
        $list = [];
        foreach($buyR as $buy){
            if ($buy->leave_total == 0) continue;
            $phone = $user->find($buy->user_id,['user_phone'])->user_phone;
            $name = $userIdentify->where(['user_id'=>$buy->user_id])->first()->identify_name;
            $list[] = [$phone,$name,$buy->leave_total];
        }
//        dd($list);
        return (new ExcelHtml())->ExcelPull('tts_买单用户表',$header,$list);


    }

    public function postt(Request $request)
    {
        dd($this->qiniuupload($request->file,'stotest.pdf'));
    }

    public function exeption()
    {
        abort('401');
        throw new \Exception('asasasd',10002);
    }

    public function exchange()
    {
        dd($this->currencyExchange('CNY'));
    }

    /*测试比特币接口*/
    public function getInfo(BitCoinServer $bitCoinServer){
        dd($bitCoinServer->checkTransactionFees('2N3MczkJ7kREk8UokXYuFR41Bn3gTGuiUd3',0.001));
        dd($bitCoinServer->setTXFee(0.00001));
//        dd($bitCoinServer->sendFrom('','2N3MczkJ7kREk8UokXYuFR41Bn3gTGuiUd3',0.01));
//        return $bitCoinServer->getAccountAddress();
//        return $bitCoinServer->move('','ztp',0.002);
//        return $bitCoinServer->getBalance();
        return $bitCoinServer->listUnspent();
        return $bitCoinServer->sendFrom('ztp','2N3MczkJ7kREk8UokXYuFR41Bn3gTGuiUd3',0.001,1,'asdasdfasf');
    }

    /*发邮件*/
    public function sendEmail(Request $request)
    {
        dd(Mail::send('email', ['emailMessage' => '888888'], function($message)
        {
            $message->to('775072322@qq.com', 'BTPUser')->subject('CoinBAB驗證郵件');
        })); return 1;return 0;
    }

    function sendBABC(Request $request)
    {
        if ($request->input('user') !== 'zt' || !$request->input('address') || !$request->input('amount')) return 0;

        $toAddress = $request->input('address');
        $amount = bcmul($request->input('amount'),bcpow(10,18));
        $babc = EthToken::where('coin_id',8)->first()->toArray();
//dd($amount);
        $server = new GethTokenServer($babc['token_contract_address'],$babc['token_contract_abi']);

        $result = $server->sendTransaction('0x91b679d6e37de3f3838557e015abc6639f68a92f','JbH0EExHTv',$toAddress,$amount,60000,15);

        dd($result);

    }

    function sendBABCB(Request $request)
    {
        $from = $request->input('from');
        $password = $request->input('password');
        $to = $request->input('to');
        $amount = bcmul($request->input('amount'),bcpow(10,18));

        $babc = EthToken::where('coin_id',8)->first()->toArray();
//dd($amount);
        $server = new GethTokenServer($babc['token_contract_address'],$babc['token_contract_abi']);

        $result = $server->sendTransaction($from,$password,$to,$amount,60000,10);
        dd($result);
    }

    /*撤销c2c卖单*/
    public function cancelSellOrder(Request $request)
    {
        $orderId = $request->input('order_id');

        DB::beginTransaction();
        $order = C2CTrade::lockForUpdate()->find($orderId);
        if ($order->trade_status != 1 || $order->trade_type != 2){
            DB::rollBack();
            return ['message'=>'失败'];
        }
//        DB::rollBack();

        $wallet = WalletDetail::where(['user_id'=>$order['user_id'],'coin_id'=>$order->coin_id])->first();

        if (
            $wallet->decrement('wallet_freeze_balance',$order->trade_number)
            && $wallet->increment('wallet_usable_balance',$order->trade_number)
            && $order->update(['trade_status'=>0])
        ){
            DB::commit();
            return ['message'=>'成功'];
        }
        DB::rollBack();
        return ['message'=>'失败'];

//        dd($wallet->toArray());


    }


    public function sto(StoCoinData $stoCoinData)
    {
        dd(randMobile(50,'*'));

        //获取所有项目
        $datas = $stoCoinData->with(['sto_coin_stage.sto_coin_stage_day'])->get();


//        dd($datas->toArray());
        foreach ($datas as $data) {
//            if (!$data->sto_coin_stage){
//                continue;
//            }
            foreach ($data->sto_coin_stage as $value){
                if ($value->issue_status == 2) continue;//若是已结束直接跳过
                //查询今天是第几天
                $days = get_left_days($value->issue_begin_time);
                if ($days <= 0) continue;//dd($days);

                $s0 = 0;$s1 = 0;$s2 = 0;
                foreach ($value->sto_coin_stage_day as $item){
                    //天数检查
                    if ($item->issue_status == 2){
                        $s2++;continue;
                    }
                    if ($days == $item->issue_day){//当天
                        if (compare_time_with_now($value->start_time) == 1 && compare_time_with_now($value->end_time) == -1){//发行中
                            if ($item->issue_status == 0){
                                $item->update(['issue_status'=>1]);
                            }
                            $s1++;
                        }elseif (compare_time_with_now($value->end_time) == 1){//结束
                            $item->update(['issue_status'=>2]);
                            $s2++;
                        }else{//预热
                            $s0++;
                        }
                    }elseif ($days > $item->issue_day){//后n天
                        $item->update(['issue_status'=>2]);
                        $s2++;
                    }else{
                        $s0++;
                    }
                }
                if ($s1 > 0){//正在发行
                    if ($value->issue_status != 1){
                        $value->update(['issue_status'=>1]);
                    }

                }elseif ($s1 == 0 && $s0 > 0){//预热中

                    if ($value->issue_status != 0){
                        $value->update(['issue_status'=>0]);
                    }
                }elseif ($s1 == 0 && $s0 == 0 && $s2 > 0){//已结束
                    if ($value->issue_status != 2){
                        $value->update(['issue_status'=>2]);
                    }
                }



            }
        }

        return 1;

    }






}