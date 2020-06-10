<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 10:37
 */

namespace App\Server\UserServers\Interfaces;

interface LoginServerInterface
{

    public function login($data);


    public function logout();

}