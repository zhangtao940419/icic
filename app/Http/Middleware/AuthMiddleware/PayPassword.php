<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/19
 * Time: 16:45
 */

namespace App\Http\Middleware\AuthMiddleware;
use Closure;
use App\Model\User;

class PayPassword
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
        $user = $this->user->where('user_id',$request->input('user_id'))->select(['user_pay_password'])->first();

        if (!$user || ($user->user_pay_password == ''))
            return response()->json(['status_code'=>1034,'message'=>'请设置资金密码']);

        return $next($request);
    }


}