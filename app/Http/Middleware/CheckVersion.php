<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\RedisTool;

class CheckVersion
{
    use RedisTool;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ios_version = $request->header('iosVersion');
        $android_version = $request->header('androidVersion');

        if (!isset($android_version) && !isset($ios_version)) {
            return response()->json(['status_code' => 500, 'message' => '版本号不一致,请升级到最新版']);
        } elseif (isset($android_version) && $android_version != $this->stringGet('android_version')) {
            return response()->json(['status_code' => 500, 'message' => '版本号不一致,请升级到最新版']);
        } elseif (isset($ios_version) && $ios_version != $this->stringGet('ios_version')) {
            return response()->json(['status_code' => 500, 'message' => '版本号不一致,请升级到最新版']);
        } else {
            return $next($request);
        }
    }
}
