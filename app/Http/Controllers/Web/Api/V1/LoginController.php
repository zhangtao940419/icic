<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 13:29
 */

namespace App\Http\Controllers\Web\Api\V1;

use App\Http\Controllers\Web\BaseController;
use App\Logic\UserLogic;
use Illuminate\Http\Request;
use App\Traits\Tools;
use App\Traits\RedisTool;
use App\Http\Response\ApiResponse;

class LoginController extends BaseController
{
    use Tools,RedisTool,ApiResponse;

    private $userLogic;

    function __construct(UserLogic $userLogic)
    {
        $this->userLogic = $userLogic;
    }

    /*
     *  验证并获取TOken
     *
     */
    public function login(Request $request){

        if($result = $this->verifyField($request->all(),[
            'phone' => 'required',
            'password' => 'required|string',
            'code' => 'required'
        ])) return $this->parameterError();

        switch ($result = $this->userLogic->login($request->all())){
            case 0:
                return $this->responseByENCode('STATUS_CODE_UNAUTHORIZED','账号被冻结,请联系客服!');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_UNAUTHORIZED','账号或者密码不正确,请联系客服!');
                break;
            case -1:
                return $this->responseByENCode('STATUS_CODE_CODE_EXPIRE','请重新发送验证码');
                break;
            case -2:
                return $this->responseByENCode('STATUS_CODE_CODE_ERROR','验证码错误');
                break;
            default:
                $this->redisDelete($request->phone);
                return $this->responseByENCode('STATUS_CODE_SUCCESS','登录成功',$result);
                break;

        }

    }

    /**
     * 退出
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
//        Auth::guard($this->guard)->logout();
        $this->userLogic->logout();
        return $this->responseByENCode('STATUS_CODE_SUCCESS','退出成功');
    }


    public function verifyPasswordCode(Request $request)
    {
        if($result = $this->verifyField($request->all(),[
            'phone' => 'required|string|min:11|max:11',
            'code' => 'required',
        ])) return $this->parameterError();

        switch ($this->userLogic->verifyPasswordCode($request->phone,$request->code)){
            case 0:
                return $this->responseByENCode('STATUS_CODE_PHONE_NOTEXIST','账号不存在');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_CODE_EXPIRE','请重新发送验证码');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_SUCCESS','验证通过');
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_CODE_ERROR','验证码错误');
                break;
        }

    }





    /*找回密码*/
    public function retrievePassword(Request $request)
    {
        if($result = $this->verifyField($request->all(),[
            'phone' => 'required',
            'code' => 'required|integer',
            'password' => 'required|string|min:6|max:16',
            're_password' => 'required|string|min:6|max:16',
        ])) return $this->parameterError();

        switch ($this->userLogic->retrievePassword($request->all())){
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

        }


    }


}