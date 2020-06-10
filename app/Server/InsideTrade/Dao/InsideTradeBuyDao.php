<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 10:56
 */

namespace App\Server\InsideTrade\Dao;

use App\Model\InsideTradeBuy;

class InsideTradeBuyDao
{

    public $insideTradeBuy;

    public function __construct(InsideTradeBuy $insideTradeBuy)
    {
        $this->insideTradeBuy = $insideTradeBuy;
    }

    public function getInsideTradeBuy(){
        return $this->insideTradeBuy;
    }

    public function getOneRecord($where){
        return $this->insideTradeBuy->where($where)->first()->toArray();
    }

    public function getManyRecord($where){
        return $this->insideTradeBuy->where($where)->get()->toArray();
    }

    public function insertOneRecord($data){
        return $this->insideTradeBuy::create($data);
    }

    public function updateOneRecord($where,$update_data){
        return $this->insideTradeBuy->where($where)->update($update_data);
    }

    public function getFinishBuy($user_id, $base_coin_id, $exchange_coin_id){
        return $this->insideTradeBuy
                    ->where(['user_id'=>$user_id, 'base_coin_id' => $base_coin_id, 'exchange_coin_id' => $exchange_coin_id, 'trade_statu'=>2,'is_usable'=>1])
                    ->latest()
                    ->get()->toArray();
    }

    public function getHistoryBuy($user_id, $base_coin_id, $exchange_coin_id){
        return $this->insideTradeBuy
            ->where(['user_id'=>$user_id, 'base_coin_id' => $base_coin_id, 'exchange_coin_id' => $exchange_coin_id,'is_usable'=>1])
            ->latest()
            ->get()->toArray();
    }

    //获取最新数据
    public function getManyTrade($base_coin_id, $exchange_coin_id, $user_id)
    {
        return $this->insideTradeBuy
                    ->where(['is_usable' => 1, 'base_coin_id' => $base_coin_id, 'exchange_coin_id' => $exchange_coin_id, 'user_id' => $user_id])
                    ->where('trade_statu', 1)
                    ->latest()
                    ->get();
    }

}
