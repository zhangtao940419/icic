<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class coinTransaction extends Model
{
    protected $table = 'coin_transaction';

    protected $fillable = ['base_coin_id', 'exchange_coin_id', 'vol', 'price_float', 'max_price',
        'min_price', 'current_price', 'float_type', 'base_coin_name', 'exchange_coin_name'];

    public function coin()
    {
        return $this->hasOne('App\Model\CoinType', 'coin_id', 'coin_id');
    }

    public function changeCoin()
    {
        return $this->belongsTo('App\Model\CoinType', 'exchange_coin_id', 'coin_id');
    }

    /*查询虚拟货币的价格*/
    public function getCoinPrice(int $base_coin_id)
    {
        return $this->where(['base_coin_id' => $base_coin_id, 'exchange_coin_name' => 'USDT'])->pluck('current_price')->first();
    }

}
