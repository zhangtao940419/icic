<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InsideCountBuy extends Model
{
    protected $primaryKey = 'id';

    /*
     * 与模型关联的数据表
     * @var string
     */
    protected $table = 'inside_count_buy';

    protected $fillable = [
        'unit_price',
        'base_coin_id',
        'exchange_coin_id',
        'trade_total_num',
        'is_usable',
    ];

    /*增加盘面统计数量*/
    public function incrementCount($where,$trade_num)
    {
        return $this->where($where)->increment('trade_total_num',$trade_num);
    }

    //减少盘面数据
    public function decrementCount($where,$trade_num)
    {
        return $this->where($where)->decrement('trade_total_num',$trade_num);
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

}
