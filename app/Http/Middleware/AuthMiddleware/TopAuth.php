<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13
 * Time: 14:50
 */

namespace App\Http\Middleware\AuthMiddleware;

use Closure;
use App\Model\User;
use App\Model\UserIdentify;

class TopAuth
{

    public $user;
    public $userIdentify;
    public function __construct(User $user,UserIdentify $userIdentify)
    {
        $this->user = $user;
        $this->userIdentify = $userIdentify;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            $user = $this->user->where('user_id',$request->input('user_id'))->select(['user_auth_level'])->first();

            if (!$user || ($user->user_auth_level < 2)){
                $userIdentify = $this->userIdentify->where(['user_id'=>$request->user_id])->first();
                if ($userIdentify && $userIdentify->status==1)
                    return response()->json(['status_code'=>1035,'message'=>'高级认证审核中','data'=>[]]);
                return response()->json(['status_code'=>1033,'message'=>'请完成高级实名认证','data'=>[]]);
            }
        }catch (\Exception $exception){
            return api_response()->zidingyi('网络繁忙');
        }

//        dd(1);
        return $next($request);
    }

}