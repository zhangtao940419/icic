<?php

namespace App\Http\Middleware\C2C;

use Closure;
use App\Model\C2CSetting;
use App\Model\C2CTrade;
use App\Model\C2CTradeOrder;
use App\Traits\RedisTool;

class C2CBusinessReceptOrder
{
    use RedisTool;
    /*商家接单前处理*/

    public $c2CSetting;
    public $c2CTradeOrder;
    public function __construct(C2CSetting $c2CSetting,C2CTradeOrder $c2CTradeOrder)
    {
        $this->c2CSetting = $c2CSetting;
        $this->c2CTradeOrder = $c2CTradeOrder;
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

        $trade = C2CTrade::find($request->input('trade_id'));

        $order = $this->c2CTradeOrder->getTransferingRecords($request->input('user_id'));
//dd($order);
        if ($trade && ($trade->trade_type == 1)){//买单
            foreach ($order as $key=>$value){
                if ($value['trade_msg']['trade_type'] != 1){
                    unset($order[$key]);
                }
            }
            if (count($order) < $c2cSetting['business_buy_order_limit'] && count($order) == 0) return $next($request);

            if (count($order) >= $c2cSetting['business_buy_order_limit'])
                return response()->json(['status_code'=>1065,'message'=>'有待处理的订单']);

            if ($this->redisExists('C2C_BUSINESS_BUY_TIMELIMIT_'.$request->input('user_id')))
                return response()->json(['status_code'=>1064,'message'=>'有待处理的订单']);


        }

        if ($trade->trade_type == 2)//卖单
        {
            foreach ($order as $key=>$value){
                if ($value['trade_msg']['trade_type'] != 2){
                    unset($order[$key]);
                }
            }

            if (count($order) >= $c2cSetting['business_sell_order_limit'])
                return response()->json(['status_code'=>1065,'message'=>'有待处理的订单']);


        }


        return $next($request);
    }
}
