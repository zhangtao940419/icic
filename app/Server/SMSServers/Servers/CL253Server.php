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

class CL253Server implements SMSInterface
{

    private $msg;

    private $signature = '253云通讯';

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

        $this->postArr = array (
            'account'  =>  env('MSG_ACCOUNT'),
            'password' => env('MSG_PASSWORD'),
            'msg' => urlencode($msg),
            'phone' => $this->phone,
            'report' => true
        );

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

    /**
     * 发送变量短信
     *
     * @param string $msg 			短信内容
     * @param string $params 	最多不能超过1000个参数组
     */
    public function sendVariableSMS( $msg, $params) {
        //创蓝接口参数
        $this->postArr = array (
            'account'  =>  env('MSG_ACCOUNT'),
            'password' => env('MSG_PASSWORD'),
            'msg' => '【' . $this->signature . '】' . $msg,
            'params' => $params,
            'report' => 'true'
        );
//dd($params);
        $result = $this->exec( );
        return $result;
    }

    private function exec()
    {

//dd($msg);
            //创蓝接口参数
        $url = env('MSG_SEND_URL');

        //var_dump($postArr);die();

//dd($this->postArr);
        $postFields = json_encode($this->postArr);

        $ch = curl_init ();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'   //json版本需要填写  Content-Type: application/json;
            )
        );
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,60);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );//dd($result);
        if (\GuzzleHttp\json_decode($result,true)['code'] == 0) return 1;
        return 0;

    }

}