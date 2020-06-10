<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 15:16
 */

namespace App\Server\UserServers\Dao;


use App\Model\BankCardVerify;

class BankCardVerifyDao extends BankCardVerify
{


    public function getOneRecord(int $userId,$cardNo)
    {
        return $this->where(['user_id'=>$userId,'verify_card_no'=>$cardNo,'is_usable'=>1])->first();
    }

    public function getRecordByUserId(int $userId)
    {
        return $this->where(['user_id'=>$userId,'is_usable'=>1])->first();
    }

    /*获取一张银行卡*/
    public function getOneBankCard(int $userId)
    {
        return $this->with('bankName','userPhone')->where(['user_id'=>$userId,'is_usable'=>1])->first();
    }

    /*获取一张银行卡*/
    public function getOneBankCardById(int $verifyId)
    {
        return $this->with('bankName','userPhone')->find($verifyId);
    }

    /*获取用户的所有银行卡*/
    public function getAllCardsByUserId(int $userId)
    {
        return $this->with('bankName','userPhone')->where(['user_id'=>$userId,'is_usable'=>1])->get()->toArray();
    }

    public function saveOneRecord(array $data)
    {

        $this->verify_name = $data['verify_name'];
        $this->verify_card_no = $data['verify_card_no'];
        $this->bank_id = $data['bank_id'];
        $this->user_id = $data['user_id'];
        $this->verify_phone = $data['verify_phone'];

        return $this->save();

    }

    /*update*/
    public function updateOneRecord($where,$data)
    {
        return $this->where($where)->update($data);

    }

    /*模型关联--银行名*/
    public function bankName()
    {
        return $this->hasOne('App\Model\BankList','bank_id','bank_id')->select('bank_id','bank_cn_name');
    }

    /*用户手机*/
    public function userPhone()
    {
        return $this->hasOne('App\Model\User','user_id','user_id')->select('user_id','user_phone');
    }

    /*用户姓名*/
    public function userName()
    {
        return $this->hasOne('App\Model\UserIdentify','user_id','user_id')->select('user_id','identify_name');
    }

}