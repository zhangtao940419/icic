<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/29
 * Time: 15:12
 */

namespace App\Server\SMSServers\Servers;


use App\Server\SMSServers\Interfaces\SMSInterface;

class DXBServer implements SMSInterface
{

    private $msg;

    private $signature = '短信宝';

    private $code;

    private $phone;

    private $template = 'Verification code %s, will be expired in 5 minutes. (Please make sure this request is raised by yourself and the mobile is belongs to you, otherwise please ignore this text.)';


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

        $result = $this->exec();
        switch ($result){
            case 0:
                return 0;//失败
                break;
            case 1:
                return 1;//成功
                break;
        }
        // TODO: Implement send() method.

    }

    private function exec($needStatus = true)
    {
        return $this->newMsg($this->phone,$this->code);

    }

    // 短信宝 短信发送接口
// api 参见 https://www.showdoc.cc/web/#/1621091?page_id=14901087
    private function newMsg($mobile, $code)
    {
        $config = $this->sms_config($mobile);
        if ($config["cn"]) {
            $content = $config["sign"] . "你的验证码是" . $code . ",如非本人操作，请忽略本短信。";
        } else {
            $content = utf8_encode($config["sign"] . "Your verification code is " . $code . ",valid within 5 minutes. thank you.");
        }

        $sendurl = $config["url"] . "?u=" . $config['account'] . "&p=" . md5($config['password']) . "&m=" . $config["mobile"] . "&c=" . urlencode($content);//dd($sendurl);
        $result = file_get_contents(trim($sendurl));
//dd($result);
        if (isset($result) && $result == '0') {
            return 1;
        } else {
            return 0;
        }
    }


    //短信宝 短信配置
    private function sms_config($mobile)
    {
//        if (strlen($mobile) == 13 && strpos($mobile, "86") == 0) {
            $data["url"] = "https://api.smsbao.com/sms";
            $data["account"] = 'BT2019';
            $data["password"] = '98210201225';
            $data["mobile"] = $mobile;
            $data["sign"] = '【'.$this->signature.'】';
            $data["cn"] = true;
//        } else {
//            $data["url"] = " https://api.smsbao.com/wsms";
//            $data["account"] = 'BT2019';
//            $data["password"] = '98210201225';
//            $data["mobile"] = $mobile;
//            $data["sign"] = '【'.$this->signature.'】';
//            $data["cn"] = false;
//        }

        return $data;
    }


}