<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 10:59
 */

namespace App\Server\InsideTrade\Dao;

use App\Model\InsideListSell;

class InsideListSellDao
{

    protected $insideListSell;

    public function __construct(InsideListSell $insideListSell)
    {
        $this->insideListSell = $insideListSell;
    }

    public function insertOneRecord($Param){
        return $this->insideListSell::create($Param);
    }

    public function getOneRecord($where){
        return $this->insideListSell->where($where)->first()->toArray();
    }

    public function updateOneRecord($where,$update_data){
        return $this->insideListSell->where($where)->update($update_data);
    }

    public function deleteOneRecord($where){
        return $this->insideListSell->where($where)->delete();
    }

    public function getSellTradeList($base_coin_id,$exchange_coin_id,$unit_price,$user_id){
        return $this->insideListSell->where([
                ['base_coin_id','=',$base_coin_id],
                ['exchange_coin_id','=',$exchange_coin_id],
                ['unit_price','<=',$unit_price],
                //['user_id','!=',$user_id],
            ])
            ->orderBy('unit_price','asc')
            ->get()
            ->toArray();
    }

}
