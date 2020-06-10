<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5
 * Time: 11:01
 */

namespace App\Server\IdentifyVerifyServers\Servers;

use App\Server\IdentifyVerifyServers\Interfaces\BankCardInterface;

class CLBankCardServer implements BankCardInterface
{

    //银行卡三要素

    private $url;
    private $appId;
    private $appKey;

    private $name;
    private $idCard;
    private $cardNo;
    public function __construct()
    {
        $this->url = env('BC_URL','https://api.253.com/open/bankcard/card-three-auth');
        $this->appId = env('BC_APPID','t56h7mvd');
        $this->appKey = env('BC_APPKEY','UNpx5HO0');
    }

    private function demo($name,$idCard,$cardNo)
    {
        try {

            $params = [
                'appId' => $this->appId, // appId,登录万数平台查看
                'appKey' => $this->appKey, // appKey,登录万数平台查看
                'name' => $name, // 姓名
                'idNum' => $idCard, // 身份证号码，限单个
                'cardNo' => $cardNo, // 银行卡号，限单个
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

        $result = $this->demo($this->name,$this->idCard,$this->cardNo);

        if ($result == 0) return 0;
//dd($result);
        if ($result['code'] == 200000 && $result['data']['result'] == '01') return 1;return 0;

        //return $result['data']['result'];//认证结果。01：一致 02：不一致 03：认证不确定 04：认证失败。01、02收费
    }


    public function verify($name,$idCard,$cardNo)
    {
        $this->name = $name;
        $this->idCard = $idCard;
        $this->cardNo = $cardNo;
        return $this->exec();

    }

}