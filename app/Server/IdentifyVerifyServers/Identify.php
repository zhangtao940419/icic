<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5
 * Time: 14:41
 */

namespace App\Server\IdentifyVerifyServers;


use App\Server\IdentifyVerifyServers\Interfaces\IdentifyInterface;
use App\Server\IdentifyVerifyServers\Servers\CLIdentifyServer;

class Identify
{
    private $provider;

    public function __construct()
    {
        $this->provider = new CLIdentifyServer();
    }

    public function setProvider(IdentifyInterface $identify)
    {
        $this->provider = $identify;
        return $this;
    }

    public function verify($name,$idCard,$sex = 0)
    {
        return $this->provider->verify($name,$idCard,$sex);
    }

}