<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13
 * Time: 14:42
 */

namespace App\Http\Middleware\AuthMiddleware;

use Closure;
use App\Model\User;

class PrimaryAuth
{
    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
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

        if (!$user || ($user->user_auth_level < 1))
            return response()->json(['status_code'=>1032,'message'=>'请完成初级实名认证']);

        }catch (\Exception $exception){
            return api_response()->zidingyi('网络繁忙');
        }


        return $next($request);
    }


}