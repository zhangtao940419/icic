<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/25
 * Time: 17:03
 */
namespace App\Server\SMSServers\Servers;

use App\Server\SMSServers\Interfaces\SMSInterface;

header("Content-type:text/html; charset=UTF-8");

class HQYServer implements SMSInterface
{

    protected $uid = '3324';
    protected $pw = '530010';

    private $msg;

    private $signature = '鸿庆云通讯';

    private $code;

    private $phone;
    private $postArr;

    private $template = '亲爱的用户，您的短信验证码为%s，在5分钟内有效，若非本人操作请忽略。';
    private $template1 = 'Verification code %s, will be expired in 5 minutes. (Please make sure this request is raised by yourself and the mobile is belongs to you, otherwise please ignore this text.)';


    public function __construct()
    {

    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
        // TODO: Implement setPhone() method.
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
        // TODO: Implement setCode() method.
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
        // TODO: Implement setMsg() method.
    }

    public function setSignature($signature)
    {
        if (strlen((string)$signature) < 0 || strlen((string)$signature) > 20){
            throw new \Exception();
        }
        $this->signature = $signature;
        return $this;
        // TODO: Implement setSignature() method.
    }

    //发送 1成功 0失败
    public function send($phone = '',$msg = '')
    {
        if ($phone) $this->phone = $phone;
        if ($msg) $this->msg = $msg;

        if (!$this->msg && !$this->code) return 0;
        if (!$this->phone) return 0;

        $content = $this->msg ? $this->msg : sprintf($this->template,$this->code);
        $msg = '【' . $this->signature . '】' . $content;
        $dateP = date('YmdHis');
        $this->postArr = array (
            'uid'  =>  $this->uid,
            'pw' => md5($this->pw.$dateP),
            'mb' => (string)$this->phone,
            'ms' => $msg,
//            'ex' => '77',
            'tm' => $dateP

        );
//dd(file_get_contents("http://39.100.94.222:18002/send.do?uid=3324&pw=$pw&mb=15574832499&ms=【测试】你好&ex=77&tm=$dateP"));
        $result = $this->exec();
        switch ($result){
            case 0:
                return 0;
                break;
            case 1:
                return 1;
                break;
        }
        // TODO: Implement send() method.

    }


    private function exec()
    {

        $url = 'http://39.100.94.222:18002/send.do';


        foreach ($this->postArr as $k=>$item){
            if (strpos($url,'?') === false){
                $url .= '?' . $k . '=' . $item;
            }else{
                $url .= '&' . $k . '=' . $item;
            }
        }


        $re = file_get_contents($url);//dd($re);

        if (strpos($re,',') !== false) return 1;return 0;

    }

}