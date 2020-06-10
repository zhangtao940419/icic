<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 11:00
 */

namespace App\Server\InsideTrade\Dao;

use App\Model\InsideCountBuy;
use App\Model\InsideListBuy;


class InsideCountBuyDao
{
    protected $insideCountBuy;

    protected $insideListBuy;

    public function __construct(InsideCountBuy $insideCountBuy,InsideListBuy $insideListBuy )
    {
        $this->insideCountBuy = $insideCountBuy;
        $this->insideListBuy = $insideListBuy;
    }

    public function getInsideCountBuy(){
        return $this->insideCountBuy;
    }

    public function getOneRecord($where){
        return $this->insideCountBuy->where($where)->first();
    }

    public function insertOneRecord($data){
        return $this->insideCountBuy::create($data);
    }

    //减少盘面数据
    public function dealCountBuy($param,$trade_num){
        $where=['base_coin_id'=>$param['base_coin_id'],
               'exchange_coin_id'=>$param['exchange_coin_id'],
               'unit_price'=>$param['unit_price']
               ];
     if( $this->insideCountBuy->decrementCount($where,$trade_num)
       ){
         if($this->getOneRecord($where)->trade_total_num<=0.00000009 && !$this->insideListBuy->where($where)->count())
            return $this->deleteOneRecord($where);
         return 1;
     }
     return 0;
    }

    //添加数据到盘面统计
    public function addCountRecord($param){

       $data =  $this->getOneRecord(['unit_price'=>$param['unit_price'],
                             'base_coin_id'=>$param['base_coin_id'],
                              'exchange_coin_id'=>$param['exchange_coin_id']]);
        if(!empty($data)){
           return $this->insideCountBuy->incrementCount(['id'=>$data->id],$param['trade_total_num']);
        }else{
            return  $this->insertOneRecord($param);
        }

    }

    public function getBuyDisksurface($inParam){
        return $this->insideCountBuy
                    ->where(['base_coin_id'=>$inParam['base_coin_id'],'exchange_coin_id'=>$inParam['exchange_coin_id']])
                    ->orderBy('unit_price','desc')
                    ->select('unit_price','trade_total_num')
                    ->get()->toArray();
    }

    public function updateOneRecord($where,$update_data){
        return $this->insideCountBuy->where($where)->update($update_data);
    }

    public function deleteOneRecord($where){
        return $this->insideCountBuy->where($where)->delete();
    }

}
