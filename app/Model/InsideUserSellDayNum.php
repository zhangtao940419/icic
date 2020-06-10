<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/10/14
 * Time: 17:24
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class InsideUserSellDayNum extends Model
{

    protected $table = 'inside_user_sell_day_num';


    protected $primaryKey = 'id';


    protected $guarded = [];






    public function getTodayNum($userId,$baseCoinId,$exchangeCoinId)
    {
        $re = $this->where(['day' => date('Ymd'),'user_id'=>$userId,'base_coin_id'=>$baseCoinId,'exchange_coin_id' => $exchangeCoinId])->first();
        if ($re) return $re->sell_num;
        return 0;

    }

    public function createOne($userId,$baseCoinId,$exchangeCoinId,$num = 1)
    {
        $this->user_id = $userId;
        $this->base_coin_id = $baseCoinId;
        $this->exchange_coin_id = $exchangeCoinId;
        $this->sell_num = $num;
        $this->day = date('Ymd');
        return $this->save();
    }



    public function incTodayNum($userId,$baseCoinId,$exchangeCoinId,$num = 1)
    {
        $re = $this->where(['day' => date('Ymd'),'user_id'=>$userId,'base_coin_id'=>$baseCoinId,'exchange_coin_id' => $exchangeCoinId])->first();
        if ($re) return $re->increment('sell_num',$num);

        return $this->createOne($userId,$baseCoinId,$exchangeCoinId,$num);
    }


    public function decTodayNum($userId,$baseCoinId,$exchangeCoinId,$num = 1)
    {
        $re = $this->where(['day' => date('Ymd'),'user_id'=>$userId,'base_coin_id'=>$baseCoinId,'exchange_coin_id' => $exchangeCoinId])->first();
        if ($re && ($re->sell_num > 0)) return $re->decrement('sell_num',$num);

        return true;

    }



}