<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 15:24
 */

namespace App\Http\Controllers\Web\Api\V1;

use App\Logic\UserLogic;
use App\Traits\Tools;
use App\Traits\RedisTool;
use App\Traits\Communication;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use App\Model\User;
use App\Http\Response\ApiResponse;

class RegisterController extends BaseController
{
    use Tools,ApiResponse;

    private $userLogic;

    function __construct(UserLogic $userLogic)
    {
        $this->userLogic = $userLogic;

    }


    //注册检查验证码
    public function checkRegisterCode(Request $request)
    {

        if($result = $this->verifyField($request->all(),[
            'phone' => 'required',
            'code' => 'required|integer',
        ])) return $this->parameterError();

        switch ($this->userLogic->checkRegisterCode($request->phone,$request->code)){
            case 0:
                return $this->responseByENCode('STATUS_CODE_PHONE_HASEXIST','该手机号已被注册');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_CODE_EXPIRE','请重新发送验证码');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','验证通过');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_CODE_ERROR','验证码不正确');
                break;
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 用户数据入库
     */
    public function register(Request $request,User $user)
    {
        if($result = $this->verifyField($request->all(),[
            'phone' => 'required',
            'code' => 'required',
            'password' => 'required|min:6|max:16',
            're_password' => 'min:6|max:16',
//            'nickname' => 'required|min:2|max:18',
            'invitation_code' => 'string'
        ])) return $this->parameterError(1004,'参数错误',$result);

        if ($user->getUserByPhone($request->phone)) return api_response()->zidingyi('该手机号已注册');
//dd($this->userLogic->register($request->all()));
        switch ($result = $this->userLogic->register($request->all())){
            case -1:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','操作失败');
                break;
            case -2:
                return $this->responseByENCode('STATUS_CODE_CODE_EXPIRE','请重新发送验证码');
                break;
            case -3:
                return $this->responseByENCode('STATUS_CODE_CODE_ERROR','验证码不正确');
                break;
            case 0:
                return $this->responseByENCode('STATUS_CODE_NICKNAME_HASEXIST','昵称已被占用');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','注册失败');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_INVITE_CODE_ERROR','邀请码不正确');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','注册失败');
                break;
            case 4:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','注册失败');
                break;
            default:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','注册成功',$result);
                break;
        }

    }













}