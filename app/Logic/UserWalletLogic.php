<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/7
 * Time: 14:31
 */

namespace App\Logic;

use App\Handlers\ExchangeHelper;
use App\Http\Response\ApiResponse;
use App\Model\CoinType;
use App\Model\EthToken;
use App\Model\InsideTradeOrder;
use App\Model\kgModel\UserWallet;
use App\Model\OrePoolTransferRecord;
use App\Server\ChatServer\ChatServer;
use App\Server\CoinServer;
use App\Server\HuoBiServer\LibServer\LibServer;
use App\Server\HuoBiServer\Server\HuobiServer;
use App\Server\PlcServer\PlcServer;
use App\Server\UserServers\Dao\WalletDetailDao;
use App\Server\UserServers\Dao\UserDao;
use App\Server\UserServers\Dao\CoinTypeDao;
use App\Model\Admin\coinTransaction;
use App\Traits\RedisTool;
use App\Traits\Tools;
use App\Model\C2CSetting;
use function foo\func;
use Illuminate\Support\Facades\DB;

class UserWalletLogic
{
    use ApiResponse,Tools,RedisTool;

    private $coinTypeDao;
    private $userDao;
    private $walletDetailDao;
    private $coinTransaction;
    private $coinServer;

    private $huoBiServer;

    protected $chatServer,$plcServer;

    public function __construct(CoinTypeDao $coinTypeDao, WalletDetailDao $walletDetailDao, UserDao $userDao, coinTransaction $coinTransaction, CoinServer $coinServer, ChatServer $chatServer,PlcServer $plcServer)
    {
        $this->coinTypeDao = $coinTypeDao;
        $this->walletDetailDao = $walletDetailDao;
        $this->userDao = $userDao;
        $this->coinTransaction = $coinTransaction;
        $this->coinServer = $coinServer;
        $this->chatServer = $chatServer;
        $this->plcServer = $plcServer;
    }

    //返回钱包首页数据
    public function index($userId)
    {
        //1.货币的列表
        $walletList = $this->walletDetailDao->getUserWallet($userId);//dd($walletList);

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
        $usdtRate = DB::table('coin_exchange_rate')->where(['virtual_coin_id'=>5])->first()->rate;
        foreach ($walletList as $wallet){
            switch ($wallet['coin_name']['coin_name']){
//                case 'ICIC':
//                    $exchangeCny = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
//                    $totalCny = bcadd($exchangeCny,$totalCny,2);
//                    break;
//                case 'XYB':
//                    $exchangeCny = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
//                    $totalCny = bcadd($exchangeCny,$totalCny,2);
//                    break;
                case 'USDT':

                    $exchangeCny = bcmul($usdtRate,bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
                    $totalCny = bcadd($exchangeCny,$totalCny,2);
                    break;
                default:
                    if ((new EthToken())->getRecordByCoinId($wallet['coin_id']) || $wallet['coin_name']['coin_name']=='QC'){
                        $exchangeCny = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
                        $totalCny = bcadd($exchangeCny,$totalCny,2);
                    }else {
//                        if (env('APP_V') == 'test'){
                            $exchangeCny = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
                            $totalCny = bcadd($exchangeCny,$totalCny,2);
//                        }else{
//                            $exchangeCny = bcmul($this->huoBiServer->getOneMerged(strtolower($wallet['coin_name']['coin_name'] . 'usdt'))['CNY_price'], bcadd(bcadd($wallet['wallet_usable_balance'], $wallet['wallet_withdraw_balance'], 8), $wallet['wallet_freeze_balance'], 8), 6);
////                    $exchangeCny = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
//                            $totalCny = bcadd($exchangeCny, $totalCny, 2);
//                        }
                    }
                    break;
            }
        }

        return [bcdiv($totalCny,$usdtRate,2),$totalCny];
    }

    /*获取指定账户的余额详情*/
    public function getWalletDetail($userId,$walletId)
    {//return api_response()->zidingyi('暂未开放');
        if (! ($result = $this->walletDetailDao->getRecordById($walletId,$userId)))
            return $this->parameterError();

        $result['wallet_total_balance'] = bcadd($result['wallet_usable_balance'],$result['wallet_withdraw_balance'],8);
        //$result['wallet_total_balance'] = (string)number_format(($result['wallet_usable_balance'] + $result['wallet_freeze_balance']),8,'.','');
        return $this->successWithData(['walletDetail'=>$result]);
    }

    /*充值信息接口*/
    public function getRechargeMsg($userId,$walletId)
    {

        $data = $this->walletDetailDao->getRechargeMsg($userId,$walletId);
        $data['transfer_msg'] = '1.资金划转手续费为0.7%';
        if ($data['coin_fees']['recharge_on_off_status'])
            return $this->successWithData($data);

        return $this->responseByENCode('STATUS_CODE_CANNOT_HANDLE',$data['coin_name']['coin_name'].'充值暂未开放');
    }

    /*提币信息接口*/
    public function getWithdrawMsg($userId,$walletId)
    {
        $data = $this->walletDetailDao->getWithdrawMsg($userId,$walletId);
        $data['wallet_usable_balance'] = $data['wallet_withdraw_balance'];
        $data['transfer_msg'] = '1.资金划转手续费为0.7%';
        if ($data['coin_fees']['withdraw_on_off_status'])
            return $this->successWithData($data);

        return $this->responseByENCode('STATUS_CODE_CANNOT_HANDLE',$data['coin_name']['coin_name'].'提币暂未开放');
    }

    /*
     * 用户发起提币
     * */
    public function withdrawCoin($data)
    {
        $result = $this->walletDetailDao->getRecordById($data['wallet_id'],$data['user_id']);
        if (! $result) return $this->parameterError();//检查账户和userid对不对的上

        if (strcasecmp($result['wallet_address'],$data['to_address']) == 0)
            return $this->responseByENCode('STATUS_CODE_CANNOT_TRANSFER_SELF','不能给自己转账');

        if (!$result['coin_fees']['withdraw_on_off_status'])
            return $this->responseByENCode('STATUS_CODE_CANNOT_HANDLE',$result['coin_name']['coin_name'].'提币暂不开放');

//        $hjkgWallet = UserWallet::where(['wallet_address' => $data['to_address']])->first();
//        if ($hjkgWallet) return api_response()->zidingyi('暂不支持提币到互链');

        if (
            !is_numeric($data['amount'])
            || (bccomp($result['coin_fees']['withdraw_min'],$data['amount'],8)==1)
            || (bccomp($data['amount'],$result['coin_fees']['withdraw_max'],8)==1)
        ){
            return $this->responseByENCode('STATUS_CODE_AMOUNT_ERROR','请输入'.$result['coin_fees']['withdraw_min'].'到'.$result['coin_fees']['withdraw_max'].'之间的数量');
        }

        //检查数据库的可用余额是否大于提取的金额amount
        if (bccomp($data['amount'],$result['wallet_withdraw_balance'],8) == 1)
            return $this->responseByENCode('STATUS_CODE_NOTSUFFICIENT_FUNDS','余额不足');

        //plc检查
//        if ($result['coin_name']['coin_name'] == 'PALT' && !$this->plcServer->checkIsAddress($data['to_address'])){
//            return api_response()->zidingyi('只能提币到PLC');
//        }
        if ($result['coin_name']['coin_name'] == 'PALT' && !$this->walletDetailDao->getRecordByAddress($data['to_address'])){
            return api_response()->zidingyi('暂不支持提币到外部地址');
        }
//dd(1);

        switch ($result['coin_name']['coin_name']){
            case 'BTC':
                $result = $this->coinServer->withdrawCoin($result['wallet_account'],$result['wallet_id'],$data['to_address'],(string)$data['amount'],$result['coin_id'],'BTC',$data['user_id']);
                break;
            case 'ETH':
                $result = $this->coinServer->withdrawCoin('',$result['wallet_id'],$data['to_address'],(string)$data['amount'],$result['coin_id'],'ETH',$data['user_id']);
                break;
            case 'USDT':
                $result = 0;
                break;
            default:
                //$token = (new EthToken())->getRecordByCoinId($result['coin_id']);dd($token);
                $result = $this->coinServer->withdrawCoin('',$result['wallet_id'],$data['to_address'],(string)$data['amount'],$result['coin_id'],'TOKEN',$data['user_id']);
        }

        return $this->withdrawResponse($result);
    }

    private function withdrawResponse($code)
    {
        switch ($code){
            case 0:
                return $this->responseByENCode('STATUS_CODE_UNKNOWN_ERROR','未知的错误,请联系客服');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_NOTSUFFICIENT_FUNDS','余额不足');
                break;
            case 2:
                return $this->success();
                break;
            case -1:
                return $this->responseByENCode('STATUS_CODE_ADDRESS_ERROR','地址不合法');
                break;
            default:
        }
    }


    /*转账 可提转交易*/
    public function transferAccounts(int $userId,int $walletId,$amount)
    {
        switch ($this->walletDetailDao->transferAccount($walletId,$userId,$amount)){
            case 0:
                return $this->responseByENCode('STATUS_CODE_NOTSUFFICIENT_FUNDS','余额不足');
                break;
            case 1:
                return $this->success();
                break;
            default:
                return $this->error();
                break;
        }
    }

    //内部用户划转usdt
    public function transferUSDT(int $userId,int $walletId,$amount)
    {
        $wallet = $this->walletDetailDao->find($walletId);

        if ($wallet->user_id != $userId || $wallet->coin->coin_name != 'USDT'){
            return $this->error();
        }


        switch ($this->walletDetailDao->transferUSDT($wallet,$amount)){
            case 0:
                return $this->responseByENCode('STATUS_CODE_NOTSUFFICIENT_FUNDS','余额不足');
                break;
            case 1:
                return $this->success();
                break;
            default:
                return $this->error();
                break;
        }


    }

    /*查询c2c基币的余额*/
    public function getUSDTBalance(int $userId)
    {
        $c2cSetting = (new C2CSetting())->getOneRecord();
        $basecoin = $this->coinTypeDao->find($c2cSetting['coin_id']);
        $balance = $this->walletDetailDao->where(['coin_id'=>$basecoin->coin_id,'user_id'=>$userId])->select('wallet_usable_balance','wallet_withdraw_balance')->first();
        if (!$balance){
            $balance = ['wallet_usable_balance'=>0,'wallet_withdraw_balance'=>0];
        }
        return $this->successWithData(['balance'=>$balance]);
    }

    //查询指定币种余额


    //钱包流水
    public function getCoinFlow(int $userId,int $coinId)
    {
        $records = $this->walletDetailDao
            ->with([
//                'coinTradeOrder'=>function($q) use($coinId) {
//                    $q->where(['coin_id'=>$coinId,'is_usable'=>1]);//提币记录
//                },
//                'c2cTrade'=>function($q) use($coinId) {
//                    $q->where(['coin_id'=>$coinId,'is_usable'=>1]);//c2c用户记录
//                },
//                'c2cTradeOrder'=>function($q) use($coinId) {
//                    $q->with('tradeMsg')->where(['coin_id'=>$coinId,'is_usable'=>1])->where('order_status','!=',4)->where('order_status','!=',0);//c2c商家记录
//                },
//                'insideTradeBuy'=>function($q) use($coinId) {
//                    $q->where(['is_usable'=>1,'base_coin_id'=>$coinId])->where('trade_statu','!=',2);//买单扣基准货币
//                },
//                'insideTradeSell'=>function($q) use($coinId) {
//                    $q->Where(['is_usable'=>1,'exchange_coin_id' => $coinId])->where('trade_statu','!=',2);//卖单扣兑换货币
//                },
//                'insideTradeOrderBuy'=>function($q) use($coinId) {
//                    $q->where('base_coin_id',$coinId)->orWhere('exchange_coin_id',$coinId);//场内成交记录买方
//                },
//                'insideTradeOrderSell'=>function($q) use($coinId) {
//                    $q->where('base_coin_id',$coinId)->orWhere('exchange_coin_id',$coinId);//场内成交记录卖方
//                },
//                'adminWalletFlow'=>function($q) use($coinId) {
//                    $q->where('coin_id',$coinId);//后台充扣记录
//                },
//                'userInvestmentRecord'=>function($q) use($coinId) {
//                    $q->where('coin_id',$coinId);//理财记录
//                },
//                'walletTransferRecords'=>function($q) use($coinId) {
//                    $q->where('coin_id',$coinId);//资金划转
//                },
                'flow'
            ])
            ->where(['user_id'=>$userId,'coin_id'=>$coinId])
            ->select(['user_id','wallet_id'])
            ->first()->toArray();//dd($records);
//        dd($records);
//        dd($records);
        return $this->successWithData(['records'=>$this->flowDataHandle($records,$coinId)]);

    }


    private function flowDataHandle($records,$coinId)
    {

        unset($records['user_id']);
        unset($records['wallet_id']);
//        foreach ($records['coin_trade_order'] as $key=>$value){
//            $records['coin_trade_order'][$key]['title'] = $value['order_type']==1?'提币':'转入';
//            $records['coin_trade_order'][$key]['symbol'] = $value['order_type']==1?2:1;//1+ 2-
//            $records['coin_trade_order'][$key]['flow_number'] = (string)($value['order_trade_money']);
//            $records['coin_trade_order'][$key]['des'] = $value['order_check_status'] == 0?'审核中':($value['order_check_status'] == 1?'已完成':'审核未通过');
//            $records['coin_trade_order'][$key]['sort'] = strtotime($value['created_at']);
////            $records['coin_trade_order'][$key]['flow_status'] = $value['order_status'] == 1?'已到账':'未到账';
//            $records['coin_trade_order'][$key]['fee'] = (string)($value['order_trade_fee']);
//            $records['coin_trade_order'][$key]['wallet_type'] = $value['order_type']==1?2:1;
//        }
//        foreach ($records['c2c_trade'] as $key=>$value){
//            if ($value['trade_type'] == 1 && $value['trade_status']!=3){unset($records['c2c_trade'][$key]);continue;}
//            $records['c2c_trade'][$key]['title'] = $value['trade_type']==1?'C2C买入':'C2C卖出';
//            $records['c2c_trade'][$key]['symbol'] = $value['trade_type'];//1+ 2-
//            $records['c2c_trade'][$key]['flow_number'] = (string)($value['trade_number']);
//            $records['c2c_trade'][$key]['des'] = $value['trade_status'] == 1? '待商家接单':($value['trade_status']==2?'交易中':($value['trade_status']==3?'交易完成':'已撤销'));
//            $records['c2c_trade'][$key]['sort'] = $value['trade_type']==1 ?strtotime($value['updated_at']) : strtotime($value['created_at']);
//            $records['c2c_trade'][$key]['created_at'] = $value['trade_type']==1 ?$value['updated_at'] : $value['created_at'];
////            $records['c2c_trade'][$key]['flow_status'] = $value['trade_status'] == 3?'已到账':'未到账';
//            $records['c2c_trade'][$key]['fee'] = 0;
//            $records['c2c_trade'][$key]['wallet_type'] = $value['trade_type'];
//
//        }
//        foreach ($records['c2c_trade_order'] as $key=>$value){
//            if ($value['trade_msg']['trade_type']!=1 && $value['order_status']!=3){ unset($records['c2c_trade_order'][$key]);continue;}
//            $records['c2c_trade_order'][$key]['title'] = $value['trade_msg']['trade_type']==1?'商家卖出':'商家买入';
//            $records['c2c_trade_order'][$key]['symbol'] = $value['trade_msg']['trade_type']==1?2:1;//1+ 2-
//            $records['c2c_trade_order'][$key]['flow_number'] = (string)($value['trade_msg']['trade_number']);
//            $records['c2c_trade_order'][$key]['des'] = $value['order_status'] == 1? '交易中':($value['order_status']==2?'审核中':($value['order_status']==3?'交易完成':'超时撤销'));
//            $records['c2c_trade_order'][$key]['sort'] = $value['trade_msg']['trade_type']==1 ? strtotime($value['created_at']):strtotime($value['updated_at']);
//            $records['c2c_trade_order'][$key]['created_at'] = $value['trade_msg']['trade_type']==1 ? $value['created_at']:$value['updated_at'];
////            $records['c2c_trade_order'][$key]['flow_status'] = $value['order_status'] == 3?'已到账':'未到账';
//            $records['c2c_trade_order'][$key]['fee'] = 0;
//            $records['c2c_trade_order'][$key]['wallet_type'] = 2;
//        }
//        foreach ($records['inside_trade_buy'] as $key=>$value){
//            $records['inside_trade_buy'][$key]['title'] = '场内交易';
//            $records['inside_trade_buy'][$key]['symbol'] = $value['base_coin_id']==$coinId?2:1;//1+ 2-
//            $records['inside_trade_buy'][$key]['flow_number'] = bcmul($value['want_trade_count'],$value['unit_price'],12);
//            $records['inside_trade_buy'][$key]['des'] = $value['trade_statu'] == 1? '交易中':($value['trade_statu']==2?'交易完成':'挂单');
//            $records['inside_trade_buy'][$key]['sort'] = strtotime($value['created_at']);
////            $records['inside_trade_buy'][$key]['flow_status'] = $value['order_status'] == 3?'已到账':'未到账';
//            $records['inside_trade_buy'][$key]['fee'] = 0;
//            $records['inside_trade_buy'][$key]['wallet_type'] = $value['base_coin_id']==$coinId?1:2;
//
//            if ($value['trade_statu'] == 0){
//                $newData2 = $records['inside_trade_buy'][$key];
//                $newData2['created_at'] = $value['updated_at'];
//                $newData2['sort'] = strtotime($value['updated_at']);
//                $newData2['wallet_type'] = 1;
//                $newData2['symbol'] = 1;
//                $newData2['des'] = '撤单';
//                $newData2['flow_number'] = $records['inside_trade_buy'][$key]['flow_number'] - InsideTradeOrder::where(['buy_order_number'=>$value['order_number']])->first([\DB::raw('SUM(unit_price * trade_num) as total')])->total;
//                $records['inside_trade_buy'][] = $newData2;
//            }
//        }
//        foreach ($records['inside_trade_sell'] as $key=>$value){
//            $records['inside_trade_sell'][$key]['title'] = '场内交易';
//            $records['inside_trade_sell'][$key]['symbol'] = $value['base_coin_id']==$coinId?1:2;//1+ 2-
//            $records['inside_trade_sell'][$key]['flow_number'] = (string)($value['want_trade_count']);
//            $records['inside_trade_sell'][$key]['des'] = $value['trade_statu'] == 1? '交易中':($value['trade_statu']==2?'交易完成':'挂单');
//            $records['inside_trade_sell'][$key]['sort'] = strtotime($value['created_at']);
////            $records['inside_trade_sell'][$key]['flow_status'] = $value['trade_statu'] == 3?'已到账':'交易中';
//            $records['inside_trade_sell'][$key]['fee'] = 0;
//            $records['inside_trade_sell'][$key]['wallet_type'] = $value['base_coin_id']==$coinId?2:1;
//            if ($value['trade_statu'] == 0){
//                $newData3 = $records['inside_trade_sell'][$key];
//                $newData3['created_at'] = $value['updated_at'];
//                $newData3['sort'] = strtotime($value['updated_at']);
//                $newData3['wallet_type'] = 1;
//                $newData3['symbol'] = 1;
//                $newData3['des'] = '撤单';
//                $newData3['flow_number'] = $records['inside_trade_sell'][$key]['flow_number'] - InsideTradeOrder::where(['sell_order_number'=>$value['order_number']])->sum('trade_num');
//                $records['inside_trade_sell'][] = $newData3;
//            }
//        }
//        foreach ($records['inside_trade_order_buy'] as $key=>$value){
//            $records['inside_trade_order_buy'][$key]['title'] = '场内交易';
//            $records['inside_trade_order_buy'][$key]['symbol'] = $value['base_coin_id']==$coinId ? 2 : 1;//1+ 2-
//            $records['inside_trade_order_buy'][$key]['flow_number'] = (string)($value['base_coin_id']==$coinId ? bcmul($value['unit_price'],$value['trade_num'],12) :$value['trade_num']);
//            $records['inside_trade_order_buy'][$key]['des'] = '交易完成';
//            $records['inside_trade_order_buy'][$key]['sort'] = strtotime($value['created_at']);
////            $records['c2c_trade_order'][$key]['flow_status'] = $value['order_status'] == 3?'已到账':'未到账';
//            $records['inside_trade_order_buy'][$key]['fee'] = (string)($value['base_coin_id']==$coinId ? 0 :$value['trade_poundage']);
//            $records['inside_trade_order_buy'][$key]['wallet_type'] = $value['base_coin_id']==$coinId ? 1 : 2;;
//        }
//        foreach ($records['inside_trade_order_sell'] as $key=>$value){
//            $records['inside_trade_order_sell'][$key]['title'] = '场内交易';
//            $records['inside_trade_order_sell'][$key]['symbol'] = $value['base_coin_id']==$coinId ? 1:2;//1+ 2-
//            $records['inside_trade_order_sell'][$key]['flow_number'] = (string)($value['exchange_coin_id']==$coinId ? $value['trade_num'] : bcmul($value['unit_price'],$value['trade_num'],12));
//            $records['inside_trade_order_sell'][$key]['des'] = '交易完成';
//            $records['inside_trade_order_sell'][$key]['sort'] = strtotime($value['created_at']);
////            $records['c2c_trade_order'][$key]['flow_status'] = $value['order_status'] == 3?'已到账':'未到账';
//            $records['inside_trade_order_sell'][$key]['fee'] = (string)($value['base_coin_id']==$coinId ? bcmul($value['trade_poundage'],$value['unit_price'],12): 0);
//            $records['inside_trade_order_sell'][$key]['wallet_type'] = $value['base_coin_id']==$coinId ? 2:1;;
//        }
/////////////////////
//        foreach ($records['admin_wallet_flow'] as $key=>$value) {
//            $records['admin_wallet_flow'][$key]['title'] = $value['type'] == 1? '后台充值' : '后台扣除';
//            $records['admin_wallet_flow'][$key]['symbol'] = $value['type'];//1+ 2-
//            $records['admin_wallet_flow'][$key]['flow_number'] = (string)($value['amount']);
//            $records['admin_wallet_flow'][$key]['des'] = '已完成';
//            $records['admin_wallet_flow'][$key]['sort'] = strtotime($value['created_at']);
////            $records['c2c_trade_order'][$key]['flow_status'] = $value['order_status'] == 3?'已到账':'未到账';
//            $records['admin_wallet_flow'][$key]['fee'] = 0;
//            $records['admin_wallet_flow'][$key]['wallet_type'] = $value['wallet_type'];
//        }
///////////////////
//        foreach ($records['user_investment_record'] as $key=>$value) {//理财记录
//            $records['user_investment_record'][$key]['title'] = in_array($value['invest_status'],[1,2]) ?'理财' :'理财提取';
//            $records['user_investment_record'][$key]['symbol'] = in_array($value['invest_status'],[1,2]) ? 2 :1;//1+ 2-
//            $records['user_investment_record'][$key]['flow_number'] = (string)(in_array($value['invest_status'],[1,2,4]) ? $value['invest_money']:(1+$value['rate_of_return_set']/100)*$value['invest_money']);
//            $records['user_investment_record'][$key]['des'] = '已完成';
//            $records['user_investment_record'][$key]['created_at'] =  in_array($value['invest_status'],[1,2]) ? $value['created_at'] :$value['updated_at'];
//            $records['user_investment_record'][$key]['sort'] = in_array($value['invest_status'],[1,2]) ? strtotime($value['created_at']) :strtotime($value['updated_at']);
//            $records['user_investment_record'][$key]['fee'] = 0;
//            $records['user_investment_record'][$key]['wallet_type'] = in_array($value['invest_status'],[1,2]) ? 2 :1;;
//
//            if (in_array($value['invest_status'],[3,4])) {
//                $newData = $records['user_investment_record'][$key];
//                $newData['created_at'] = $value['created_at'];
//                $newData['title'] = '理财';
//                $newData['symbol'] = 2;//1+ 2-
//                $newData['flow_number'] = (string)$value['invest_money'];
//                $newData['des'] = '已完成';
//                $newData['sort'] = strtotime($value['created_at']);
//                $newData['wallet_type'] = 2;//1场内2可提
//                $records['user_investment_record'][] = $newData;
//
//            }
//        }
//////////////////////
//        foreach ($records['wallet_transfer_records'] as $key=>$value){
//            $records['wallet_transfer_records'][$key]['title'] = '资金划转';
//            $records['wallet_transfer_records'][$key]['symbol'] = 1;//1+ 2-
//            $records['wallet_transfer_records'][$key]['flow_number'] = (string)$value['amount'];
//            $records['wallet_transfer_records'][$key]['des'] = '已完成';
//            $records['wallet_transfer_records'][$key]['fee'] = (string)$value['fee'];
//            $records['wallet_transfer_records'][$key]['sort'] = strtotime($value['created_at']) + 1;
//            $records['wallet_transfer_records'][$key]['wallet_type'] = 1;
//
//            $newData1 = $records['wallet_transfer_records'][$key];
//            $newData1['symbol'] = 2;
//            $newData1['fee'] = 0;
//            $newData1['sort'] = strtotime($value['created_at']);
//            $newData1['wallet_type'] = 2;
//            $records['wallet_transfer_records'][] = $newData1;
//        }
///
//dd($records);
        if ($records){
            $newRecords = [];
            foreach ($records as $record){
                $newRecords = array_merge($newRecords,$record);
            }
        }

//            $records = array_merge($records['coin_trade_order'],$records['c2c_trade'],$records['c2c_trade_order'],$records['inside_trade_order_buy'],$records['inside_trade_order_sell'],$records['admin_wallet_flow'],$records['inside_trade_buy'],$records['inside_trade_sell']);

        return $this->arraySort($newRecords,'id');
    }




    //获取矿池资产
    public function getOrePoolWallet()
    {
        $user = current_user();

        $coin = (new CoinType())->with(['coinIcon'])->where(['is_usable'=>1,'coin_name'=>env('COIN_SYMBOL')])->first();

        $wallet = $this->walletDetailDao->getOneWallet($user->user_id,$coin->coin_id);

        $exchangeToCny = bcmul((new ExchangeHelper())->getCoinPrice($coin->coin_name), $wallet->ore_pool_balance,2);

        return api_response()->successWithData(['wallet' => $wallet,'coin' => $coin,'exchange_to_cny' => $exchangeToCny]);

    }

    //获取矿池资产详情
    public function getOrePoolWalletDetail($walletId)
    {
        $wallet = $this->walletDetailDao->find($walletId);

        $flow = (new OrePoolTransferRecord())->getWalletFlow($walletId);


        return api_response()->successWithData(['wallet' => $wallet,'flow' => $flow]);


    }

    //矿池icic划转(场内->矿池或者可提->矿池)
    public function orePoolTransfer($walletId,$amount,$type)
    {

        $wallet = $this->walletDetailDao->with(['coin'])->find($walletId);

        if ($wallet->coin->coin_name != env('COIN_SYMBOL')) return api_response()->zidingyi('只能划转ICIC');
        if ($type == 1){//场内
            if ($amount > $wallet->wallet_usable_balance) return api_response()->zidingyi('余额不足');

        }else{//可提

            if ($amount > $wallet->wallet_withdraw_balance) return api_response()->zidingyi('余额不足');
        }
        $re = $this->walletDetailDao->ore_pool_transfer($wallet,$amount,$type);
        if ($re){
            return api_response()->success();
        }
        return api_response()->error();


    }






}