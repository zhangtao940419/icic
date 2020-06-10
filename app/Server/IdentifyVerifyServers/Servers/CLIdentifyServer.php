<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5
 * Time: 14:31
 */

namespace App\Server\IdentifyVerifyServers\Servers;


use App\Server\IdentifyVerifyServers\Interfaces\IdentifyInterface;

class CLIdentifyServer implements IdentifyInterface
{


    //银行卡三要素

    private $url;
    private $appId;
    private $appKey;

    private $name;
    private $idCard;
    private $sex;
    public function __construct()
    {
        $this->url = env('IDCARD_URL','https://api.253.com/open/bankcard/card-three-auth');
        $this->appId = env('IDCARD_APPID','t56h7mvd');
        $this->appKey = env('IDCARD_APPKEY','UNpx5HO0');
    }

    private function demo($name,$idCard)
    {
        try {

            $params = [
                'appId' => $this->appId, // appId,登录万数平台查看
                'appKey' => $this->appKey, // appKey,登录万数平台查看
                'name' => $name, // 姓名
                'idNum' => $idCard, // 身份证号码，限单个
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $result = curl_exec($ch);
            return \GuzzleHttp\json_decode($result, true);
        }catch (\Exception $exception){
            return 0;
        }
    }


    public function exec()
    {

        $result = $this->demo($this->name,$this->idCard);

        if ($result == 0) return 0;
//dd($result);
        if ($result['code'] == 200000 && $result['data']['result'] == '01'){
            if ($this->sex){
                if ($result['data']['gender'] == $this->sex) return 1;return 0;
            }
            return 1;
        }
        return 0;

        //return $result['data']['result'];//认证结果。01：一致 02：不一致 03：认证不确定 04：认证失败。01、02收费
    }


    public function verify($name,$idCard,$sex)
    {
        $this->name = $name;
        $this->idCard = $idCard;
        $this->sex = $sex;
        return $this->exec();
    }




}