<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/25
 * Time: 17:09
 */

namespace App\Server\SMSServers;

use App\Server\SMSServers\Interfaces\SMSInterface;
use App\Server\SMSServers\Servers\CL253Server;

class SMS
{

    public $sMSServer;

    public function __construct()
    {
        if (!isset($this->sMSServer)){
            $this->sMSServer = (new CL253Server());
        }
    }

    public function setProvider(SMSInterface $sMSServer)
    {
        $this->sMSServer = $sMSServer;
        return $this;
    }


    public function setPhone($phone)
    {
        $this->sMSServer = $this->sMSServer->setPhone($phone);
        return $this;
    }


    public function setMsg($msg)
    {
        $this->sMSServer = $this->sMSServer->setMsg($msg);
        return $this;
    }

    public function setCode($code)
    {
        $this->sMSServer = $this->sMSServer->setCode($code);
        return $this;
    }

    public function setSignature($signature)
    {
        $this->sMSServer = $this->sMSServer->setSignature($signature);
        return $this;
    }

    public function send($phone = '',$msg = '')
    {
        if (!isset($this->sMSServer)){
            $this->sMSServer = (new CL253Server());
        }
        return $this->sMSServer->send($phone,$msg);
    }

    public function sendCodeMsg($phone,$code)
    {
        if (!isset($this->sMSServer)){
            $this->sMSServer = (new CL253Server());
        }
        $this->sMSServer->setMsg('');
        $this->sMSServer->setCode($code);
        return $this->sMSServer->send($phone);

    }

    public function sendVariableMsg($msg,$params)
    {
        return $this->sMSServer->sendVariableSMS($msg,$params);
    }


}