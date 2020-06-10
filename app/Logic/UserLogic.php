<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 13:48
 */

namespace App\Logic;

use App\Server\SMSServers\Servers\DXBServer;
use App\Server\UserServers\Login;
use App\Server\UserServers\Register;
use App\Server\UserServers\Servers\ZTLoginServer;
use App\Server\UserServers\Servers\ZTRegisterServer;
use App\Traits\RedisTool;
use App\Server\UserServers\Dao\UserDao;

class UserLogic
{
    use RedisTool;
    private $register;

    private $login;

    private $userDao;

    public function __construct()
    {
        $this->login = new Login(new ZTLoginServer());
        $this->register = new Register(new ZTRegisterServer());
        $this->userDao = new UserDao();
    }

    public function sendCodeSMS($phone)
    {

        $code = rand(100000,999999);

        if ($this->redisExists($phone)) return 0;//重复发送

        $smsProvider = $this->stringGet('sms_provider') ? $this->stringGet('sms_provider') : 1;//1创蓝2短信宝
        switch ($smsProvider){
            case 1:
                $re = app('sms')->setSignature('TTS')->sendCodeMsg($phone,$code);
                break;
            case 2:
                $re = app('sms')->setProvider(new DXBServer())->setSignature('LOVE')->sendCodeMsg($phone,$code);
                break;
        }
        if (
            //
            $re
            && $this->stringSetex($phone,300,"{$code}")
        ) return 1;//成功
        return 2;//失败

    }

    public function checkRegisterCode($phone,$code)
    {
        return $this->register->checkRegisterCode($phone,$code);
    }


    //注册
    public function register(array $data)
    {

        $key = 'REGISTER_'.$data['phone'];

        if (! $this->setKeyLock($key,5)) return -1;

        if (! $result = $this->checkCode($data['phone'],$data['code'])){
            $this->redisDelete($key);
            return -2;
        }elseif ($result == 2){
            $this->redisDelete($key);
            return -3;
        }
//dd($this->register->saveUserMsg($data,1));
        return $this->register->saveUserMsg($data,1);

    }


    //登录
    public function login($data)
    {

        if (! $result = $this->checkCode($data['phone'],$data['code'])){
            return -1;//请重新发送验证码
        }elseif ($result == 1){
            return $this->login->login($data,1);//正确
        }else{
            return -2;//错误
        }

    }

    public function logout()
    {

        return $this->login->logout();
    }


    //找回密码检测验证码
    public function verifyPasswordCode($phone,$code)
    {
        if (!$this->userDao->getOneDataByPhone($phone)) return 0;//bu存在手机号

        if (! $result = $this->checkCode($phone,$code)){
            return 1;//请重新发送验证码
        }elseif ($result == 1){
            return 2;//正确
        }else{
            return 3;//错误
        }

    }



    /*找回密码*/
    public function retrievePassword($data)
    {
        return $this->login->retrievePassword($data);
    }





}