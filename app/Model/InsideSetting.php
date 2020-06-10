<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/10/15
 * Time: 17:54
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class InsideSetting extends Model
{



    protected $table = 'inside_setting';


    protected $primaryKey = 'id';


    protected $guarded = [];





    public function insertOne($baseCoinId,$exchangeCoinId,$daySellNumLimit,$fee = 0.7)
    {

        $this->base_coin_id = $baseCoinId;
        $this->exchange_coin_id = $exchangeCoinId;
        $this->day_sell_num_limit = $daySellNumLimit;
        $this->fee = $fee;
        return $this->save();
    }



    public function getOneDaySellNumLimit($baseCoinId,$exchangeCoinId)
    {
        $re = $this->where(['base_coin_id'=>$baseCoinId,'exchange_coin_id'=>$exchangeCoinId])->first();
        if (!$re) return false;return $re->day_sell_num_limit;
    }

    public function getFee($baseCoinId,$exchangeCoinId)
    {
        $re = $this->where(['base_coin_id'=>$baseCoinId,'exchange_coin_id'=>$exchangeCoinId])->first();
        if (!$re) return 0.007;return $re->fee / 100;
    }


    public function getSetting($baseCoinId,$exchangeCoinId)
    {
        $re = $this->where(['base_coin_id'=>$baseCoinId,'exchange_coin_id'=>$exchangeCoinId])->first();

        if ($re) return $re;return false;

    }

    public function setOne($baseCoinId,$exchangeCoinId,$daySellNumLimit,$fee = 0.7)
    {
        $re = $this->where(['base_coin_id'=>$baseCoinId,'exchange_coin_id'=>$exchangeCoinId])->first();

        if($re) return $re->update(['day_sell_num_limit'=>$daySellNumLimit,'fee'=>$fee]);
        return $this->insertOne($baseCoinId,$exchangeCoinId,$daySellNumLimit,$fee);
    }


}