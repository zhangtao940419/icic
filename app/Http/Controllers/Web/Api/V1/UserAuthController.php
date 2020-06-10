<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/11
 * Time: 20:34
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Http\Controllers\Web\BaseController;
use App\Http\Response\ApiResponse;
use App\Model\User;
use Illuminate\Http\Request;
use App\Traits\Tools;
use App\Traits\RedisTool;
use App\Server\UserServers\Dao\UserDao;

class UserAuthController extends BaseController
{
    use Tools,RedisTool,ApiResponse;

    private $user;
    private $userDao;
    function __construct(User $user,UserDao $userDao)
    {
        $this->user = $user;
        $this->userDao = $userDao;
    }

    /*验证用户密码*/
    public function verifyUserPayPassword(Request $request)
    {
        if($result = $this->verifyField($request->all(),[
            'pay_password' => 'required|string',
        ])) return $this->parameterError();

        if (! $this->userDao->verifyPayPassword($request->user_id,$request->pay_password))
            return $this->responseByENCode('STATUS_CODE_PASSWORD_ERROR','密码错误');

        $this->stringSetex('AUTH_USER_'.$request->input('user_id'),120,$request->pay_password);

            return $this->responseByENCode('STATUS_CODE_SUCCESS','验证通过');
    }


    /*检查是否需要输入密码*/
    public function checkUserAuth(Request $request)
    {
        if ($this->user->getUserById($request->input('user_id'),['user_pay_password'])->user_pay_password == '')
            return $this->responseByENCode('STATUS_CODE_PAYPASSWORD_NOTEXIST','请设置资金密码');

        if ($this->redisExists('AUTH_USER_'.$request->input('user_id'))
            && $this->userDao->verifyPayPassword($request->user_id,$this->stringGet('AUTH_USER_'.$request->input('user_id')))
        ){
            return $this->responseByENCode('STATUS_CODE_SUCCESS','验证通过');
        }

        return $this->responseByENCode('STATUS_CODE_AUTH_VERIFY_FAIL','验证不通过');

    }


    //查询是否是内部用户
    public function checkIsInsideUser()
    {

        $user = auth('api')->user();

        if ($user->is_inside_user == 1){
            return $this->success();
        }

        return $this->error();

    }




}