<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 13:32
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CoinFees extends Model
{
    protected $table = 'coin_fees';

    protected $primaryKey = 'id';

//    protected $fillable = ['coin_id', 'fixed_fee', 'percent_fee', 'eth_gaslimit', 'eth_gasprice', 'fee_type', 'withdraw_min', 'withdraw_max', 'recharge_min','ore_pool_min','inside_transfer_lock_time','transfer_fee','ore_pool_free_rate'];

    protected $guarded = [];


    public function getOneRecord(int $coinId)
    {
        return $this->where(['coin_id'=>$coinId,'is_usable'=>1])->first()->toArray();
    }


    public function coin()
    {
        return $this->hasOne('App\Model\CoinType', 'coin_id', 'coin_id');
    }


}