<?php

namespace App\Http\Middleware;

use App\Model\Admin\Permission;
use Closure;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $route = \Route::currentRouteName();
      //  dd($route);
//        if (\Cache::has('permissions-' . $user->id)) {
//            $data = \Cache::get('permissions-' . $user->id);
////            dd($data);
//        } else {
            $data = [];
            foreach ($user->permissions as $v) {
                $data[] = $v->route;
            }
//            \Cache::put('permissions-' . $user->id, $data, 30);
//            dd($data);
//        }
//dd($route);
        $permission = Permission::where('route',$route)->first();
        if ($permission && in_array($route, $data)) {
            return $next($request);
        } else {
//            throw new \Exception('你没有该权限');
            if ($route == 'admin.index' || !$permission) return $next($request);//dd($permission);
            return back()->with('danger','你没有该权限');
        }

    }
}
