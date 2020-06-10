<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/11
 * Time: 14:04
 */

namespace App\Model;

use App\Model\kgModel\UserWallet;
use App\Server\ChatServer\ChatServer;
use Illuminate\Database\Eloquent\Model;

class CoinTradeOrder extends Model
{

    /*表名称*/
    protected $table = 'coin_trade_order';

    protected $primaryKey = 'order_id';

//    private $fields =['coin_id','coin_name'];

//    protected $fillable = ['*'];
    protected $guarded = [];

    /*存储一条记录*/
    public function saveOneRecord(int $userId,int $coinId,$orderHash,$fromAddress,$toAddress,$amount,$orderType=0,$fee=0,$transferType = 0)
    {
        $this->user_id = $userId;
        $this->coin_id = $coinId;
        $this->order_trade_hash = $orderHash;
        $this->order_trade_from = $fromAddress;
        $this->order_trade_to = $toAddress;
        $this->order_trade_money = $amount;

        if ($orderType != 0) $this->order_type = $orderType;
        if ($fee != 0) $this->order_trade_fee = $fee;

        if ($orderType == 2){
            $this->order_check_status = 1;
            $this->order_status = 1;
            $this->transfer_type = 1;
        }



        if ($transferType) $this->transfer_type = $transferType;

        if ($this->save()) return $this;
        return 0;
    }

    /*更新一条记录*/
    public function updateOneRecord(int $orderId,array $data)
    {
        return $this->where('order_id',$orderId)->update($data);
    }

    /*获取记录*/
    public function getRecords(array $conditions)
    {
        return $this->with('coinName')->where($conditions)->get();
    }

    /*获取用户转账记录*/
    public function getUserOrders(int $userId,int $coinId)
    {
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->orderByDesc('order_id')->get();
    }

    /*模型关联查询货币coinname*/
    public function coinName()
    {
        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')->select(['coin_id','coin_name']);
    }

    /*根据id获取记录*/
    public function getRecordById(int $orderId)
    {
        return $this->with('coinName','centerWallet','coinFees')->find($orderId)->toArray();
    }

    /*模型关联获取中央钱包*/
    public function centerWallet()
    {
        return $this->hasOne('App\Model\CenterWalletDetail','coin_id','coin_id');
    }

    /*fees*/
    public function coinFees()
    {
        return $this->hasOne('App\Model\CoinFees','coin_id','coin_id');
    }

    /*关联用户表*/
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'user_id');
    }

    public function getStatus()
    {
        return [
            0 => '待审核',
            1 => '通过审核',
            2 => '拒绝'
        ];
    }

    public function getType($order)
    {
        if ($order->order_type == 1) {

            if ($order->transfer_type == 1) {
                return '平台->外部地址';
            } elseif ($order->transfer_type == 2) {
                return '平台->平台';
            } elseif ($order->transfer_type == 3) {
                return '平台->互链';
            } elseif ($order->transfer_type == 4) {
                return '平台->星云';
            } elseif ($order->transfer_type == 5) {
                return '平台->chat';
            } elseif (WalletDetail::where('wallet_address', $order->order_trade_to)->first()) {
                return '平台->平台';
            } elseif ($order->transfer_type == 6) {
                return '平台->PLC';
            }else {
                return '平台->外部地址';
            }
        }else{
            if ($order->transfer_type == 1) {
                return '外部地址->平台';
            } elseif ($order->transfer_type == 2) {
                return '平台->平台';
            } elseif ($order->transfer_type == 3) {
                return '互链->平台';
            }elseif ($order->transfer_type == 4){
                return '星云->平台';
            }elseif ($order->transfer_type == 5){
                return 'chat->平台';
            }
            elseif (WalletDetail::where('wallet_address', $order->order_trade_to)->first()) {
                return '平台->平台';
            } elseif ($order->transfer_type == 6) {
                return 'PLC->平台';
            } else {
                return '外部地址->平台';
            }
        }

    }

    public function getHLRechargeAddressNum($tts_address)
    {
        $num =  $this->where(['order_trade_to'=>$tts_address,'order_type'=>2,'transfer_type'=>3])->groupBy('order_trade_from')->get()->count();

        return $num;
    }

    public function getTTSTOHLAddressNum($hl_address)
    {
        $num =  $this->where(['order_trade_to'=>$hl_address,'order_type'=>1,'transfer_type'=>3])->groupBy('user_id')->get()->count();

        return $num;
    }


    public function from_wallet()
    {
        return $this->hasOne(WalletDetail::class,'wallet_address','order_trade_from');
    }

    public function to_wallet()
    {
        return $this->hasOne(WalletDetail::class,'wallet_address','order_trade_to');
    }


}