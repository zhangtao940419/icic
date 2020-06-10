<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5
 * Time: 13:53
 */

namespace App\Server\IdentifyVerifyServers;


use App\Model\BankList;
use App\Server\IdentifyVerifyServers\Interfaces\BankCardInterface;
use App\Server\IdentifyVerifyServers\Servers\CLBankCardServer;

class BankCard
{

    private $provider;


    public function __construct()
    {
        $this->provider = new CLBankCardServer();
    }

    public function setProvider(BankCardInterface $provider)
    {
        $this->provider = $provider;
        return $this;
    }

    //sanyaosu
    public function verify($name,$idCard,$cardNo)
    {
        return $this->provider->verify($name,$idCard,$cardNo);
    }

    //查询银行卡所属银行bank_id
    public function checkBankCardId($cardNo)
    {
        $api = 'https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo='. $cardNo .'&cardBinCheck=true';


        $re = file_get_contents($api);
        $re = json_decode($re,true);

        if (!$re['validated']) return '银行卡有误';

        $bank = (new BankList())->getBankByEnCode($re['bank']);

        if(!$bank) return '不支持的银行卡';

        return $bank->bank_id;
    }



}