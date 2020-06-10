<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 16:27
 */

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\WalletDetail;
use App\Model\BankCardVerify;
use App\Model\User;
use App\Traits\RedisTool;
use App\Model\BankList;
use App\Model\UserIdentify;

class C2CTradeOrder extends Model
{
    use RedisTool;

    protected $table = 'c2c_trade_order';

    protected $primaryKey = 'order_id';

    protected $fillable = ['order_status','bank_id','bank_card_no','transfer_img','confirm_at'];

    //获取当前未确认的单数
    public function getUnConfirmOrderNum($bUserId)
    {
        return $this->where(['business_user_id'=>$bUserId,'order_status'=>1])->
            whereHas('tradeMsg',function ($q){
                $q->where(['trade_type'=>1]);
        })->count();
    }

    /*接单*/
    public function saveOneRecord($orderNumber,$orderPayNumber,$tradeId,$businessUserId,$bankId,$bankCardNo,$name,$coinId)
    {

        $this->order_number = $orderNumber;
        $this->order_pay_number = $orderPayNumber;
        $this->trade_id = $tradeId;
        $this->business_user_id = $businessUserId;
        $this->bank_id = $bankId;
        $this->bank_card_no = $bankCardNo;
        if ($name) $this->card_name = $name;
        $this->coin_id = $coinId;

//        $bankCard = (new BankCardVerify())->getRecordByUserId($businessUserId);
//        if (!$bankCard) return 0;
//        $this->business_bank_card_id = $bankCard->verify_id;

        if ($this->save()){
            return $this->toArray();
        }
        return 0;


    }


    /*确认收款*/
    public function confirmBuyOrder(int $userId,int $orderId,$c2cSetting)
    {
        DB::beginTransaction();
        $order = $this->getOrder($userId,$orderId);


//dd($order);
        if (!$order || ($order->order_status != 1) || ($order->tradeMsg->trade_type != 1)) return 0;

        if ((time()-strtotime($order->created_at)) < $c2cSetting['business_buy_order_confirm_time']*60) return 2;

        $bWallet = WalletDetail::where(['user_id'=>$order->business_user_id,'coin_id'=>$order->tradeMsg->coin_id,'is_usable'=>1])->first();
//        $uWallet = WalletDetail::where(['user_id'=>$order->tradeMsg->user_id,'coin_id'=>$order->tradeMsg->coin_id,'is_usable'=>1])->first();
        $uWallet = (new WalletDetail())->getOrCreateUserWallet($order->tradeMsg->user_id,$order->tradeMsg->coin_id);
//dd($uWallet);
        if (
            $bWallet
            && $uWallet
            && $bWallet->decrement('wallet_freeze_balance',$order->tradeMsg->trade_number)
            && $uWallet->increment('wallet_usable_balance',$order->tradeMsg->trade_number)
            && $order->update(['order_status'=>3,'confirm_at'=>date('Y-m-d H:i:s',time())])
            && $order->tradeMsg->update(['trade_status'=>3])
            && (new WalletFlow())->insertOne($uWallet['user_id'],$uWallet['wallet_id'],$uWallet['coin_id'],$order->tradeMsg->trade_number,3,1,'c2c买入',1)
        ){

            DB::commit();
            (new C2c_User_Last_Trade_Time())->insertOne($uWallet->user_id);
            if ($uWallet->user->c2c_long_time_not_buy_status){
                $uWallet->user->update(['c2c_long_time_not_buy_status'=>0]);
            }
//            if ($this->redisExists('C2C_BUSINESS_BUY_TIMELIMIT_'.$userId)) $this->redisDelete('C2C_BUSINESS_BUY_TIMELIMIT_'.$userId);
            if ($this->getUnConfirmOrderNum($userId) == 0 && $this->redisExists('C2C_BUSINESS_BUY_TIMELIMIT_'.$userId)){
                $this->redisDelete('C2C_BUSINESS_BUY_TIMELIMIT_'.$userId);
            }

            return 1;
        }
        DB::rollBack();return 0;


    }

    /*确认打款*/
    public function confirmSellOrder(array $data,$c2cSetting)
    {
        DB::beginTransaction();
        $order = $this->getOrder($data['user_id'],$data['order_id']);

        if (!$order || ($order->order_status != 1) || ($order->tradeMsg->trade_type != 2)) return 0;

        if ((time()-strtotime($order->created_at)) < $c2cSetting['business_sell_order_confirm_time']*60) return 2;

//        $bWallet = WalletDetail::where(['user_id'=>$order->business_user_id,'coin_id'=>$order->tradeMsg->coin_id,'is_usable'=>1])->first();
//        $uWallet = WalletDetail::where(['user_id'=>$order->tradeMsg->user_id,'coin_id'=>$order->tradeMsg->coin_id,'is_usable'=>1])->first();

        if (
//            $bWallet
//            && $uWallet
//            && $bWallet->increment('wallet_usable_balance',$order->tradeMsg->trade_number)
//            && $uWallet->decrement('wallet_freeze_balance',$order->tradeMsg->trade_number)
                $order->update(['order_status'=>2,'confirm_at'=>date('Y-m-d H:i:s',time()),'transfer_img'=>$data['transfer_img'],'bank_id'=>$data['bank_id'],'bank_card_no'=>$data['bank_card_no']])
//            && $order->tradeMsg->update(['trade_status'=>3])
        ){
            DB::commit();return 1;
        }
        DB::rollBack();return 0;

    }

    /*后台审核打款凭证*/
    public function confirmTransfer($orderId,$checkStatus)
    {
        if ($checkStatus == 2) return $this->refuseTransfer($orderId);
        DB::beginTransaction();

        $order = $this->with('tradeMsg')->lockForUpdate()->find($orderId);

        if (!$order || ($order->order_status != 2) || ($order->tradeMsg->trade_type != 2)) return 0;

        $bWallet = WalletDetail::where(['user_id'=>$order->business_user_id,'coin_id'=>$order->tradeMsg->coin_id,'is_usable'=>1])->first();
        $uWallet = WalletDetail::where(['user_id'=>$order->tradeMsg->user_id,'coin_id'=>$order->tradeMsg->coin_id,'is_usable'=>1])->first();

        if (
            $bWallet
            && $uWallet
            && $bWallet->increment('wallet_withdraw_balance',$order->tradeMsg->trade_number)
            && $uWallet->decrement('wallet_freeze_balance',$order->tradeMsg->trade_number)
            && $order->update(['order_status'=>3])
            && $order->tradeMsg->update(['trade_status'=>3])
            && (new WalletFlow())->insertOne($bWallet['user_id'],$bWallet['wallet_id'],$bWallet['coin_id'],$order->tradeMsg->trade_number,5,1,'c2c买入',2)
//            && (new WalletFlow())->insertOne($uWallet['user_id'],$uWallet['wallet_id'],$uWallet['coin_id'],$order->tradeMsg->trade_number,3,1,'c2c买入',1)
        ){
            DB::commit();return 1;
        }
        DB::rollBack();return 0;
    }
    //审核拒绝
    public function refuseTransfer($orderId)
    {
        DB::beginTransaction();
        $order = $this->with('tradeMsg')->lockForUpdate()->find($orderId);
        if (!$order || ($order->order_status != 2) || ($order->tradeMsg->trade_type != 2)) return 0;

        if (
            $order->update(['order_status'=>1])
        ){
            DB::commit();return 1;
        }
        DB::rollBack();return 0;
    }

    /*获取一条记录*/
    public function getOrder(int $userId,int $orderId)
    {
        return $this->with('tradeMsg')->where(['order_id'=>$orderId,'business_user_id'=>$userId,'is_usable'=>1])->lockForUpdate()->first();//悲观锁
    }

    /*获取商家订单列表*/
    public function getBOrderList(int $bUserId,int $handleStatus)
    {
        switch ($handleStatus){
            case 1://处理中
                return $this->with(['tradeMsg','base_coin'])->where(['business_user_id'=>$bUserId,'is_usable'=>1])->whereIn('order_status',[1,2])->get()->toArray();
                break;
            case 2://已完成
                return $this->with(['tradeMsg','base_coin'])->where(['business_user_id'=>$bUserId,'is_usable'=>1])->whereIn('order_status',[3,4,0])->orderByDesc('updated_at')->get()->toArray();
                break;
        }

    }

    /*商家获取特定订单*/
    public function getBOrder(int $bUserId,int $orderId)
    {
        $order = $this->with('tradeMsg')->where(['order_id'=>$orderId,'is_usable'=>1,'business_user_id'=>$bUserId])->first();
//        dd($order->toArray());
        if (!$order) return 0;

        $userMsg = (new User())->getUserById($order->tradeMsg->user_id,['user_name','user_phone'])->toArray();
        $userIdentifyName= UserIdentify::where(['user_id'=>$order->tradeMsg->user_id,'is_usable'=>1])->first()->identify_name;
        $userRealName= (new BankCardVerify())->getRecordByUserId($order->tradeMsg->user_id)->verify_name;
        $businessName= UserIdentify::where(['user_id'=>$bUserId,'is_usable'=>1])->first()->identify_name;
        $userMsg['user_name'] = $userRealName;
        $userMsg['user_identify_name'] = $userIdentifyName;

        //$bankCard = (new BankCardVerify())->getOneBankCard($order->tradeMsg->user_id);
        $bank = (new BankList())->getRecordById($order->tradeMsg->bank_id);
        unset($order['bank_card_no']);
        return ['order'=>$order,'bank'=>$bank,'user_msg'=>$userMsg,'business_name'=>$businessName];


    }

    public function getTransferingRecords(int $userId)
    {
        return $this->with('tradeMsg')->where(['business_user_id'=>$userId,'is_usable'=>1])->whereIn('order_status',[1,2])->get()->toArray();

    }

    public function getBOrders(int $userId)
    {
        return $this->with('tradeMsg')->where(['business_user_id'=>$userId,'order_status'=>3])->get()->toArray();

    }

    /*模型关联*/
    public function tradeMsg()
    {
        return $this->hasOne('App\Model\C2CTrade','trade_id','trade_id')->select('trade_id','trade_number','coin_id','trade_type','user_id','trade_price','currency_id','bank_id','bank_card_no','updated_at','created_at');
    }

    //关联用户表
    public function user()
    {
        return $this->hasOne('App\Model\User', 'user_id', 'business_user_id')->where(['is_usable'=>1])->select();
    }
    public function userIdentify()
    {
        return $this->hasOne(UserIdentify::class,'user_id','business_user_id');
    }

    public function coinId()
    {
        return $this->hasOne(CoinType::class,'coin_id','coin_id');
    }

    public function base_coin()
    {
        return $this->belongsTo(CoinType::class,'coin_id','coin_id')->select(['coin_id','coin_name']);
    }


    public function getOrderStatus()
    {
        return
            [
                0 => '<i style="color: 	red">商家撤单</i>',
                1 => '<i style="color: 	#F08080">商家拍下订单,待确认</i>',
                2 => '<i style="color: #EE7621">商家已确认,待审核</i>',
                3 => '<i style="color: 	green">后台确认完毕,交易完成</i>',
                4 => '<i style="color: 	red">超时自动撤单</i>',
            ];
    }

    //商家收入支出信息
    public function iODetails($userId)
    {
        $data = [];
        $query = $this->where(['business_user_id'=>$userId,'order_status'=>3])
            ->join('c2c_trade','c2c_trade.trade_id' ,'=' ,'c2c_trade_order.trade_id')
            ->select(DB::raw('sum(c2c_trade.trade_number*c2c_trade.trade_price) as total'));
        $data['zIn'] = $query->where('c2c_trade.trade_type',1)->value('total') ?: 0;
        $data['zOut'] = $query->where('c2c_trade.trade_type',2)->value('total') ?: 0;

        $data['dIn'] = $query->where('c2c_trade.trade_type',1)->whereDay('c2c_trade_order.updated_at',date('Y-m-d H:i:s'))->value('total') ?: 0;
        $data['dOut'] = $query->where('c2c_trade.trade_type',2)->whereDay('c2c_trade_order.updated_at',date('Y-m-d H:i:s'))->value('total') ?: 0;

        $data['wIn'] = $query->where('c2c_trade.trade_type',1)->whereBetween('c2c_trade_order.updated_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->value('total') ?: 0;
        $data['wOut'] = $query->where('c2c_trade.trade_type',2)->whereBetween('c2c_trade_order.updated_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->value('total') ?: 0;

        $data['mIn'] = $query->where('c2c_trade.trade_type',1)->whereMonth('c2c_trade_order.updated_at',date('m'))->value('total') ?: 0;
        $data['mOut'] = $query->where('c2c_trade.trade_type',2)->whereMonth('c2c_trade_order.updated_at',date('m'))->value('total') ?: 0;

//        dd($data);
        return $data;
    }


}