<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InsideTradeOrder extends Model
{
    protected $primaryKey = 'order_id';

    /*
     * 与模型关联的数据表
     * @var string
     */
    protected $table = 'inside_trade_order';

    protected $fillable = [
        'buy_order_number',
        'sell_order_number',
        'buy_id',
        'sell_id',
        'buy_user_id',
        'sell_user_id',
        'unit_price',
        'base_coin_id',
        'exchange_coin_id',
        'trade_num',
        'trade_total_money',
        'trade_poundage',
        'trade_statu',
        'is_evaluate',
        'is_usable',
        'traded_type'
    ];

    //时间访问器
    public function getCreatedAtAttribute($value)
    {
        $res = \Route::currentRouteName();

        if ($res == "getInsideHistoryTrade" || $res == "getCarefulInsideHistoryTrade") {
            return $this->attributes['created_at'] = date('m-d', strtotime($value));
        } else {
            return $this->attributes['created_at'];
        }
    }

    //买家关联用户表
    public function buyUser()
    {
        return $this->belongsTo('App\Model\User', 'buy_user_id', 'user_id');
    }

    //卖家关联用户表
    public function sellUser()
    {
        return $this->belongsTo('App\Model\User', 'sell_user_id', 'user_id');
    }

    //交易底货币
    public function getBaseCoin()
    {
        return $this->hasOne('App\Model\CoinType', 'coin_id', 'base_coin_id');
    }

    //交换的货币
    public function getExchangeCoin()
    {
        return $this->hasOne('App\Model\CoinType', 'coin_id', 'exchange_coin_id');
    }

    //获取用户今日卖单成交的次数
    public function getUserTodaySellSuccessNum($userId)
    {
        return $this->where(['sell_user_id'=>$userId,'trade_statu'=>1])->whereDate('created_at',date('Y-m-d'))->count();
//        return


    }
    public function getUserAllSellSuccessNum($userId)
    {
        return $this->where(['sell_user_id'=>$userId,'trade_statu'=>1])->count();


    }

    //获取卖单的成交笔数
    public function getSellOrderDealNum($sell_order_number)
    {
        return $this->where(['sell_order_number'=>$sell_order_number])->count();


    }



}
