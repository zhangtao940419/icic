<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 10:51
 */
namespace App\Server\UserServers\Dao;

use App\Model\User;

class UserDao extends User
{

//    public $userModel;
//
//    public function __construct()
//    {
//        $this->userModel = new User();
//
//    }

    public function getOneDataByPhone($phone)
    {
        return $this->getOneData(['user_phone' => $phone], ['user_id']);

    }

    public function saveUserMsg($data)
    {

        if ($this->getUserByPhone($data['phone'])) return 0;
        $this->user_name = $data['phone'];
        $this->user_phone = $data['phone'];
        $this->user_password = $this->passwordHash($data['password']);
        $this->user_headimg = '/app/head_image/head_default.png';
        if (isset($data['pid'])) $this->pid = $data['pid'];
        $this->user_Invitation_code = $data['user_Invitation_code'];
        $this->user_reg_ip = $data['user_reg_ip'];
        $this->is_new = $data['is_new'];
        if ($this->save()) return $this;
        return 0;

    }

    public function updatePasswordByPhone($phone,$password)
    {
        return $this->where('user_phone',$phone)->update(['user_password'=>$this->passwordHash($password)]);
    }

    public function updatePasswordByUserId($userId,$password)
    {
        return $this->where('user_id',$userId)->update(['user_password'=>$this->passwordHash($password)]);
    }
    public function updatePayPasswordByUserId($userId,$password)
    {
        return $this->where('user_id',$userId)->update(['user_pay_password'=>$this->passwordHash($password)]);
    }

    public function setPayPassword($userId,$payPassword)
    {
        return $this->where('user_id',$userId)->update(['user_pay_password'=>$this->passwordHash($payPassword)]);
    }

    public function setEmail($userId,$eamil)
    {
        return $this->where('user_id',$userId)->update(['user_email'=>$eamil]);
    }

    public function verifyUserPassword($userId,$password)
    {
        $realPassword = $this->getUserById($userId,['user_password'])->user_password;
        return $this->verifyPassword($password,$realPassword);
    }

    public function verifyPayPassword($userId,$password)
    {
        $realPassword = $this->getUserById($userId,['user_pay_password'])->user_pay_password;
        return $this->verifyPassword($password,$realPassword);
    }

    /*获取用户设置的相关信息*/
    public function getUserSettingMsg(int $userId)
    {
        return $this->select('user_phone','user_pay_password','user_email','user_auth_level')->where('user_id',$userId)->first();
    }

    /*更新一条*/
    public function updateOneData($condition,$data)
    {
        return $this->where($condition)->update($data);
    }


    public function passwordHash($password)
    {
        return password_hash($password,PASSWORD_DEFAULT);
    }

    public function verifyPassword($password,$pHash)
    {
        return password_verify($password,$pHash);
    }










}