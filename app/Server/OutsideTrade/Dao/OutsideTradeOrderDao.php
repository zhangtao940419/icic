<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 11:36
 */

namespace App\Server\OutsideTrade\Dao;


use App\Model\CoinType;
use App\Model\OutsideTradeOrder;
use App\Model\User;
use App\Model\WorldCurrency;

class OutsideTradeOrderDao extends OutsideTradeOrder
{

    private $insert_fields=['order_number','trade_id','user_id','trade_user_id','order_fee',
        'currency_id','coin_id','order_coin_num','order_total_money','order_price','order_type','order_status'];


    public function saveOrder($data)
    {
        foreach ($data as $key=>$value){

            if(in_array($key,$this->insert_fields)){
                $this->$key=$value;
            }
        }
        if(!$this->save()) return 0;
        return $this->order_id;
    }


    public function getRecord($orderId,$column = ['*'],$lock = 0)
    {
        if ($lock)
            return $this->lockForUpdate()->find($orderId,$column);
        return $this->find($orderId,$column);
    }


    public function getRecords($where,$column = ['*'])
    {
        return $this->where($where)->get($column);
    }

    public function getWithOtherOrders($userId,$targetUserId)
    {//dd(1);
        $q1 = $this->with([
        'trade'=>function($q){
            $q->select(['trade_id','get_money_type']);
        },
        'orderUser','tradeUser','coin','currency'
    ])->where(['user_id'=>$userId,'trade_user_id'=>$targetUserId]);
        return $this->with([
            'trade'=>function($q){
                $q->select(['trade_id','get_money_type']);
            },
            'orderUser','tradeUser','coin','currency'
        ])->where(['user_id'=>$targetUserId,'trade_user_id'=>$userId])->union($q1)->latest()->get();

    }

    public function getMyOrders($userId,$status)
    {//1jinx2wanc3quxiao
        switch ($status){
            case 1:
                $status = [1,2];
                break;
            case 2:
                $status = [3];
                break;
            case 3:
                $status = [-1,0];
                break;
        }

        return $this->with([
            'trade'=>function($q){
                $q->select(['trade_id','get_money_type']);
            },
            'orderUser','tradeUser','coin','currency'
        ])->whereIn('order_status',$status)->where(function ($q) use($userId){
            $q->where('user_id',$userId)->orWhere('trade_user_id',$userId);
        })->latest()->get();


    }

    public function getOrderDetail($userId,$orderId)
    {
        return $this->with([
            'trade'=>function($q){
                $q->select(['trade_id','get_money_type','trade_des']);
            },
            'orderUser','tradeUser','coin','currency'
        ])->where('order_id',$orderId)->where(function ($q) use($userId){
            $q->where('user_id',$userId)->orWhere('trade_user_id',$userId);
        })->first();
    }

    public function getTradeOrderList($tradeId)
    {
        return $this->with('orderUser','coin','currency')->where(['trade_id'=>$tradeId,'order_status'=>3])->get();

    }

    public function trade()
    {
        return $this->hasOne(OutsideTrade::class,'trade_id','trade_id');
    }

    public function orderUser()
    {
        return $this->hasOne(User::class,'user_id','user_id')->select(['user_id','user_name','user_headimg']);
    }

    public function coin()
    {
        return $this->hasOne(CoinType::class,'coin_id','coin_id')->select(['coin_id','coin_name']);
    }

    public function currency()
    {
        return $this->hasOne(WorldCurrency::class,'currency_id','currency_id')->select(['currency_id','currency_code']);
    }

    public function tradeUser()
    {
        return $this->hasOne(User::class,'user_id','trade_user_id')->select(['user_id','user_name','user_headimg']);
    }


}