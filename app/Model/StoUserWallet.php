<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/9
 * Time: 14:22
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class StoUserWallet extends Model
{


    protected $table = 'sto_user_wallet';

    protected $primaryKey = 'id';


    protected $guarded = [];



    public function getUserWalletByCoinId($userId,$coinId)
    {

        $wallet = $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->first();

        if (!$wallet){
            $this->createWallet($userId,$coinId);
            $wallet = $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->first();
        }

        return $wallet;

    }



    public function createWallet($userId,$coinId)
    {
        return $this->insert([
            'user_id' => $userId,
            'coin_id' => $coinId,
            'created_at' => datetime(),
            'updated_at' => datetime()
        ]);
    }


    //提取
    public function tq($amount)
    {
        $r1 = $this->dec_usable_balance($amount);
        $r2 = $this->add_extract_amount($amount);
        if ($r1 && $r2) return true; return false;
    }

    //增加余额
    public function inc_usable_balance($num)
    {
        return $this->where(['id'=>$this->id])->increment('usable_balance',$num);
    }
    public function dec_usable_balance($num)
    {
        return $this->where(['id'=>$this->id])->where('usable_balance','>=',$num)->decrement('usable_balance',$num);
    }
    public function add_extract_amount($num)
    {
        return $this->where(['id'=>$this->id])->increment('has_extract_amount',$num);
    }

    public function sto_coin_data()
    {
        return $this->hasOne(StoCoinData::class,'coin_id','coin_id');
    }

    public function coin()
    {
        return $this->belongsTo(CoinType::class,'coin_id','coin_id');
    }

    public function sto_wallet_flow()
    {
        return $this->hasMany(StoRewardFlow::class,'wallet_id','id');
    }



}