<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 17:31
 */

namespace App\Server\MessagePushServers\Servers;

use App\Server\MessagePushServers\Interfaces\MessagePushInterface;
use JMessage\IM\User;
use JMessage\JMessage;

class JiGuangIMServer implements MessagePushInterface
{

    private $userServer;
    private $appKey = '71ddeab6cfff8612b19b33a0';
    private $masterSecret = '0e2b25b7765a73f098177289';
    public function __construct()
    {
        $this->userServer = new User(new JMessage($this->appKey,$this->masterSecret));
    }

    /**
     * @param array $userMsg
     * 单个用户注册
     */
    public function register($userName,$password)
    {
        // TODO: Implement register() method.
        $response = $this->userServer->register($userName, $password);//dd($response);
        if (isset($response['body'][0]['error'])) return false;
        return true;
    }

}