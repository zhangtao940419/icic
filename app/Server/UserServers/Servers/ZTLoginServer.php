<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 10:40
 */
namespace App\Server\UserServers\Servers;

use App\Server\UserServers\Dao\UserDao;
use App\Server\UserServers\Interfaces\LoginServerInterface;
use Auth;
use App\Traits\RedisTool;

class ZTLoginServer implements LoginServerInterface
{
    use RedisTool;
    private $userDao;

    public function __construct()
    {
        $this->userDao = new UserDao();
    }

    public function login($data,$single = 0)
    {
        // TODO: Implement login() method.

        $user = $this->userDao->getOneData(['user_phone'=>$data['phone']],['*']);

        if(
            $user
            && $this->userDao->verifyPassword($data['password'],$user->user_password)
        ){
            if (!$user->is_frozen)
                return 0;
                //return response()->json(['message'=>'账号被冻结,请联系客服!','status_code'=>self::STATUS_CODE_UNAUTHORIZED]);
            $rand = rand(1,9999);
            $token =  $single ? Auth::guard('api')->claims(['rand'=>$rand])->fromUser($user) : Auth::guard('api')->fromUser($user);
            $data = $user->toArray();
            $data['token'] = $token;
            if ($single)$this->stringSetex('SINGLE:POINT_TOKEN'.$data['user_id'],86400,$rand);
            return $data;
            //return response()->json(['data'=>$data,'message'=>'登录成功','status_code'=>self::STATUS_CODE_SUCCESS]);
        }else{
            return 1;
            //return response()->json(['message'=>'账号或者密码不正确','status_code'=>self::STATUS_CODE_UNAUTHORIZED]);
        }

    }

    public function logout()
    {
        // TODO: Implement logout() method.
        Auth::guard('api')->logout();
        return 1;
    }


    /*找回密码*/
    public function retrievePassword($data)
    {

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

        if ($this->userDao->updatePasswordByPhone($data['phone'],$data['password'])){
            $this->redisDelete($data['phone']);
            return 3;
        }
        //return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
        return 4;
        //return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);

    }


}