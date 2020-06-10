<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 15:00
 */

namespace App\Logic;

use App\Model\BankList;
use App\Server\IdentifyVerifyServers\BankCard;
use App\Server\IdentifyVerifyServers\Identify;
use App\Server\UserServers\Dao\BankCardVerifyDao;
use App\Server\UserServers\Dao\UserDao;
use App\Server\UserServers\Dao\UserIdentifyDao;
use App\Traits\FileTools;
use App\Traits\RedisTool;
use App\Traits\Tools;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Class PersonalCenterLogic
 * @package App\Logic
 * 个人中心所有逻辑
 */
class PersonalCenterLogic
{
    use Tools,FileTools,RedisTool;

    private $userDao;
    private $bankCardVerifyDao;
    private $userIdentifyDao;

    public function __construct(UserDao $userDao,BankCardVerifyDao $bankCardVerifyDao,UserIdentifyDao $userIdentifyDao)
    {
        $this->userDao = $userDao;
        $this->bankCardVerifyDao = $bankCardVerifyDao;
        $this->userIdentifyDao = $userIdentifyDao;
    }

    /*获取用户设置页面的相关信息*/
    public function getUserSettingMsg($userId)
    {
        $message = $this->userDao->getUserSettingMsg($userId);
        $bankCard = $this->bankCardVerifyDao->getRecordByUserId($userId);
        $userIdentify = $this->userIdentifyDao->getOneRecordByUserId($userId);//dd($userIdentify);
        if ($userIdentify){
            if ($userIdentify->status == 1){
                $auth = 3;
            }elseif ($userIdentify->status == 3){
                $auth = 4;
            }else{
                $auth = $message->user_auth_level;
            }
        }else{
            $auth = $message->user_auth_level;
        }
        return [
            'is_auth' => $auth,
            'is_set_payPassword' => $message->user_pay_password == '' ? 0 : 1,
            'is_bind_phone' => $message->user_phone ? 1 :0,
            'is_bind_email' => $message->user_email ? 1: 0,
            'is_set_bank_card' => $bankCard ? 1:0,
            'phone' => $message->user_phone,
            'email' => $message->user_email
        ];

    }

    /*初级认证*/
    public function primaryAuth($data)
    {

        if ($data['area_id'] == 1){
            if ( ! $this->isCreditNo($data['identify_card'])) return 0;

            if (! (new Identify())->verify($data['identify_name'],$data['identify_card'],$data['identify_sex'])) return -1;
        }

        if ($this->userIdentifyDao->getOneRecordByCard($data['identify_card'])) return 1;

        try{
            DB::beginTransaction();
            if (
                $this->userIdentifyDao->saveOneRecord($data)
                && $this->userDao->updateOneData(['user_id'=>$data['user_id']],['user_auth_level'=>1])
            ){
                DB::commit();return 2;
            }
            DB::rollBack();
            return 3;
        }catch (\Exception $e){
            DB::rollBack();
            return 3;
        }

    }

    public function getUserAuthMsg(int $userId)
    {
        return $this->userIdentifyDao->getOneRecordByUserId($userId);
    }

    /**
     * 高级认证
     */
    public function topAuth($data,$images)
    {

        $userId = $data['user_id'];
        unset($data['user_id']);

        $identify = $this->userIdentifyDao->getOneRecordByUserId($userId);

        if (! $identify){
            if (! $this->isCreditNo($data['identify_card'])) return 0;//card error
            if ($this->userIdentifyDao->getOneRecordByCard($data['identify_card'])) return 1;//card exists
        }
        if ($identify){
            if ($identify->status != 0 && $identify->status != 3) return 2;//repeat
        }

        $imgArr = [];
        foreach ($images as $key=>$value){
            $filePath = $this->uploadAuthImg($value);
            if ($filePath === 0) return 3;//unkown error
            $imgArr[$key] = '/app/user_identify/'.$filePath;
        }

        $imgArr['status'] = 1;
        $imgArr['refuse_reason'] = '';
//        if ($identify) $data = [];
        $data = [];
        if ($this->userIdentifyDao->topAuth($userId,$imgArr,$data)){
            return 4;//success
        }
        return 5;

    }

    /**
     * 上传图片
     */
    protected function uploadAuthImg($imageFile,$disk = 'userIdentify')
    {
        try{
            return $this->putImage($imageFile,date('Y-m',time()),$disk);
        }catch (\Exception $e){
            return 0;
        }
    }

    /**
     * 修改头像
     */
    public function setHeadImg($userId,$image)
    {
        try{
            $filePath = $this->uploadAuthImg($image,'headImg');
            if ($filePath === 0) return 0;//unkown error
            $oldHeadImg = str_replace('http://'.$_SERVER['HTTP_HOST'],'',$this->userDao->find($userId,['user_headimg'])->user_headimg);
            if ($this->userDao->updateOneRecord($userId,['user_headimg'=>'/app/head_image/' . $filePath])){
                if ($oldHeadImg != '/app/head_image/head_default.png')
                File::delete(public_path($oldHeadImg));
                return ['user_headimg'=>'http://'.$_SERVER['HTTP_HOST'].'/app/head_image/' . $filePath];
            }
            return 1;//error
        }catch (\Exception $e){
            return 2;//error
        }
    }

    /**
     *set pay password
     */
    public function setPayPassword($userId,$payPassword)
    {
        if ($this->userDao->getOneData(['user_id'=>$userId],['user_pay_password'])->user_pay_password !== '') return 0;
        if ($this->userDao->setPayPassword($userId,$payPassword)) return 1;return 0;
    }
    /**
     * send email
     */
    public function sendEmailCodeMsg($email)
    {
        $code = rand(100000,999999);
        if ($this->redisExists($email)) return 0;//response()->json(['status_code'=>self::STATUS_CODE_CODE_REPEAT,'message'=>'请勿重新发送']);
//        if (($this->sendEmailMessage($email,$code)) && $this->stringSetex($email,300,"{$code}"))
//            return 1;return 2;
        $this->curlThread($email,$code);
        $this->stringSetex($email,300,"{$code}");
        return 1;

    }

    /**
     * set email
     */
    public function setEmail($userId,$email,$code)
    {

        if (! $result = $this->checkCode($email,$code)){
            return 0;
        }elseif ($result == 2){
            return 1;
        }

        if ($this->userDao->getOneData(['user_id'=>$userId],['user_email'])->user_email !== '') return 2;
        if ($this->userDao->getOneRecord(['user_email'=>$email])) return 3;

        if ($this->userDao->setEmail($userId,$email)) return 4;return 2;
    }

    /*查询银行列表*/
    public function getBankList()
    {
        return ['bank_list'=>(new BankList())->getRecords()];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * bind bank card
     */
    public function bindBankCard($data)
    {
        $currentUser = UserDao::find($data['user_id']);
        if (! $result = $this->checkCode($currentUser->user_phone,$data['code'])){
            return 0;
        }elseif ($result == 2){
            return 1;
        }

        if (! BankList::find($data['bank_id'])) return -2;

        if (! $this->isBankCard($data['bank_card'])) return 3;

        if ($this->bankCardVerifyDao->getOneBankCard($data['user_id'])) return 2;

        $user = $this->userIdentifyDao->getOneRecordByUserId($data['user_id']);

        $verifyName = $user['identify_name'];

        if ($user['identify_area_id'] == 1) {
            if (!(new BankCard())->verify($user['identify_name'], $user['identify_card'], $data['bank_card'])) return -1;
        }else{
            $verifyName = $data['name'];
        }

//            return response()->json(['status_code'=>self::STATUS_CODE_BANKCARD_NOT_LEGAL,'message'=>'银行卡不合法']);

        $result = $this->bankCardVerifyDao->saveOneRecord(['verify_name'=>$verifyName,'verify_card_no'=>$data['bank_card'],'bank_id'=>$data['bank_id'],'verify_phone'=>$data['phone'],'user_id'=>$data['user_id']]);
        if ($result) return 4;return 5;
    }

    /**
     * update bank card
     */
    public function updateBankCard($data)
    {
        $currentUser = UserDao::find($data['user_id']);
        if (! $result = $this->checkCode($currentUser->user_phone,$data['code'])){
            return 0;
        }elseif ($result == 2){
            return 1;
        }
        if (! BankList::find($data['bank_id'])) return -2;

        if (! $this->isBankCard($data['bank_card'])) return 3;

        if (!$this->bankCardVerifyDao->getOneBankCard($data['user_id'])) return 2;

        $user = $this->userIdentifyDao->getOneRecordByUserId($data['user_id']);
        $verifyName = $user['identify_name'];
        if ($user['identify_area_id'] == 1) {
            if (!(new BankCard())->verify($user['identify_name'], $user['identify_card'], $data['bank_card'])) return -1;
        }else{
            $verifyName = $data['name'];
        }
//            return response()->json(['status_code'=>self::STATUS_CODE_BANKCARD_NOT_LEGAL,'message'=>'银行卡不合法']);

        $result = $this->bankCardVerifyDao->updateOneRecord(['user_id'=>$data['user_id']],['verify_name'=>$verifyName,'verify_card_no'=>$data['bank_card'],'bank_id'=>$data['bank_id']]);
        if ($result) return 4;return 5;
    }

    /*获取用户绑定的银行卡列表*/
    public function getUserBankCards($userId)
    {
        return ['bank_card_list'=>$this->bankCardVerifyDao->getAllCardsByUserId($userId)];
    }

    /*修改登录密码*/
    public function updateLoginPassword($data)
    {
        if (! $this->userDao->verifyUserPassword($data['user_id'],$data['old_password'])) return 0;//password error

        if ($data['new_password'] !== $data['re_password']) return 1;//repassword error

        if ($this->userDao->updatePasswordByUserId($data['user_id'],$data['new_password'])){
            auth('api')->logout();
            return 2;
        }//success
        return 3;//error
    }

    /*修改资金密码*/
    public function updatePayPassword($data)
    {
        if (! $this->userDao->verifyPayPassword($data['user_id'],$data['old_pay_password'])) return 0;//password error

        if ($data['new_pay_password'] != $data['re_pay_password']) return 1;//repassword error

        if ($this->userDao->updatePayPasswordByUserId($data['user_id'],$data['new_pay_password'])){
            return 2;
        }//success
        return 3;//error
    }


    /*找回zijin密码*/
    public function retrievePayPassword($data)
    {
        if($this->userDao->find($data['user_id'],['user_phone'])->user_phone != $data['phone']) return -1;

        if (! $result = $this->checkCode($data['phone'],$data['code'])){
            return 0;
            //return response()->json(['status_code'=>self::STATUS_CODE_CODE_EXPIRE,'message'=>'请重新发送验证码']);
        }elseif ($result == 2){
            return 1;
            //return response()->json(['status_code'=>self::STATUS_CODE_CODE_ERROR,'message'=>'验证码不正确']);
        }

        if ($data['password'] !== $data['re_password'])
            return 2;
        //return response()->json(['status_code'=>self::STATUS_CODE_REPASSWORD_ERROR,'message'=>'密码不一致']);

        if ($this->userDao->updatePayPasswordByUserId($data['user_id'],$data['password'])){
            $this->redisDelete($data['phone']);
            return 3;
        }
        //return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
        return 4;
        //return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);

    }











}