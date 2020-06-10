<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/10
 * Time: 16:57
 */

namespace App\Http\Controllers\Web\Api\V1;

use App\Traits\Tools;
use App\Traits\RedisTool;
use App\Traits\FileTools;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\UserIdentify;
use App\Model\BankCardVerify;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Server\BankCardVerifyServer;

class UserSettingController extends BaseController
{
    use Tools,RedisTool,FileTools;



    /*发送邮件验证码*/
    public function sendEmail(Request $request, User $user)
    {

        if ($this->verifyField($request->all(),[
            'email' => 'required|email',
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);


        if ($user->getOneRecord(['user_email'=>$request->input('email')]))
            return response()->json(['status_code'=>self::STATUS_CODE_EMAIL_HASEXIST,'message'=>'邮箱已被使用']);

        $code = rand(100000,999999);
        if ($this->redisExists($request->input('email'))) return response()->json(['status_code'=>self::STATUS_CODE_CODE_REPEAT,'message'=>'请勿重新发送']);
        if (($this->sendEmailMessage($request->input('email'),$code)) && $this->stringSetex($request->input('email'),300,"{$code}"))
            return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'data'=>['code'=>$code],'message'=>'验证码发送成功']);
        return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'验证码发送失败']);

    }


    /*绑定邮箱*/
    public function bindEmail(Request $request, User $user, JWTAuth $auth)
    {
        if ($this->verifyField($request->all(),[
            'email' => 'required|email',
            'code' => 'required|integer'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        if (! $result = $this->checkCode($request->input('email'),$request->input('code'))){
            return response()->json(['status_code'=>self::STATUS_CODE_CODE_EXPIRE,'message'=>'操作超时,验证码已过期']);
        }elseif ($result == 2){
            return response()->json(['status_code'=>self::STATUS_CODE_CODE_ERROR,'message'=>'验证码不正确']);
        }

        $userMessage = $auth->parseToken()->authenticate()->toArray();

        if ((!$user->getOneRecord(['user_email'=>$request->input('email')])) && $user->updateOneRecord($userMessage['user_id'],['user_email'=>$request->input('email')]))
            return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'绑定成功']);
        return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'绑定失败']);

    }


    /*设置资金密码*/
    public function setPayPassword(Request $request, User $user)
    {
        if ($this->verifyField($request->all(),[
            'pay_password' => 'required|min:6|max:12',
            'user_id' => 'required'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);


        if ($user->updateOneRecord($request->input('user_id'),['user_pay_password'=>md5($request->input('pay_password'))]))
            return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
        return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
    }

    /*查询用户认证级别*/
    public function checkUserAuthLevel(Request $request,User $user,UserIdentify $userIdentify)
    {
        if ($this->verifyField($request->all(),[
            'user_id' => 'required'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
        $userMessage = $user->getUserById($request->input('user_id'));
        $identify = $userIdentify->getOneRecordByUserId($request->input('user_id'));
        $topAuthStatus = $identify ? $identify->status : 0;
        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'data'=>['user_auth_level'=>$userMessage->user_auth_level,'topAuthStatus'=>$topAuthStatus],'message'=>'ok']);
    }


    /*查询身份证是否可用*/
    public function cardIsAvailable(Request $request,UserIdentify $userIdentify)
    {
        if ($this->verifyField($request->all(),[
            'user_id' => 'required',
            'identify_card' => 'required',
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
        if ($userIdentify->getOneRecordByCard($request->input('identify_card')))
            return response()->json(['status_code'=>self::STATUS_CODE_IDCARD_HASEXIST,'message'=>'该身份证已被认证']);

        if (! $this->isCreditNo($request->input('identify_card')))
            return response()->json(['status_code'=>self::STATUS_CODE_IDCARD_NOT_LEGAL,'message'=>'身份证不合法']);;

        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'身份证可用']);
    }



    /*初级认证*/
    public function primaryAuth(Request $request,UserIdentify $userIdentify,User $user)
    {
        if ($this->verifyField($request->all(),[
            'identify_name' => 'required|string',
            'identify_card' => 'required|min:18|max:18',
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

//        if ($userIdentify->getOneRecordByUserId($request->input('user_id'))){
//            return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
//        }
//
//        if ($userIdentify->getOneRecordByCard($request->input('identify_card')))
//            return response()->json(['status_code'=>self::STATUS_CODE_IDCARD_HASEXIST,'message'=>'该身份证已被认证']);
//
//        switch ($this->cardVerify($request->input('identify_name'),$request->input('identify_card'))){
//            case 0:
//                return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
//                break;
//            case 1:
//
//                break;
//            case 2:
//                return response()->json(['status_code'=>self::STATUS_CODE_CARDNO_ERROR,'message'=>'身份验证失败']);
//                break;
//            default:
                return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
//                break;
//        }

//        try{
//            DB::beginTransaction();
//            if (
//                $userIdentify->saveOneRecord($request->all())
//                && $user->updateOneRecord($request->input('user_id'),['user_auth_level'=>1])
//            ){
//                DB::commit();
//                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
//            }
//            DB::rollBack();
//            return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
//        }catch (\Exception $e){
////            dd($e);
//            DB::rollBack();
//            return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
//        }

    }


    /*上传身份证照片接口*/
    public function uploadIdentifyCard(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'file' => 'required|mimes:jpeg,png',
            'user_id' => 'required',
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
        try{
            $filePath = $this->putImage($request->file('file'),date('Y-m',time()),'userIdentify');
            if ($filePath == 0) return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
            if ($filePath == 2) return response()->json(['status_code'=>self::STATUS_CODE_IMAGE_TOOLARGE,'message'=>'图片过大']);
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'上传成功','data'=>['path'=>'/app/user_identify/'.$filePath]]);
        }catch (\Exception $e){
            return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
        }

    }




    /*高级认证*/
    public function topAuth(Request $request,UserIdentify $userIdentify)
    {
        if ($this->verifyField($request->all(),[
            'user_id' => 'required',
            'identify_card_z_img' => 'required|string|max:100',
            'identify_card_f_img' => 'required|string|max:100',
            'identify_card_h_img' => 'required|string|max:100',
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
//        dd($request->only(['identify_card_z_img','identify_card_f_img','identify_card_h_img']));
        $identify = $userIdentify->getOneRecordByUserId($request->input('user_id'));
        if (!$identify || ($identify->status != 0))
            return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);

        $data = $request->only(['identify_card_z_img','identify_card_f_img','identify_card_h_img']);
        $data['status'] = 1;
        if (
            $userIdentify->updateOneRecordByUserId($request->input('user_id'),$data)
        ){
            return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
        }
        return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);

    }


    /*上传头像*/
    public function setHeadImage(Request $request,User $user)
    {
        if ($this->verifyField($request->all(),[
            'file' => 'required',
            'user_id' => 'required',
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        try{
            $filePath = $this->putImage($request->file('file'),date('Y-m',time()),'headImg');
            if ($filePath == 0) return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
            if ($filePath == 2) return response()->json(['status_code'=>self::STATUS_CODE_IMAGE_TOOLARGE,'message'=>'图片过大']);
            if ($user->updateOneRecord($request->input('user_id'),['user_headimg'=>'/app/head_image/' . $filePath]))
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功','data'=>['user_headimg'=>'http://'.$_SERVER['HTTP_HOST'].'/app/head_image/' . $filePath]]);
            return response()->json(['status_code'=>self::STATUS_CODE_IMAGE_ERROR,'message'=>'操作失败,请检查您的图片大小和格式']);
        }catch (\Exception $e){
            return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
        }


    }


    /*修改登录密码*/
    public function updateLoginPassword(Request $request,User $user)
    {
        if ($this->verifyField($request->all(),[
            'user_id' => 'required',
            'oldPassword' => 'required|min:6|max:16',
            'newPassword' => 'required|min:6|max:16',
            'rePassword' => 'required|min:6|max:16',
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
        if ($request->input('newPassword') !== $request->input('rePassword'))
            return response()->json(['status_code'=>self::STATUS_CODE_REPASSWORD_ERROR,'message'=>'两次密码不一致']);
        if (($user->getUserById($request->input('user_id'))->user_password === md5($request->input('oldPassword'))) && $user->updateOneRecord($request->input('user_id'),['user_password'=>md5($request->input('newPassword'))]))
            return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
        return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);

    }

    /*修改资金密码*/
    public function updatePayPassword(Request $request,User $user)
    {
        if ($this->verifyField($request->all(),[
            'oldPassword' => 'required|min:6|max:6',
            'newPassword' => 'required|min:6|max:6',
            'rePassword' => 'required|min:6|max:6',
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
        if ($request->input('newPassword') !== $request->input('rePassword'))
            return response()->json(['status_code'=>self::STATUS_CODE_REPASSWORD_ERROR,'message'=>'两次密码不一致']);
        if (($user->getUserById($request->input('user_id'))->user_pay_password === md5($request->input('oldPassword'))) && $user->updateOneRecord($request->input('user_id'),['user_pay_password'=>md5($request->input('newPassword'))]))
            return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
        return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);

    }

    /*手机验证码修改资金密码*/
    public function updatePayPassword1(Request $request,User $user)
    {
        if ($this->verifyField($request->all(),[
            'phone' => 'required|min:11|max:11',
            'code' => 'required',
            'newPassword' => 'required|min:6|max:6',
            'rePassword' => 'required|min:6|max:6',
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        if ($request->input('newPassword') !== $request->input('rePassword'))
            return response()->json(['status_code'=>self::STATUS_CODE_REPASSWORD_ERROR,'message'=>'两次密码不一致']);

        if (! $result = $this->checkCode($request->input('phone'),$request->input('code'))){
            return response()->json(['status_code'=>self::STATUS_CODE_CODE_EXPIRE,'message'=>'请重新发送验证码']);
        }elseif ($result == 1){
            if ($user->updateOneRecord($request->input('user_id'),['user_pay_password'=>md5($request->input('newPassword'))]))
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
            return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
        }else{
            return response()->json(['status_code'=>self::STATUS_CODE_CODE_ERROR,'message'=>'验证码不正确']);
        }

    }


    /*获取用户设置页面的相关信息*/
    public function getUserSettingMsg(Request $request,User $user,BankCardVerify $bankCardVerify)
    {
        $message = $user->getUserSettingMsg($request->input('user_id'));
        $bankCard = $bankCardVerify->getRecordByUserId($request->input('user_id'));
        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>[
            'is_auth' => $message->user_auth_level,
            'is_set_payPassword' => $message->user_pay_password == '' ? 0 : 1,
            'is_bind_phone' => $message->user_phone ? 1 :0,
            'is_bind_email' => $message->user_email ? 1: 0,
            'is_set_password' => $message->user_password=='' ? 0: 1,
            'is_set_bank_card' => $bankCard ? 1:0,
            'phone' => $message->user_phone,
            'email' => $message->user_email
        ]]);
    }

    /*C初级认证绑定银行卡逻辑*/
    public function bindBankCard(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'name' => 'required',
            'id_card' => 'required',
            'bank_card' => 'required',
            'bank_id' => 'required|integer',
            'phone' => 'required',
            'code' => 'required'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        if (! $this->isBankCard($request->input('bank_card')))
            return response()->json(['status_code'=>self::STATUS_CODE_BANKCARD_NOT_LEGAL,'message'=>'银行卡不合法']);

        if (! $this->isCreditNo($request->input('id_card')))
            return response()->json(['status_code'=>self::STATUS_CODE_IDCARD_NOT_LEGAL,'message'=>'身份证不合法']);

        if (
            $this->redisExists('PRIMARY_AUTH_LIMIT_'.$request->input('user_id'))
            && ($this->stringGet('PRIMARY_AUTH_LIMIT_'.$request->input('user_id')) >= 3)
        ){
            return response()->json(['status_code'=>self::STATUS_CODE_VERIFY_NUM_LIMIT,'message'=>'今日验证次数已达上限']);
        }

        switch ($this->bankCardVerify($request->input('user_id'),$request->input('phone'),$request->input('code'),$request->input('name'),$request->input('id_card'),$request->input('bank_card'),$request->input('bank_id'))){
            case 0:
                return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
                break;
            case 1:
                return response()->json(['status_code'=>self::STATUS_CODE_CODE_EXPIRE,'message'=>'请重新发送验证码']);
                break;
            case 2:
                return response()->json(['status_code'=>self::STATUS_CODE_CODE_ERROR,'message'=>'验证码不正确']);
                break;
            case 3:
                return response()->json(['status_code'=>self::STATUS_CODE_BANK_VERIFY_EXIST,'message'=>'请勿重复认证']);
                break;
            case 4:
                return response()->json(['status_code'=>self::STATUS_CODE_IDCARD_HASEXIST,'message'=>'该身份证已被认证']);
                break;
            case 5:
                return response()->json(['status_code'=>self::STATUS_CODE_PHONE_ERROR,'message'=>'手机号不正确']);
                break;
            case 6:
                return response()->json(['status_code'=>self::STATUS_CODE_BANK_NAME_ERROR,'message'=>'银行名有误']);
                break;
            case 7:
                return response()->json(['status_code'=>self::STATUS_CODE_CARD_VERIFY_ERROR,'message'=>'验证不通过']);
                break;
            case 8:
                $this->redisDelete('PRIMARY_AUTH_LIMIT_'.$request->input('user_id'));
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'验证通过']);
                break;

        }

    }


    /*更换银行卡逻辑*/
    public function changeBankCard(Request $request,BankCardVerifyServer $verifyServer)
    {
        if ($this->verifyField($request->all(),[
            'name' => 'required',
            'id_card' => 'required',
            'bank_card' => 'required',
            'bank_id' => 'required|integer',
            'phone' => 'required',
            'code' => 'required'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        if (
            ($this->stringGet('BANKCARD_CHANGE_LIMIT_'.$request->input('user_id')) >= 3)
            || $this->redisExists('CHANGE_BANK_CARD_TIMEOUT_'.$request->input('user_id'))
        ){
            return response()->json(['status_code'=>self::STATUS_CODE_VERIFY_NUM_LIMIT,'message'=>'今日验证次数已达上限']);
        }

        if (! $this->isBankCard($request->input('bank_card')))
            return response()->json(['status_code'=>self::STATUS_CODE_BANKCARD_NOT_LEGAL,'message'=>'银行卡不合法']);

        switch ($verifyServer->changeBankCard($request->input('user_id'),$request->input('phone'),$request->input('code'),$request->input('name'),$request->input('id_card'),$request->input('bank_card'),$request->input('bank_id'))){
            case 0:
                return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'验证失败']);
                break;
            case 1:
                return response()->json(['status_code'=>self::STATUS_CODE_CODE_EXPIRE,'message'=>'请重新发送验证码']);
                break;
            case 2:
                return response()->json(['status_code'=>self::STATUS_CODE_CODE_ERROR,'message'=>'验证码不正确']);
                break;
            case 3:
                return response()->json(['status_code'=>self::STATUS_CODE_CARD_VERIFY_ERROR,'message'=>'身份信息有误']);
                break;
            case 4:
                return response()->json(['status_code'=>self::STATUS_CODE_BANK_VERIFY_EXIST,'message'=>'请勿重复认证']);
                break;
            case 5:
                return response()->json(['status_code'=>self::STATUS_CODE_PHONE_ERROR,'message'=>'手机号不正确']);
                break;
            case 6:
                return response()->json(['status_code'=>self::STATUS_CODE_BANK_NAME_ERROR,'message'=>'银行名有误']);
                break;
            case 7:
                return response()->json(['status_code'=>self::STATUS_CODE_CARD_VERIFY_ERROR,'message'=>'验证不通过']);
                break;
            case 8:
                $this->redisDelete('BANKCARD_CHANGE_LIMIT_'.$request->input('user_id'));
                $this->stringSetex('CHANGE_BANK_CARD_TIMEOUT_'.$request->input('user_id'),86400,time());
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'更新成功']);
                break;
        }

    }






}