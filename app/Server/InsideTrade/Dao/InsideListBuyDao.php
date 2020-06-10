<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 10:58
 */

namespace App\Server\InsideTrade\Dao;

use App\Model\InsideListBuy;

class InsideListBuyDao
{

    protected $insideListBuy;

    public function __construct(InsideListBuy $insideListBuy)
    {
        $this->insideListBuy = $insideListBuy;
    }

    public function insertOneRecord($Param){
        return $this->insideListBuy::create($Param);
    }

    public function getOneRecord($where){
        return $this->insideListBuy->where($where)->first()->toArray();
    }

    public function updateOneRecord($where,$update_data){
        return $this->insideListBuy->where($where)->update($update_data);
    }

    public function deleteOneRecord($where){
        return $this->insideListBuy->where($where)->delete();
    }

    public function getBuyTradeList($base_coin_id,$exchange_coin_id,$unit_price,$user_id){
        return $this->insideListBuy->where([
                ['base_coin_id','=',$base_coin_id],
                ['exchange_coin_id','=',$exchange_coin_id],
                ['unit_price','>=',$unit_price],
               // ['user_id','!=',$user_id],
            ])
            ->orderBy('unit_price','desc')
            ->get()
            ->toArray();
    }

}
