<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/20
 * Time: 16:04
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CenterStoWallet extends Model
{



    protected $table = 'real_coin_sto_center_wallet';


    protected $primaryKey = 'id';


    protected $guarded = [];



    public function create_wallet($base_coin_id,$exchange_coin_id){

        $this->base_coin_id = $base_coin_id;
        $this->exchange_coin_id = $exchange_coin_id;

        if ($this->save())
            return $this;
        return 0;


    }

    public function get_wallet($base_coin_id,$exchange_coin_id)
    {
        $re = $this->where(['base_coin_id'=>$base_coin_id,'exchange_coin_id'=>$exchange_coin_id])->first();
        if ($re) return $re;
        return $this->create_wallet($base_coin_id,$exchange_coin_id);
    }



    public function inc_balance($amount)
    {
        return $this->where(['id'=>$this->id])->increment('balance',$amount);
    }


    public function getBalance($baseCoinId)
    {
        return $this->where(['base_coin_id'=>$baseCoinId])->sum('balance');
    }

}