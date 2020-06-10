<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 17:24
 */

namespace App\Server\MessagePushServers\Interfaces;

interface MessagePushInterface
{

    public function register($userName, $password);//单个用户注册


}