<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 17:34
 */

namespace App\Server\MessagePushServers;

use App\Server\MessagePushServers\Interfaces\MessagePushInterface;
use App\Server\MessagePushServers\Servers\JiGuangIMServer;

class MessagePush
{

    private $server;

    public function __construct()
    {
        $this->server = new JiGuangIMServer();
    }


    public function setProvider(MessagePushInterface $server)
    {
        $this->server = $server;
        return $this;
    }

    public function test()
    {
        dd(1);
    }

    public function register($userName, $password)
    {
        return $this->server->register($userName,$password);
    }


}