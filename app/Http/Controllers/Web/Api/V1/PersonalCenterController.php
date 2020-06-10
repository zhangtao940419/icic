<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 14:59
 */

namespace App\Http\Controllers\Web\Api\V1;

use App\Http\Controllers\Web\BaseController;
use App\Http\Response\ApiResponse;
use App\Logic\PersonalCenterLogic;
use App\Model\Admin\Article;
use App\Model\Poster;
use App\Model\Settings;
use App\Model\User;
use App\Notifications\AdminWarningNotification;
use App\Server\IdentifyVerifyServers\BankCard;
use App\Traits\RedisTool;
use App\Traits\Tools;
use function Couchbase\basicEncoderV1;
use Illuminate\Http\Request;

/**
 * Class PersonalCenterController
 * @package App\Http\Controllers\Web\Api\V1
 * 个人中心控制器
 */
class PersonalCenterController extends BaseController
{
    use ApiResponse,Tools,RedisTool;

    private $personalCenterLogic;

    function __construct(PersonalCenterLogic $personalCenterLogic)
    {
        $this->personalCenterLogic = $personalCenterLogic;
    }

    //模拟登陆获取token
    public function mockLogin(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'user_id' => 'required|integer'
        ])) return $vr;

        $user = User::query()->find($request->user_id);

        $rand = rand(1,9999);
        $token =  auth('api')->claims(['rand' => $rand])->fromUser($user);
        $data = $user->toArray();
        $data['token'] = $token;

        return $this->successWithData($data);
    }

    //获取邀请好友概况
    public function getInvitaInfo(Request $request)
    {
        $user = User::query()->find($request->user_id);

        $invitation_code = $user['user_Invitation_code'];
        $url = env('DOMAIN_NAME',$_SERVER['HTTP_HOST']);
        $invitation_url = 'http://' . $url . '/register?invitation_code=' . $invitation_code;

        $data['invitation_code'] = $invitation_code;//邀请码
        $data['invitation_url'] = $invitation_url;//邀请链接
        $data['children_count'] = $user->direct_user_count();//邀请人数
        $icic_commission = $user->wallet_flows()->where(['flow_type'=>13,'coin_id'=>8])->sum('flow_number')
            + $user->ore_pool_transfer_records()->where(['type'=>5,'coin_id'=>8])->sum('amount');//ICIC返佣金额
//        $plc_commission = $user->wallet_flows()->where(['flow_type'=>13,'coin_id'=>13])->sum('flow_number');//PLC返佣金额
        $data['commission_count'] = [
             ['label'=>'返佣（ICIC）','commission'=>$icic_commission],
//             ['label'=>'返佣（PLC）','commission'=>$plc_commission],
        ];

        return $this->successWithData($data);
    }

    //获取返佣规则
    public function getInvitaRules(Request $request)
    {
        $category_id = 9;

        $rules = Article::query()->where('category_id',$category_id)->first();

        return $this->successWithData($rules);
    }

    //获取邀请海报模板
    public function getInvitaPoster(Request $request)
    {
        $posters = Poster::query()->pluck('imgurl');

        return $this->successWithData($posters);
    }

    //邀请好友--邀请记录
    public function getInvitaUsers(Request $request)
    {
        $user = User::query()->find($request->user_id);

        $builder = $user->children();

        $data = $builder->latest()->paginate();

        return $this->successWithData($data);
    }

    //邀请好友--返佣记录
    public function getInvitaCommission(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'datetime' => 'date',
        ])) return $vr;

        $user = User::query()->find($request->user_id);

        $builder = $user->wallet_flows()->where(['flow_type'=>13]);
        $builder2 = $user->ore_pool_transfer_records()->where(['type'=>5]);

        if($datetime = $request->input('datetime',date('Y-m'))){
            $datetime_arr = explode('-',$datetime);
//            dd($datetime_arr);

            $builder->whereYear('created_at',$datetime_arr[0])->whereMonth('created_at',$datetime_arr[1]);
            $builder2->whereYear('created_at',$datetime_arr[0])->whereMonth('created_at',$datetime_arr[1]);
        }

        $data1 = $builder->with('s_user:user_id,user_phone')->latest()->get()->toArray();
        $data2 = $builder2->with('s_user:user_id,user_phone')->latest()->get()->toArray();

        $data = array_merge($data1,$data2);

        return $this->successWithData($data);
    }

    //邀请好友返佣排行榜
    public function getInvitaRanking(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'limit' => 'integer',
            'coin_id' => 'integer',
        ])) return $vr;

        $limit = $request->input('limit',10);
        $coin_type = $request->input('coin_type',8);

        if($coin_type == 8){
            $invitaRanking = $this->getZrevRange('icicInvitaRanking',0,$limit-1);
        }elseif($coin_type == 13){
            $invitaRanking = $this->getZrevRange('plcInvitaRanking',0,$limit-1);
        }

        foreach ($invitaRanking as $k=>$v){
            $user_id = str_replace('uid_','',$k);
            $phone = User::query()->where('user_id',$user_id)->value('user_phone');
            $data[] = ['user_id'=>$user_id,'amount'=>round($v,2),'phone'=>substr_cut($phone)];
        }

        return $this->successWithData($data);
    }

    //用户未读消息通知条数
    public function myNotifiablesCount(Request $request)
    {
        $user = User::query()->find($request->user_id);

        $count = $user->unreadNotifications()->count();

        return $this->successWithData(['count'=>$count]);
    }

    //用户消息通知列表
    public function myNotifiables(Request $request)
    {
        $user = User::query()->find($request->user_id);

        $notifiables = $user->notifications;

        //全部标记已读
//        $user->unreadNotifications->markAsRead();

        return $this->successWithData($notifiables);
    }

    //获取消息通知详情并标记已读
    public function readNotifiable(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'id' => 'required',
        ])) return $vr;

        $user = User::query()->find($request->user_id);

        $notifiable = $user->notifications()->where('id',$request->id)->firstOrFail();

        //标记消息为已读
        $notifiable->markAsRead();

        return $this->successWithData($notifiable);
    }

    //获取用户是否有后台发送的未读警告类型消息通知
    public function getWarningNotifiable(Request $request)
    {
        $user = User::query()->find($request->user_id);

        $notifiable = $user->unreadNotifications()->where('type',AdminWarningNotification::class)->first();

        return $this->successWithData($notifiable);
    }

    /*获取用户设置页面的相关信息*/
    public function getUserSettingMsg(Request $request)
    {
        switch ($result = $this->personalCenterLogic->getUserSettingMsg($request->user_id)){
            default:
                return $this->success(200,'获取成功',$result);
                break;
        }

    }

    /*初级认证*/
    public function primaryAuth(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'area_id' => 'required|integer|in:1,2,3',
            'identify_name' => 'required|string',
            'identify_card' => 'required|string',
            'identify_sex' => 'required|integer|min:1|max:2'
        ])) return $this->parameterError();

        $areas = ['0','c','h','m'];
        if ($request->area_id != 1 && !$this->checkHuiXiangZhengID($request->identify_card,$areas[$request->area_id]))
            return $this->responseByENCode('STATUS_CODE_AUTH_VERIFY_FAIL','回乡证不合法');

        switch ($this->personalCenterLogic->primaryAuth($request->all())){
            case -1:
                return $this->responseByENCode('STATUS_CODE_AUTH_VERIFY_FAIL','信息验证不通过');
                break;
            case 0:
                return $this->responseByENCode('STATUS_CODE_IDCARD_NOT_LEGAL','身份证不合法');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_IDCARD_HASEXIST','该身份证已被认证');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
        }

    }

    //获取认证信息
    public function getAuthMsg(Request $request)
    {
        switch ($result = $this->personalCenterLogic->getUserAuthMsg($request->user_id))
        {
            default:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','获取成功',$result);
                break;
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * top auth
     */
    public function topAuth(Request $request)
    {
        if ($result = $this->verifyField($request->all(),[
            'identify_name' => 'required|string',
            'identify_card' => 'required|string',
            'identify_sex' => 'required|integer',
            'identify_card_z_img' => 'required|image|mimes:jpg,png,jpeg|max:10240',
            'identify_card_f_img' => 'required|image|mimes:jpg,png,jpeg|max:10240',
            'identify_card_h_img' => 'required|image|mimes:jpg,png,jpeg|max:10240',
//            'identify_zp_img' => 'image|mimes:jpg,png,jpeg|max:10240',
        ])) return $this->parameterError(1004,'请检查图片格式和大小',$result);

        switch ($this->personalCenterLogic->topAuth($request->except(['identify_card_z_img','identify_card_f_img','identify_card_h_img','identify_zp_img']),$request->allFiles())){
            case 0:
                return $this->responseByENCode('STATUS_CODE_IDCARD_NOT_LEGAL','身份证不合法');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_IDCARD_HASEXIST','该身份证已被认证');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','请勿重复认证');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            case 4:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功');
                break;
            case 5:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
        }

    }

    /**
     * update headImg
     */
    public function setHeadImg(Request $request)
    {
        if ($result = $this->verifyField($request->all(),[
            'file' => 'required|image|mimes:jpg,png,jpeg|max:10240',
        ])) return $this->parameterError(1004,'请检查图片格式和大小',$result);

        switch ($result = $this->personalCenterLogic->setHeadImg($request->user_id,$request->file('file'))){
            case 0:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            default:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功',$result);
                break;
        }
    }

    /**
     * set pay password
     */
    public function setPayPassword(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'pay_password' => 'required|min:6|max:6',
        ])) return $this->parameterError();

        switch ($this->personalCenterLogic->setPayPassword($request->user_id,$request->pay_password)){
            case 0:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功');
                break;
        }

    }

    /**
     * send email code msg
     */
    public function sendEmail(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'email' => 'required|email',
        ])) return $this->parameterError();

        switch ($this->personalCenterLogic->sendEmailCodeMsg($request->email)){
            case 0:
                return $this->responseByENCode('STATUS_CODE_CODE_REPEAT','请勿重新发送');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','验证码发送成功');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','验证码发送失败');
                break;
        }
    }

    /**
     * set email
     */
    public function setEmail(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'email' => 'required|email',
            'code' => 'required'
        ])) return $this->parameterError();

        switch ($this->personalCenterLogic->setEmail($request->user_id,$request->email,$request->code)){
            case 0:
                return $this->responseByENCode('STATUS_CODE_CODE_EXPIRE','操作超时,验证码已过期');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_CODE_ERROR','验证码不正确');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_EMAIL_HASEXIST','邮箱已被使用');
                break;
            case 4:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功');
                break;
        }

    }

    /*查询银行列表*/
    public function getBankList()
    {
        $data = $this->personalCenterLogic->getBankList();
        return $this->responseByENCode('STATUS_CODE_SUCCESS','成功',$data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * bind bank card
     */
    public function bindBankCard(Request $request,BankCard $bankCard)
    {
        if ($this->verifyField($request->all(),[
            'name' => 'required|string',
            //'id_card' => 'required',
            'bank_card' => 'required',
            'bank_id' => 'required|integer',
            'phone' => 'required',
            'code' => 'required'
        ])) return $this->parameterError();

        if (! $this->checkChinese($request->name))
            return $this->responseByENCode('STATUS_CODE_AUTH_VERIFY_FAIL','请输入中文姓名');

        $re = $bankCard->checkBankCardId($request->bank_card);
        if (!is_int($re)) return api_response()->zidingyi($re);
        if ($re != $request->bank_id) return api_response()->zidingyi('开户行有误');

        switch ($this->personalCenterLogic->bindBankCard($request->all())){
            case -1:
                return api_response()->zidingyi('银行卡与身份信息不符');
                break;
            case -2:
                return api_response()->zidingyi('请选择开户行');
                break;
            case 0:
                return $this->responseByENCode('STATUS_CODE_CODE_EXPIRE','操作超时,验证码已过期');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_CODE_ERROR','验证码不正确');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_BANKCARD_NOT_LEGAL','银行卡不合法');
                break;
            case 4:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功');
                break;
            case 5:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
        }

    }

    /*更换银行卡逻辑*/
    public function updateBankCard(Request $request,BankCard $bankCard)
    {
        if ($this->verifyField($request->all(), [
            'name' => 'required',
            //'id_card' => 'required',
            'bank_card' => 'required',
            'bank_id' => 'required|integer',
            'phone' => 'required',
            'code' => 'required'
        ])) return $this->parameterError();

        if (! $this->checkChinese($request->name))
            return $this->responseByENCode('STATUS_CODE_AUTH_VERIFY_FAIL','请输入中文姓名');

        $re = $bankCard->checkBankCardId($request->bank_card);
        if (!is_int($re)) return api_response()->zidingyi($re);
        if ($re != $request->bank_id) return api_response()->zidingyi('开户行有误');

        switch ($this->personalCenterLogic->updateBankCard($request->all())){
            case -1:
                return api_response()->zidingyi('银行卡与身份信息不符');
                break;
            case -2:
                return api_response()->zidingyi('请选择开户行');
                break;
            case 0:
                return $this->responseByENCode('STATUS_CODE_CODE_EXPIRE','操作超时,验证码已过期');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_CODE_ERROR','验证码不正确');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_BANKCARD_NOT_LEGAL','银行卡不合法');
                break;
            case 4:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功');
                break;
            case 5:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
        }

    }

    /*获取用户绑定的银行卡列表*/
    public function getUserBankCards(Request $request)
    {
        return $this->responseByENCode('STATUS_CODE_SUCCESS','成功',$this->personalCenterLogic->getUserBankCards($request->user_id));
    }

    /*修改登录密码*/
    public function updateLoginPassword(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'old_password' => 'required|min:6|max:16',
            'new_password' => 'required|min:6|max:16',
            're_password' => 'required|min:6|max:16',
        ])) return $this->responseByENCode('STATUS_CODE_PASSWORD_ERROR','密码长度6-16位');

        switch ($this->personalCenterLogic->updateLoginPassword($request->all())){
            case 0:
                return $this->responseByENCode('STATUS_CODE_PASSWORD_ERROR','密码错误');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_REPASSWORD_ERROR','两次密码不一致');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
        }
    }

    /*修改资金密码*/
    public function updatePayPassword(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'old_pay_password' => 'required|min:6|max:16',
            'new_pay_password' => 'required|min:6|max:16',
            're_pay_password' => 'required|min:6|max:16',
        ])) return $this->parameterError();

        switch ($this->personalCenterLogic->updatePayPassword($request->all())){
            case 0:
                return $this->responseByENCode('STATUS_CODE_REPASSWORD_ERROR','密码错误');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_REPASSWORD_ERROR','两次密码不一致');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
        }
    }

    /*找回zijin密码*/
    public function retrievePayPassword(Request $request)
    {
        if($result = $this->verifyField($request->all(),[
            'phone' => 'required',
            'code' => 'required|integer',
            'password' => 'required|string|min:6|max:16',
            're_password' => 'required|string|min:6|max:16',
        ])) return $this->parameterError();

        switch ($this->personalCenterLogic->retrievePayPassword($request->all())){
            case 0:
                return $this->responseByENCode('STATUS_CODE_CODE_EXPIRE','请重新发送验证码');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_CODE_ERROR','验证码错误');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_REPASSWORD_ERROR','密码不一致');
                break;
            case 4:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','操作成功');
                break;
            case -1:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;

        }


    }




}
