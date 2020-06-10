<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/8
 * Time: 10:24
 */

namespace App\Http\Middleware;

use Closure;
use App\Model\BusinessList;

class CheckBusiness
{

    private $business;
    public function __construct(BusinessList $businessList)
    {
        $this->business = $businessList;

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

        if (! $this->business->getRecordByUserId($request->input('user_id'))){
            return response()->json(['status_code'=>1050,'message'=>'您不是商家']);
        }

        return $next($request);

    }

}