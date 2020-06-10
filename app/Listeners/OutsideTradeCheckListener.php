<?php

namespace App\Listeners;

use App\Events\OutsideOrderConfirmBehavior;
use App\Model\UserDatum;
use App\Server\OutsideTrade\Dao\OutsideTrade;
use App\Server\OutsideTrade\Dao\OutsideTradeOrderDao;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OutsideTradeCheckListener
{

    protected $outsideTradeDao;
    protected $outsideTradeOrderDao;
    protected $datum;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(OutsideTrade $outsideTrade,OutsideTradeOrderDao $outsideTradeOrderDao,UserDatum $datum)
    {
        //
        $this->outsideTradeDao = $outsideTrade;
        $this->outsideTradeOrderDao = $outsideTradeOrderDao;
        $this->datum = $datum;
    }

    /**
     * Handle the event.
     *
     * @param  OutsideOrderConfirmBehavior  $event
     * @return void
     */
    public function handle(OutsideOrderConfirmBehavior $event)
    {

        //
        $order = $this->outsideTradeOrderDao->getRecord($event->orderId,['trade_id']);
        $trade = $this->outsideTradeDao->getTrade($order->trade_id);

        $this->addOrderNum($order->user_id);$this->addOrderNum($order->trade_user_id);

        if (in_array($trade->trade_status,[-1,0])) return;
        if (bccomp($trade->trade_left_number,0,8) != 0) return;

        $unFinishedOrderNum = $this->outsideTradeOrderDao->where(['trade_id'=>$order->trade_id])->whereIn('order_status',[1,2])->count();
        if ($unFinishedOrderNum == 0){
            $trade->update(['trade_status'=>2]);
        }


    }


    //增加交易数
    public function addOrderNum($userId)
    {
        return $this->datum->addTradeTotalNum($userId);
    }

    //增加积分
    public function addGrade()
    {

    }






}
