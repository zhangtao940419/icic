<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/31
 * Time: 10:43
 */

namespace App\Server\IdentifyVerifyServers;


class YiDunVerifyServer
{

    public $secretId = "2e0f0c74500fdce8d589b3c5231b35fa";
    public $secretKey = "36a5a75987645f4310f579f18771cd7f";
    public $businessId = "d0d9e82becd24a6a8ab3b83feec1f920";
    public $api_url = "https://verify.dun.163yun.com/v1/rp/check";
    public $version = "v1";
    public $api_timeout = 2;
    public $internal_string_charset = "auto";



    /**
     * 计算参数签名
     * $params 请求参数
     * $secretKey secretKey
     */
    public function gen_signature($secretKey, $params){
        ksort($params);
        $buff="";
        foreach($params as $key=>$value){
            if($value !== null) {
                $buff .=$key;
                $buff .=$value;
            }
        }
        $buff .= $secretKey;
        return md5($buff);
    }

    /**
     * 将输入数据的编码统一转换成utf8
     * @params 输入的参数
     */
    public function toUtf8($params){
        $utf8s = array();
        foreach ($params as $key => $value) {
            $utf8s[$key] = is_string($value) ? mb_convert_encoding($value, "utf8", $this->internal_string_charset) : $value;
        }
        return $utf8s;
    }

    /**
     * 易盾身份认证服务身份证实人认证在线检测请求接口简单封装
     * $params 请求参数
     */
    public function check($params){

        list($t1, $t2) = explode(' ', microtime());
        $mic = (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
//echo($mic);exit;

        $params["secretId"] = $this->secretId;
        $params["businessId"] = $this->businessId;
        $params["version"] = $this->version;
        $params["timestamp"] = $mic;
        //$params["timestamp"] = sprintf("%d", round(microtime(true)*1000));// time in milliseconds
        $params["nonce"] = sprintf("%d", rand()); // random int

        $params = $this->toUtf8($params);
        $params["signature"] = $this->gen_signature($this->secretKey, $params);
        // var_dump($params);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'timeout' => $this->api_timeout, // read timeout in seconds
                'content' => http_build_query($params),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($this->api_url, false, $context);
        if($result === FALSE){
            return array("code"=>500, "msg"=>"file_get_contents failed.");
        }else{
            return json_decode($result, true);
        }
    }

    // 简单测试
    public function main($name,$cardNo){
        //echo "mb_internal_encoding=".mb_internal_encoding()."\n";
        $params = array(
            "name"=>$name,
            "cardNo"=>$cardNo,
            "picType"=>"1",
            "avatar"=>"http://img.zcool.cn/community/0117e2571b8b246ac72538120dd8a4.jpg@1280w_1l_2o_100sh.jpg",
            "callback"=>"ebfcad1c-dba1-490c-b4de-e784c2691768",
        );

        $ret = $this->check($params);
        //header('Content-type: application/json');
        if ($ret['code'] == 500) return 0;
        if (($ret['code'] == 200) && ($ret['result']['status'] == 1)) return 1;
        return 2;
//        if ($ret["code"] == 200) {
//            $status= $ret["result"]["status"];
//            $taskId = $ret["result"]["taskId"];
//            $reasonType = $ret["result"]["reasonType"];
//            if ($status == 1) {
//                $similarityScore = $ret["result"]["similarityScore"];
//                echo "taskId={$taskId}，姓名身份证认证通过, 头像相似度得分={$similarityScore}\n";
//            } else if ($status == 2) {
//                echo "taskId={$taskId}，姓名身份证认证不通过，不通过原因：".$reasonType."\n";
//            }
//        }else{
//            //var_dump($ret); // error handler
//        }
    }



}