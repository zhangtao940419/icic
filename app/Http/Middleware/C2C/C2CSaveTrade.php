<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 13:23
 */
namespace  App\Http\Middleware\C2C;

use Closure;
use App\Model\C2CSetting;
use App\Model\C2CTrade;
use App\Model\C2CTradeOrder;

class C2CSaveTrade
{
/*c2c下单前处理*/

    public $c2CSetting;
    public function __construct(C2CSetting $c2CSetting)
    {
        $this->c2CSetting = $c2CSetting;
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

        $c2cSetting = $this->c2CSetting->getOneRecord();

        if ($request->input('trade_type') == 1){//买单
            $trade = C2CTrade::where(['user_id'=>$request->input('user_id'),'is_usable'=>1,'trade_type'=>1])->whereIn('trade_status',[1,2])->get()->toArray();
            if (count($trade) >= $c2cSetting['user_buy_order_limit'])
                return response()->json(['status_code'=>1065,'message'=>'有待处理的订单']);

            if (
                ($request->input('trade_number') < $c2cSetting['user_buy_num_min'])
                || ($request->input('trade_number') > $c2cSetting['user_buy_num_max'])
            ){
                return response()->json(['status_code'=>1063,'message'=>'请输入'.$c2cSetting['user_buy_num_min'].'到'.$c2cSetting['user_buy_num_max'].'之间的数量']);
            }
        }

        if ($request->input('trade_type') == 2){//卖单
            $trade = C2CTrade::where(['user_id'=>$request->input('user_id'),'is_usable'=>1,'trade_type'=>2])->whereIn('trade_status',[1,2])->get()->toArray();
            if (count($trade) >= $c2cSetting['user_sell_order_limit'])
                return response()->json(['status_code'=>1065,'message'=>'有待处理的订单']);

            $query = C2CTrade::where(['user_id'=>$request->input('user_id'),'is_usable'=>1,'trade_type'=>2])->whereIn('trade_status',[1,2,3])->whereDate('created_at',date('Y-m-d',time()));
            $todaySellNum = $query->sum('trade_number');

            if ($c2cSetting['user_sell_day_max'] < ($request->trade_number + $todaySellNum))
                return response()->json(['status_code'=>1063,'message'=>'单日限额'.$c2cSetting['user_sell_day_max']]);

            $todaySellTimes = $query->count();
            if ($todaySellTimes >= 5) return response()->json(['status_code'=>1063,'message'=>'今日提现次数已达上限']);


            if (
                ($request->input('trade_number') < $c2cSetting['user_sell_num_min'])
                || ($request->input('trade_number') > $c2cSetting['user_sell_num_max'])
            ){
                return response()->json(['status_code'=>1063,'message'=>'请输入'.$c2cSetting['user_sell_num_min'].'到'.$c2cSetting['user_sell_num_max'].'之间的数量']);
            }
        }

        return $next($request);
    }

}