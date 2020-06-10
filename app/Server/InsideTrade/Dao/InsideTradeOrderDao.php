<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 11:01
 */

namespace App\Server\InsideTrade\Dao;

use App\Model\InsideSetting;
use App\Model\InsideTradeOrder;
use App\Traits\RedisTool;

class InsideTradeOrderDao
{
    use RedisTool;
    //费率
    private $rate=0.007;
    protected $insideTradeOrder;

    public function __construct(InsideTradeOrder $insideTradeOrder,InsideSetting $insideSetting)
    {
        $this->insideTradeOrder = $insideTradeOrder;
//        empty($this->redisHgetAll('INSIDE_RATE')['rate']) ? : $this->rate = $this->redisHgetAll('INSIDE_RATE')['rate'];
        if (request('base_coin_id') && request('exchange_coin_id')) $this->rate = $insideSetting->getFee(request('base_coin_id'),request('exchange_coin_id'));

    }

    public function getInsideTradeOrder(){
        return $this->insideTradeOrder;
    }

    public function getOneRecord($where){
        return $this->insideTradeOrder->where($where)->first()->toArray();
    }

    public function getManyRecord($where){
        return $this->insideTradeOrder->where($where)->get()->toArray();
    }

    public function insertOneRecord($data){
        return $this->insideTradeOrder::create($data);
    }

    public function updateOneRecord($where,$update_data){
        return $this->insideTradeOrder->where($where)->update($update_data);
    }

    // 买单卖单的匹配记录入库
    public function saveInsideTradeOrder($buyInSideOrder,$sellInSideOrder,$trade_num,$unit_price,$traded_type){

        $tradeOrder['buy_order_number']=$buyInSideOrder['order_number'];
        $tradeOrder['sell_order_number']=$sellInSideOrder['order_number'];
        $tradeOrder['buy_id']=$buyInSideOrder['buy_id'];
        $tradeOrder['sell_id']=$sellInSideOrder['sell_id'];
        $tradeOrder['buy_user_id']=$buyInSideOrder['user_id'];
        $tradeOrder['sell_user_id']=$sellInSideOrder['user_id'];
        $tradeOrder['unit_price']=$unit_price;
        $tradeOrder['base_coin_id']=$buyInSideOrder['base_coin_id'];
        $tradeOrder['exchange_coin_id']=$buyInSideOrder['exchange_coin_id'];
        $tradeOrder['trade_num']=$trade_num;
        $tradeOrder['trade_total_money']=$trade_num;
        $tradeOrder['trade_poundage']=$trade_num*$this->rate;
        $tradeOrder['traded_type'] = $traded_type;


        return  $this->insideTradeOrder::create($tradeOrder);
    }

}
