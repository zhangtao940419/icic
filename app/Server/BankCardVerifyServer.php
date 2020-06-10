<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 10:16
 */

namespace App\Server;

use App\Traits\RedisTool;
use App\Model\User;
use App\Model\BankCardVerify;
use App\Server\IdentifyVerifyServers\JuHeBankCardServer;
use App\Model\BankList;
use App\Model\UserIdentify;
use Illuminate\Support\Facades\DB;


class BankCardVerifyServer
{
    //银行卡绑定server
    use RedisTool;

    private $user;
    private $bankCardVerify;
    private $bankCardServer;
    private $bankList;
    private $userIdentify;

    public function __construct()
    {
        $this->user = new User();
        $this->bankCardVerify = new BankCardVerify();
        $this->bankCardServer = new JuHeBankCardServer();
        $this->bankList = new BankList();
        $this->userIdentify = new UserIdentify();
    }

    /*初级认证银行卡验证*/
    public function verify($userId,$phone,$code,$name,$idCard,$cardNo,$bankId)
    {
        $user = $this->user->getUserById($userId);
//        if (!$user || ($user->user_phone != $phone)) return 0;

        if (! $result = $this->checkCode($phone,$code)){
            return 1;//重新发送
        }elseif ($result != 1){
            return 2;//不正确
        }

        if ($user->user_auth_level > 0) return 3;
        if ($this->userIdentify->getOneRecordByCard($idCard)) return 4;

        //if ($this->bankCardVerify->getRecordByUserId($userId)) return 3;//重复绑定

        $bank = $this->bankList->getRecordById($bankId);//银行信息

        $result = $this->bankCardServer->verify($name,$idCard,$cardNo,$phone);//juhe返回的状态码
        if (!$result) return 0;//verifyfail

        switch ($result['data']['result']){
            case '05':
                if (! $this->redisExists('PRIMARY_AUTH_LIMIT_'.$userId))
                    $this->stringSetex('PRIMARY_AUTH_LIMIT_'.$userId,43200,1);
                $this->setIncrement('PRIMARY_AUTH_LIMIT_'.$userId,1);
                return 5;//手机号不匹配
                break;
        }
//        if ($result['data']['bankName'] != $bank['bank_cn_name']){
//            if (! $this->redisExists('PRIMARY_AUTH_LIMIT_'.$userId))
//                $this->stringSetex('PRIMARY_AUTH_LIMIT_'.$userId,43200,1);
//            $this->setIncrement('PRIMARY_AUTH_LIMIT_'.$userId,1);
//            return 6;//银行名有误
//        }
        if ($result['data']['result'] != '01'){
            if (! $this->redisExists('PRIMARY_AUTH_LIMIT_'.$userId))
                $this->stringSetex('PRIMARY_AUTH_LIMIT_'.$userId,43200,1);
            $this->setIncrement('PRIMARY_AUTH_LIMIT_'.$userId,1);
            return 7;
        }//验证不通过


        DB::beginTransaction();
            if (
                $user->updateOneRecord($userId,['user_auth_level'=>1])
                && $this->userIdentify->saveOneRecord(['identify_name'=>$name,'identify_card'=>$idCard,'user_id'=>$userId])
                && $this->bankCardVerify->saveOneRecord(['verify_name'=>$name,'verify_card_no'=>$cardNo,'bank_id'=>$bankId,'user_id'=>$userId])
            ){
                DB::commit();return 8;//成功
            }
            DB::rollBack();return 0;//操作失败


        //验证逻辑

    }


    /*更换银行卡逻辑*/
    public function changeBankCard($userId,$phone,$code,$name,$idCard,$cardNo,$bankId)
    {
        if (! $result = $this->checkCode($phone,$code)){
            return 1;//重新发送
        }elseif ($result != 1){
            return 2;//不正确
        }

        $user = $this->userIdentify->getOneRecordC($userId,$name,$idCard);
        if (!$user) return 3;//身份信息不对

        if ($this->bankCardVerify->getOneRecord($userId,$cardNo)) return 4;//请更换银行卡

        $result = $this->bankCardServer->verify($name,$idCard,$cardNo,$phone);//juhe返回的状态码
        if (!$result) return 0;

        switch ($result['data']['result']){
            case '05':
                if (! $this->redisExists('BANKCARD_CHANGE_LIMIT_'.$userId))
                    $this->stringSetex('BANKCARD_CHANGE_LIMIT_'.$userId,43200,1);
                $this->setIncrement('BANKCARD_CHANGE_LIMIT_'.$userId,1);
                return 5;//手机号不匹配
                break;
        }

        if ($result['data']['result'] != '01'){
            if (! $this->redisExists('BANKCARD_CHANGE_LIMIT_'.$userId))
                $this->stringSetex('BANKCARD_CHANGE_LIMIT_'.$userId,43200,1);
            $this->setIncrement('BANKCARD_CHANGE_LIMIT_'.$userId,1);
            return 7;
        }//验证不通过

        DB::beginTransaction();
        if (
            $this->bankCardVerify->updateOneRecord(['user_id'=>$userId,'is_usable'=>1],['verify_card_no'=>$cardNo,'bank_id'=>$bankId])
        ){
            DB::commit();return 8;//成功
        }
        DB::rollBack();return 0;//操作失败



    }

}