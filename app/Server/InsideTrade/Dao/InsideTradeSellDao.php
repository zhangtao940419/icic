<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 10:58
 */

namespace App\Server\InsideTrade\Dao;

use App\Model\InsideTradeSell;

class InsideTradeSellDao
{
    protected $insideTradeSell;

    public function __construct(InsideTradeSell $insideTradeSell)
    {
        $this->insideTradeSell = $insideTradeSell;
    }

    public function getInsideTradeSell(){
        return $this->insideTradeSell;
    }

    public function getOneRecord($where){
        return $this->insideTradeSell->where($where)->first()->toArray();
    }

    public function getManyRecord($where){
        return $this->insideTradeSell->where($where)->get()->toArray();
    }

    public function insertOneRecord($data){
        return $this->insideTradeSell::create($data);
    }

    public function updateOneRecord($where,$update_data){
        return $this->insideTradeSell->where($where)->update($update_data);
    }

    public function getFinishSell($user_id, $base_coin_id, $exchange_coin_id){
        return $this->insideTradeSell
            ->where(['user_id'=>$user_id, 'base_coin_id' => $base_coin_id, 'exchange_coin_id' => $exchange_coin_id, 'trade_statu'=>2,'is_usable'=>1])
            ->latest()
            ->get()->toArray();
    }

    public function getHistorySell($user_id, $base_coin_id, $exchange_coin_id){
        return $this->insideTradeSell
            ->where(['user_id'=>$user_id, 'base_coin_id' => $base_coin_id, 'exchange_coin_id' => $exchange_coin_id,'is_usable'=>1])
            ->latest()
            ->get()->toArray();
    }
    //获取最新数据
    public function getManyTrade($base_coin_id, $exchange_coin_id, $user_id)
    {
        return $this->insideTradeSell
                ->where(['is_usable' => 1, 'base_coin_id' => $base_coin_id, 'exchange_coin_id' => $exchange_coin_id, 'user_id' => $user_id])
                ->where('trade_statu', 1)
                ->latest()
                ->get();
    }
}
