<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/10
 * Time: 14:27
 */

namespace App\Http\Middleware\Sto;

use App\Model\StoCoinStageDay;
use Closure;

class CheckDayBuyTime
{

    //用户购买sto时检测当日开售和和结束的时间




    public function __construct()
    {

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
        $day = StoCoinStageDay::find($request->day_id);

        $com_start = compare_today_time(date('H:i:s'),$day->sto_coin_stage->start_time);

        $com_end = compare_today_time(date('H:i:s'),$day->sto_coin_stage->end_time);


        if ($com_start != 1 || $com_end != -1){
            return api_response()->zidingyi('尚未开放');
        }
//        dd($com_end);


        return $next($request);
    }


}