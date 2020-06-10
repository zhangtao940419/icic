<?php

namespace App\Jobs;

use App\Server\OutsideTrade\Dao\OutsideWalletDao;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\OutsideTradeOrder;
use App\Traits\RedisTool;
use Illuminate\Support\Facades\DB;
use App\Model\OutsideTrade;

class BuyAutoCancelOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,RedisTool;

    private $orderId;

    private $outsideTradeOrder;
    private $outsideTradeDao;
    private $outsideWalletDao;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderId)
    {
        $this->orderId= $orderId;
        $this->outsideTradeOrder = new OutsideTradeOrder();
        $this->outsideTradeDao = new \App\Server\OutsideTrade\Dao\OutsideTrade();
        $this->outsideWalletDao = new OutsideWalletDao();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->outsideTradeOrder->getOrderById($this->orderId);
        if ($order->order_status != 1) return;
        if ($order->order_type == 0){
            $this->cancelUserBuyOrder($order);
        }else{
            $this->cancelUserSellOrder($order);
        }
    }



    /* 判断两小时后如果没有交易则自动取消订单
     * @param
     *  trade_order:订单号信息
     *  buy_user_id：买家
     *  sell_user_id：卖家
        */
    private function cancelUserBuyOrder($order){
        DB::beginTransaction();
        $order = $this->outsideTradeOrder->getOrderById($this->orderId,['*'],1);
        if ($order->order_status != 1) return;
        $trade = $this->outsideTradeDao->getTrade($order->trade_id, ['*'], 1);

        if (
            $order->update(['order_status' => -1])
            && $trade->increment('trade_left_number', $order->order_coin_num)
        ) {
            DB::commit();
            return;
        }

        DB::rollBack();return;

    }


    private function cancelUserSellOrder($order){
        DB::beginTransaction();
        $order = $this->outsideTradeOrder->getOrderById($this->orderId,['*'],1);
        if ($order->order_status != 1) return;
        $trade = $this->outsideTradeDao->getTrade($order->trade_id, ['*'], 1);

        $fee = bcmul($order->order_coin_num, $order->order_fee, 8);
        $amount = bcadd($fee, $order->order_coin_num, 8);
        $userWallet = $this->outsideWalletDao->getOneRecord($order->user_id, $order->coin_id, 1);

        if (
            $order->update(['order_status' => -1])
            && $trade->increment('trade_left_number', $order->order_coin_num)
            && $userWallet->increment('wallet_usable_balance', $amount)
            && $userWallet->decrement('wallet_freeze_balance', $amount)
        ) {
            DB::commit();
            return;
        }
        DB::rollBack();
        return;

    }


}
