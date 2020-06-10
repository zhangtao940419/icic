<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 10:46
 */
namespace App\Server\UserServers;

use App\Server\UserServers\Interfaces\LoginServerInterface;

class Login
{

    private $loginServer;

    public function __construct(LoginServerInterface $loginServer)
    {
        $this->loginServer = $loginServer;

    }

    public function login($data,$single = 0)
    {

        return $this->loginServer->login($data,$single);
    }

    public function logout()
    {
        return $this->loginServer->logout();
    }

    public function retrievePassword($data)
    {
        return $this->loginServer->retrievePassword($data);
    }



}