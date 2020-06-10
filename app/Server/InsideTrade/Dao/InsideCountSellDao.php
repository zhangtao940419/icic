<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 11:00
 */

namespace App\Server\InsideTrade\Dao;

use App\Model\InsideCountSell;
use App\Model\InsideListSell;

class InsideCountSellDao
{

    protected $insideCountSell;

    protected $insideListSell;

    public function __construct(InsideCountSell $insideCountSell,InsideListSell $insideListSell)
    {
        $this->insideCountSell = $insideCountSell;

        $this->insideListSell = $insideListSell;
    }

    public function getInsideCountSell(){
        return $this->insideCountSell;
    }

    public function getOneRecord($where){
        return $this->insideCountSell->where($where)->first();
    }

    public function insertOneRecord($data){
        return $this->insideCountSell::create($data);
    }

    //减少盘面数据
    public function dealCountSell($param,$trade_num){
        $where=['base_coin_id'=>$param['base_coin_id'],
            'exchange_coin_id'=>$param['exchange_coin_id'],
            'unit_price'=>$param['unit_price']
        ];
        if( $this->insideCountSell->decrementCount($where,$trade_num)
        ){
            if($this->getOneRecord($where)->trade_total_num<=0.00000009 && !$this->insideListSell->where($where)->count())
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
            return $this->insideCountSell->incrementCount(['id'=>$data->id],$param['trade_total_num']);
        }else{
            return  $this->insertOneRecord($param);
        }
    }

    public function getSellDisksurface($inParam){
        return $this->insideCountSell
                    ->where(['base_coin_id'=>$inParam['base_coin_id'],'exchange_coin_id'=>$inParam['exchange_coin_id']])
                    ->orderBy('unit_price','desc')
                    ->select('unit_price','trade_total_num')
                    ->get()->toArray();
    }

    public function adminGetSellDisksurface($inParam){
        $array =  $this->insideCountSell
            ->where(['base_coin_id'=>$inParam['base_coin_id'],'exchange_coin_id'=>$inParam['exchange_coin_id']])
            ->orderBy('unit_price','asc')
            ->select('unit_price','trade_total_num')
            ->offset(0)->limit($inParam['pageSize'])
            ->get()->toArray();
        if ($array) {
            $array = array_reverse($array);
        }
        return $array;
    }

    public function updateOneRecord($where,$update_data){
        return $this->insideCountSell->where($where)->update($update_data);
    }

    public function deleteOneRecord($where){
        return $this->insideCountSell->where($where)->delete();
    }

}
