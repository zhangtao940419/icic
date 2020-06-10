<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/9/26
 * Time: 16:29
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class InsideUserLastTradeTime extends Model
{

    protected $table = 'inside_user_last_trade_time';


    protected  $primaryKey = 'id';


    protected $guarded = [];



    public $timestamps = false;


    public function insertOne($userid,$baseCoinId,$exchangeCoinId)
    {
        $re = $this->where(['user_id'=>$userid,'base_coin_id' => $baseCoinId,'exchange_coin_id' => $exchangeCoinId])->first(['id']);
        if ($re) return $re->update(['timestamp' => time()]);
        return $this->insert([
            'user_id' => $userid,
            'base_coin_id' => $baseCoinId,
            'exchange_coin_id' => $exchangeCoinId,
            'timestamp' => time()
        ]);

    }



}