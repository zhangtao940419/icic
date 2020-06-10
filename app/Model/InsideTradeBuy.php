<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InsideTradeBuy extends Model
{
    protected $primaryKey = 'buy_id';

    /*
     * 与模型关联的数据表
     * @var string
     */
    protected $table = 'inside_trade_buy';

    protected $fillable = [
        'order_number',
        'user_id',
        'unit_price',
        'base_coin_id',
        'exchange_coin_id',
        'trade_price_show',
        'trade_total_num',
        'want_trade_count',
        'trade_total_money',
        'trade_statu',
        'is_usable',
    ];

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

    //关联用户表
    public function getUser()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'user_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'user_id');
    }

    //关联场内订单表
    public function insideOrder()
    {
        return $this->hasMany(InsideTradeOrder::class, 'buy_id', 'buy_id');
    }

    public function getStatus()
    {
        return [
            -1 => '系统自动撤单',
            0 => '撤销订单',
            1 => '挂单状态',
            2 => '已完成'
        ];
    }

    //查询冻结金额
    public function getFreezeAmount($userId,$coinId)
    {
        return $this->where(['trade_statu'=>1,'user_id'=>$userId,'base_coin_id'=>$coinId])->first([\DB::raw('SUM(trade_total_num*unit_price) as total')])->total;

    }





}
