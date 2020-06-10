<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    protected $primaryKey = 'id';
    /*
     * 与模型关联的数据表
     * @var string
     */
    protected $table = 'user_coin_trade_history';


    /* 增加用户特定虚拟币的交易记录
     *
     *
     */
    public function addCoinTradeHistroy($user_id,$coin_id){

   return $this->where(['user_id'=>$user_id,'coin_id'=>$coin_id,'is_usable'=>1])->increment('coin_trade_num');

    }

    /* 添加用户特定虚拟币的交易历史记录
     *
     *
     */
    public function saveCoinTradeHistroy($user_id,$coin_id){
        $this->coin_id =$coin_id;
        $this->user_id =$user_id;
        $this->coin_trade_num =1;
        if($this->save()) return 1;
        return 0;
    }


     /* 查询是否有相关的记录
      *
      *
      *
      */
     public function hasHistroyRecoder($user_id,$coin_id){

            return $this->where(['user_id'=>$user_id,'coin_id'=>$coin_id,'is_usable'=>1])->count();

     }



}

