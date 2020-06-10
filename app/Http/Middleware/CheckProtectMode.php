<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/6
 * Time: 17:58
 */

namespace App\Http\Middleware;

use App\Model\Settings;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class CheckProtectMode extends BaseMiddleware
{



    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {

        //维护模式检测
        $setting = (new Settings())->getSetting('protect_mode');
        if ($setting && $setting->setting_value == 1 && current_user()->is_special_user != 1) {

            return response()->json(['status_code' => 9000, 'message' => $setting->setting_des]);
        }


        return $next($request);

    }

}