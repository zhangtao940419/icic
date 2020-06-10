<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 10:49
 */

namespace App\Server\UserServers;


use App\Server\UserServers\Interfaces\RegisterServerInterface;

class Register
{

    private  $registerServer;
    public function __construct(RegisterServerInterface $registerServer)
    {
        $this->registerServer = $registerServer;
    }


    public function checkRegisterCode($phone,$code)
    {
        return $this->registerServer->checkRegisterCode($phone,$code);
    }

    public function saveUserMsg($data,$single = 0)
    {
        return $this->registerServer->saveUserMsg($data,$single);
    }




}