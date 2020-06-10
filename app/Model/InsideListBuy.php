<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InsideListBuy extends Model
{
    protected $primaryKey = 'id';

    /*
     * 与模型关联的数据表
     * @var string
     */
    protected $table = 'inside_list_buy';

    protected $fillable = [
        'order_number',
        'user_id',
        'unit_price',
        'base_coin_id',
        'exchange_coin_id',
        'trade_total_num',
        'want_trade_count',
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

}
