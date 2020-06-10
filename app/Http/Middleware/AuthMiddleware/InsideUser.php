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

class InsideUser
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
        $user = $this->user->where('user_id',$request->input('user_id'))->select(['is_inside_user'])->first();

        if ($user->is_inside_user == 0){
            return response()->json(['status_code'=>1030,'message'=>'非内部用户','data'=>[]]);
        }


//        dd(1);
        return $next($request);
    }

}