<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/15
 * Time: 14:43
 */

namespace App\Server\Investment\Dao;

use App\Model\WalletDetail;

class UserWalletDao extends  WalletDetail
{

    //获取用户的余额
    public function getUserCoinBalance($coin_id,$user_id){

     return  $this->getWalletBalance($coin_id,$user_id);

    }

    //减少用户可提现余额
    public function reduceUserWithdrawBalance($coin_id,$user_id,$reduce_money){

        return $this->reduceWithdrawBalance($coin_id,$user_id,$reduce_money);

    }


}