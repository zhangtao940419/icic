<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 14:50
 */
namespace App\Traits;

use App\Model\OutsideWalletDetail;
use App\Model\WalletDetail;
use App\Model\XYModel\UserWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Jobs\SendEmail;
use  Carbon\Carbon;
use App\Server\IdentifyVerifyServer;
use App\Server\BankCardVerifyServer;
use Illuminate\Support\Facades\Mail;

trait Tools
{


    /**
     * 二维数组根据某个字段排序
     * @param array $array 要排序的数组
     * @param string $keys   要排序的键字段
     * @param string $sort  排序类型  SORT_ASC     SORT_DESC
     * @return array 排序后的数组
     */
    function arraySort($array, $keys, $sort = SORT_DESC) {
        $keysValue = [];
        foreach ($array as $k => $v) {
            $keysValue[$k] = $v[$keys];
        }
        array_multisort($keysValue, $sort, $array);
        return $array;
    }

    /*自定义验证错误*/
    public function verifyField($input,$rules){

        $messages = [
            'required'   => '缺少必要字段:attribute.',
            'min'        => ':attribute字段的值不能低于:min',
            'image'      => ':attribute字段的值必须是图片',
            'alpha_dash' => ':attribute验证字段值是否仅包含字母、数字、破折号（ - ）以及下划线（ _ ）',
            'integer'    => ':attribute字段值必须是整数',
            'string'     => ':attribute字段值必须是字符串',
            'present'    => ':attribute字段必须出现，并且数据可以为空',
            'email'      => ':attribute字段格式不正确',
            'max'		=>':attribute字段的值不能高于:max',
            'size'		=>':attribute字段需要18位长度',
            'digits'		=>':attribute格式有错',
            'alpha'		=>':attribute必须是字符',
            'in'		=>':attribute不在范围内',
            'mimes'		=>'上传文件类型不符合要求',
            'regex' => ':attribute 不合规范'
        ];


        $errors = Validator::make($input,$rules, $messages)->errors();

        foreach($errors->all() as $message)  {
            return ['status_code'=>1004,'message'=>$message,'state'=>1];
        }

    }

    /*生成邀请码*/
    public function createInviteCode($len = 8)
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < $len;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        return $d;

    }

    /*获取货币id*/
    public function getCoinId($coinName)
    {
        $result = DB::table('fictitious_coin_type')->where('coin_name',$coinName)->first();
        if ($result) return $result->coin_id;
        return 0;
    }


    /*发送邮箱验证码*/
    public function sendEmailMessage($emailAddress,$emailMessage)
    {
        Mail::send('email', ['emailMessage' => $emailMessage], function($message) use(&$emailAddress)
        {
            $message->to($emailAddress, 'TTS')->subject('TTS驗證郵件');
        });//dd(Mail::failures());
        if(Mail::failures()) return 0;return 1;
//        SendEmail::dispatch($emailAddress,$emailMessage);return 1;
    }


      /*  获取今天星期几
       *
       *  0,1,2,3,4,5,6
       *  分别代表星期日 ，一，二，三，四，五，六
       */
     public function getToday(){

        return Carbon::parse(date("Y-M-d",time()))->dayOfWeek;

     }


    //获取交易的总费率(数量*费率)
    public function getRate($num=1)
    {
        $rate = $this->redisHgetAll('OUTSIDE-RATE')['rate']?$this->redisHgetAll('OUTSIDE-RATE')['rate']:0.007;

        $change_rate = $num * $rate;

        return $change_rate;
    }

    //获取usdt对cny价格
    public function getUSDTCNY()
    {
//        $usdtId
//        return DB::table('coin_exchange_rate')->where([''])
    }

    /*身份证验证*/
    public function cardVerify(string $name,string $cardNo)
    {
        return (new IdentifyVerifyServer())->identifyVerify($name,$cardNo);
    }

    /*银行卡验证*/
    public function bankCardVerify($userId,$phone,$code,$name,$idCard,$cardNo,$bankId)
    {
        return (new BankCardVerifyServer())->verify($userId,$phone,$code,$name,$idCard,$cardNo,$bankId);
    }

    /**
     * 判断是否为合法的身份证号码
     */
    function isCreditNo($vStr){
        $vCity = array(
            '11','12','13','14','15','21','22',
            '23','31','32','33','34','35','36',
            '37','41','42','43','44','45','46',
            '50','51','52','53','54','61','62',
            '63','64','65','71','81','82','91'
        );
        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;
        if (!in_array(substr($vStr, 0, 2), $vCity)) return false;
        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);
        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }
        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
        if ($vLength == 18) {
            $vSum = 0;
            for ($i = 17 ; $i >= 0 ; $i--) {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
            }
            if($vSum % 11 != 1) return false;
        }
        return true;
    }

    /*银行卡正则*/
    function isBankCard($cardNo)
    {
        if (preg_match("/^[1-9][0-9]{14,19}$/",$cardNo)){
            return true;
        }
        return false;

    }

    //比特币地址正则
    public function isBTCAddress($value){
        // BTC地址合法校验33/34
        if (!(preg_match('/^(1|3|2)[a-zA-Z\d]{24,36}$/', $value) && preg_match('/^[^0OlI]{25,36}$/', $value))) {
            return false;//满足if代表地址不合法
        }
        return true;
    }

    //以太坊地址正则
    public function isETHAddress($value)
    {
        if (!is_string($value)) {
            return false;
        }
        return (preg_match('/^0x[a-fA-F0-9]{40}$/', $value) >= 1);
    }

    //判断地址类型1区块,2场内,3场外,4矿工内部,5星云内部
    public function getAddressType($address)
    {
        if ((new WalletDetail())->isAddress($address)) return 2;
        if ((new OutsideWalletDetail())->isAddress($address)) return 3;
        if ((new \App\Model\KgModel\UserWallet())->isAddress($address)) return 4;
        if ((new UserWallet())->isAddress($address)) return 5;
        return 1;
    }

    //实现异步非阻塞的curl功能
    function curlThread($email,$code){
        $url = url('api/sendEmailT') . '?email='.$email . '&code=' . $code;//dd($url);

        $time = time();// 创建一对cURL资源

        $ch1 = curl_init();// 设置URL和相应的选项


        curl_setopt($ch1, CURLOPT_URL, $url);

        curl_setopt($ch1, CURLOPT_HEADER, 0);// 创建批处理cURL句柄

        curl_setopt($ch1, CURLOPT_TIMEOUT_MS, 50);//最多等待1ms,实现立即返回

        curl_setopt($ch1, CURLOPT_NOSIGNAL, 1);

//        curl_setopt($ch1, CURLOPT_TIMEOUT, 1);

        $mh = curl_multi_init();// 增加2个句柄

        curl_multi_add_handle($mh,$ch1);

        $running=null;// 执行批处理句柄

        do { usleep(10000); curl_multi_exec($mh,$running);} while ($running > 0);// 关闭全部句柄

        curl_multi_remove_handle($mh, $ch1);

        curl_multi_close($mh);

        return;
    }


    public function checkHuiXiangZhengID($hxzNo,$area)//h香港m澳门
    {
        if ($area == 'h'){
            return preg_match('/^[H]\d{10}$/',$hxzNo);
        }elseif ($area == 'm'){
            return preg_match('/^[M]\d{10}$/',$hxzNo);
        }
        return false;
    }

    public function checkChinese($string)
    {
        if (preg_match("/^[\x80-\xff]+$/i",$string)) {
//            print("该字符串全部是中文");
            return true;
        } else {
//            print("该字符串不全部是中文");
            return false;
        }
    }



}