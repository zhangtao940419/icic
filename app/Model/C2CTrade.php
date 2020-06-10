<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 16:27
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\CoinType;
use App\Model\WorldCurrency;
use Illuminate\Support\Facades\DB;
use App\Model\WalletDetail;
use App\Model\C2CTradeOrder;
use App\Model\BankCardVerify;
use App\Model\User;
use App\Model\BankList;
use App\Traits\RedisTool;
use App\Jobs\C2CAutoCancelOrder;
use App\Model\UserIdentify;

class C2CTrade extends Model
{
    use RedisTool;

    protected $table = 'c2c_trade';

    protected $primaryKey = 'trade_id';

    private $buyPrice = 6.5;

    private $sellPrice = 6.4;

    protected $fillable = ['trade_status','check_status'];


    public function getRecordByUserId(int $userId)
    {
        return $this->where(['user_id'=>$userId,'is_usable'=>1])->first();
    }

    /*订单入库*/
    public function saveOneRecord($userId,$tradeNum,$tradeType,$price,$coinId,$needCheck = 0,$low_num = 500)
    {
        $c2CSetting = (new C2CSetting())->getOneRecord();
        DB::beginTransaction();

//        $coin = (new CoinType())->getRecordByCoinName('USDT');
//        $usCoinId = $coin ? $coin->coin_id :0;
        $usCoinId = $coinId;

        $currency = WorldCurrency::select('currency_id')->where(['currency_code'=>'CNY'])->first();
        $currencyId = $currency ? $currency->currency_id : 0;

        $bankCard = (new BankCardVerify())->getOneBankCard($userId);//yinhang

        if (!$usCoinId || !$currencyId) return 2;

        $this->trade_order = 'c2c'.time() . $userId . $tradeType . mt_rand(100,999);
        $this->user_id = $userId;
        $this->trade_number = $tradeNum;
        $this->trade_type = $tradeType;
        $this->currency_id = $currencyId;
        $this->coin_id = $usCoinId;
        $this->trade_price = $price;
        $this->bank_id = $bankCard->bank_id;
        $this->bank_card_no = $bankCard->verify_card_no;
        if ($needCheck && $tradeType == 2 && $tradeNum > $low_num) $this->check_status = 0;//卖单审核判断

        if ($tradeType == 1 && $c2CSetting['buy_order_check_switch'] && $c2CSetting['buy_order_need_check_num'] < $tradeNum){
            $this->check_status = 0;//买单审核判断
        }

        $result1 = $this->save();
        if (!$result1) return 2;

        if ($tradeType == 2){
            $wallet = WalletDetail::select('wallet_usable_balance','wallet_id','wallet_withdraw_balance','user_id','coin_id')->where(['user_id'=>$userId,'coin_id'=>$usCoinId,'is_usable'=>1])->lockForUpdate()->first();
            (new WalletFlow())->insertOne($wallet['user_id'],$wallet['wallet_id'],$wallet['coin_id'],$tradeNum,4,2,'c2c卖出',2);
            if (
                !$wallet
                || (bccomp($tradeNum,$wallet->wallet_withdraw_balance,8) == 1)
            ){
                DB::rollBack();
                return 0;
            }

            if ($wallet->decrement('wallet_withdraw_balance',$tradeNum) && $wallet->increment('wallet_freeze_balance',$tradeNum)){
                DB::commit();
                return 1;
            }
            DB::rollBack();return 2;
        }

        DB::commit();
        return 1;


    }


    /*买单/卖单列表*/
    public function getTradeList($tradeType)
    {

        $records = $this->with('userMsg')->where(['trade_type'=>$tradeType,'trade_status'=>1,'check_status'=>1,'is_usable'=>1])->get()->toArray();
        return $records;
//        dd($records);

    }

    /*接单*/
    public function receiptTrade(int $tradeId,int $userId,$c2CSetting,$bankId=0,$cardNo='',$name='')
    {
        DB::beginTransaction();
        $trade = $this->lockForUpdate()->find($tradeId);
        if ($trade->user_id == $userId) return 3;
        if (!$trade || ($trade->trade_status != 1)) return 0;//订单不可用

        if ($trade->trade_type == 1){
            $wallet = WalletDetail::select('wallet_withdraw_balance','wallet_id','user_id','coin_id')->where(['user_id'=>$userId,'coin_id'=>$trade->coin_id,'is_usable'=>1])->lockForUpdate()->first();
            if (
                !$wallet
                || (bccomp($trade->trade_number,$wallet->wallet_withdraw_balance) == 1)
            ){
                DB::rollBack();
                return 1;
            }

            if (
                !$wallet->decrement('wallet_withdraw_balance',$trade->trade_number)
                || !$wallet->increment('wallet_freeze_balance',$trade->trade_number)
            ){
                DB::rollBack();return 2;
            }
            (new WalletFlow())->insertOne($wallet['user_id'],$wallet['wallet_id'],$wallet['coin_id'],$trade->trade_number,6,2,'c2c卖出',2);

        }

        $bankCard = (new BankCardVerify())->getOneBankCard($userId);//商家银行卡
        $bankId = ($bankId<=168 && $bankId>0) ? $bankId:$bankCard->bank_id;
        $cardNo = $cardNo ? $cardNo:$bankCard->verify_card_no;

        if (
            $trade->update(['trade_status'=>2])
            && ($order = (new C2CTradeOrder())->saveOneRecord(
                'c2co' . date('Ymd',time()) . $tradeId . $userId . rand(100,999),
                rand(1,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9),
                $tradeId,
                $userId,
                $bankId,
                $cardNo,
                $name,
                $trade->coin_id
            ))
        ){

            if ($trade->trade_type == 1){
                $this->stringSetex('C2C_BUSINESS_BUY_TIMELIMIT_'.$userId,$c2CSetting['business_buy_order_time_space']*60,time());
                C2CAutoCancelOrder::dispatch($order['order_id'])->delay(now()->addMinutes($c2CSetting['buy_order_auto_handle']*60));;
            }
            DB::commit();
            //$bankCard =(new BankCardVerify())->getOneBankCardById($trade->bank_card_id);//获取挂单用户的银行卡信息
            return $order;
        }

        DB::rollBack();return 2;

    }


    /*普通用户 订单列表*/
    public function getUOrderList(int $userId,int $handleStatus)
    {
        switch ($handleStatus){
            case 1:
                return $this->with(['base_coin'])->where(['user_id'=>$userId,'is_usable'=>1])->whereIn('trade_status',[1,2])->get()->toArray();
                break;
            case 2:
                return $this->with(['base_coin'])->where(['user_id'=>$userId,'is_usable'=>1])->whereIn('trade_status',[0,3])->orderByDesc('updated_at')->get()->toArray();
                break;
        }
    }

    /*普通用户 订单详情*/
    public function getUOrderDetail(int $userId,int $tradeId)
    {
        $order = $this->with('order')->where(['trade_id'=>$tradeId,'user_id'=>$userId,'is_usable'=>1])->first()->toArray();

        if ($order['order']){
//            $business = (new User())->getUserById($order['order']['business_user_id'],['user_name'])->toArray();

            if ($order['order']['card_name']){
                $order['order']['business'] = ['user_name'=>$order['order']['card_name']];
            }else{
                //$business = UserIdentify::where(['user_id'=>$order['order']['business_user_id'],'is_usable'=>1])->first();
                $bBankCard = (new BankCardVerify())->getRecordByUserId($order['order']['business_user_id']);
                $order['order']['business'] = ['user_name'=>$bBankCard->verify_name];
            }

            if ($order['trade_status'] == 3 || $order['trade_status'] == 0 || $order['trade_status'] == 4){
                $order['order']['bank'] = [];
                $order['order']['order_pay_number'] = '0';
                $order['order']['bank_card_no'] = '***********************';
                $order['order']['business']['user_name'] = '***';
                $order['order']['bank']['bank_cn_name'] = '******';
            }else{
                $bank = (new BankList())->getRecordById($order['order']['bank_id']);
                $order['order']['bank'] = $bank;
            }

        }
//        dd($order);

        return ['order'=>$order];

    }
    //卖单审核1tongguo2jujue
    public function checkSellTrade($tradeId,$status){
        try{
            DB::beginTransaction();
            $trade = $this->find($tradeId);

            if ($trade->check_status != 0 || $trade->trade_type != 2){
                DB::rollBack();return 0;
            }

            if ($status == 1){//tongg
                if ($trade->update(['check_status'=>1])){
                    DB::commit();return 1;
                }
                DB::rollBack();return 0;
            }

            $userWallet = WalletDetail::where(['user_id'=>$trade->user_id,'coin_id'=>$trade->coin_id])->first();
            if (
                $trade->update(['check_status'=>2,'trade_status'=>0])
                && $userWallet->decrement('wallet_freeze_balance',$trade->trade_number)
                && $userWallet->increment('wallet_withdraw_balance',$trade->trade_number)
            ){
                DB::commit();return 1;
            }
            DB::rollBack();return 0;
        }catch (\Exception $exception){
            DB::rollBack();return 0;
        }
    }

    public function checkBuyTrade($trade,$status)
    {
        if ($status == 1){//tongg
            if ($trade->update(['check_status'=>1])){
                return 1;
            }
            return 0;
        }else{
            $re = $trade->update(['check_status'=>2,'trade_status'=>0]);
            if ($re){
                return 1;
            }
            return 0;
        }

    }



    /*模型关联--用户头像*/
    public function userMsg()
    {
        return $this->hasOne('App\Model\User','user_id','user_id')->select('user_id','user_headimg','user_name','user_phone');
    }

    /*模型关联--order*/
    public function order()
    {
        return $this->hasOne('App\Model\C2CTradeOrder','trade_id','trade_id')->where(['is_usable'=>1])->where('order_status','<>',0);
    }

    //关联coin表
    public function coin()
    {
        return $this->hasMany('App\Model\CoinType', 'coin_id', 'coin_id')->where(['is_usable'=>1])->select('coin_id', 'coin_name');
    }

    public function base_coin()
    {
        return $this->belongsTo('App\Model\CoinType', 'coin_id', 'coin_id')->where(['is_usable'=>1])->select('coin_id', 'coin_name');
    }

    public function userIdentify()
    {
        return $this->hasOne(UserIdentify::class,'user_id','user_id');
    }

    //关联货币类型表
    public function currency()
    {
        return $this->hasOne('App\Model\WorldCurrency', 'currency_id', 'currency_id')->select('currency_code', 'currency_cn_full_name');
    }


    public function getTradeStatus()
    {
        return
            [
                0 => '<i style="color: 	#EE0000">撤销</i>',
                1 => '<i style="color: 	#5CACEE">待商家接单</i>',
                2 => '<i style="color: 	#F08080">已被商家接单, 交易中</i>',
                3 => '<i style="color: 	green">交易完成</i>',
            ];
    }


    //获取用户总买入
    public function getUserTotalBuy()
    {
        return $this->where(['user_id'=>$this->user_id,'trade_type'=>1,'trade_status'=>3])->sum('trade_number');

    }
    //获取用户总卖出
    public function getUserTotalSell()
    {
        return $this->where(['user_id'=>$this->user_id,'trade_type'=>2,'trade_status'=>3])->sum('trade_number');

    }
    //获取用户总买入
    public function getUserTotalBuy1($userId)
    {
        return $this->where(['user_id'=>$userId,'trade_type'=>1,'trade_status'=>3])->sum('trade_number');

    }
    //获取用户总卖出
    public function getUserTotalSell1($userId)
    {
        return $this->where(['user_id'=>$userId,'trade_type'=>2,'trade_status'=>3])->sum('trade_number');

    }

    //检测用户是否异常
    //规则:买入为0则直接为异常,卖出/买入>=$score也为异常
    public function checkUserStatus()
    {
        $score = (new C2CSetting())->getOneRecord()['unusual_rate'];
        $tb = $this->getUserTotalBuy();
        $ts = $this->getUserTotalSell();
        if ($tb == 0){
            if ($ts > 0)
            return 1;
            return 0;
        }else{
            if ($ts/$tb >= $score){
                return 1;
            }
            return 0;
        }

    }

    //获取用户冻结中的数量
    public function getFreezeAmount($userId)
    {
        return $this->where(['user_id'=>$userId,'trade_type'=>2])->where('trade_status','!=',3)->sum('trade_number');
    }


}