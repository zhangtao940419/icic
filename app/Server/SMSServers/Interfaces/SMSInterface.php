<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/25
 * Time: 16:55
 */
namespace App\Server\SMSServers\Interfaces;

interface SMSInterface
{

    //设置信息内容
    public function setMsg($msg);

    //设置验证码
    public function setCode($code);

    //设置签名
    public function setSignature($signature);

    //设置手机
    public function setPhone($phone);

    //发送
    public function send();




}