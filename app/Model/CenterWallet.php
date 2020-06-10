<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CenterWallet extends Model
{
    protected $table = 'real_coin_center_wallet';

    protected $primaryKey = 'wallet_id';

//    protected $fillable = ['coin_id', 'total_money','total_interest_money','coin_sum_money'];
protected $guarded = [];

    public function CoinType()
    {
        return $this->belongsTo('App\Model\CoinType', 'coin_id');
    }


    /* 添加某一个虚拟币的中央钱包余额
     * @param
     *  @coin_id
     *
     */
    public function addCenterCoinBalance($coin_id,$balance){
       return $this->where(['coin_id'=>$coin_id,'is_usable'=>1])->increment('total_interest_money',$balance);
    }



}
