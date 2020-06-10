<?php

namespace App\Jobs;

use App\Events\OutsideOrderConfirmBehavior;
use App\Model\CenterWalletRecord;
use App\Server\OutsideTrade\Dao\OutsideTradeOrderDao;
use App\Server\OutsideTrade\Dao\OutsideWalletDao;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\RedisTool;
use App\Traits\Tools;
use Illuminate\Support\Facades\DB;

class SellAutoSendCoin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,RedisTool,Tools;

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
        $this->outsideTradeOrder = new OutsideTradeOrderDao();
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
        if ($order->order_status != 2) return;
        if ($order->order_type == 0){
            $this->handleUserBuyOrder($order);
        }else{
            $this->handleUserSellOrder($order);
        }
    }



    //用户是买家
    private function handleUserBuyOrder($order){

        DB::beginTransaction();

        $order = $this->outsideTradeOrder->getRecord($this->orderId, ['*'], 1);

        $trade = $this->outsideTradeDao->getTrade($order->trade_id, ['*'], 1);

        $result1 = $order->update(['order_status' => 3]);

        $userWallet = $this->outsideWalletDao->getOneRecord($order->user_id, $order->coin_id, 1);
        $tradeUserWallet = $this->outsideWalletDao->getOneRecord($order->trade_user_id, $order->coin_id, 1);

        $centerWalletRecord = new CenterWalletRecord();

        $sellerFee = bcmul($trade->trade_fee, $order->order_coin_num, 8);
        $sellerLockAmount = bcadd($sellerFee, $order->order_coin_num, 8);
        $result2 = $tradeUserWallet->decrement('wallet_freeze_balance', $sellerLockAmount);
        $centerWalletRecord->saveOneRecord($order->coin_id, $sellerFee, '场外手续费', $order->trade_user_id, 1);//中央钱包收入

        $buyerFee = bcmul($order->order_fee, $order->order_coin_num, 8);
        $buyerAmount = bcsub($order->order_coin_num, $buyerFee, 8);
        $result3 = $userWallet->increment('wallet_withdraw_balance', $buyerAmount);
        $centerWalletRecord->saveOneRecord($order->coin_id, $buyerFee, '场外手续费', $order->trade_user_id, 1);//中央钱包收入
        $result4 = 1;
        //if (bccomp($trade->trade_left_number,0,8) == 0) $result4 = $trade->update(['trade_status'=>2]);
        if ($result1 && $result2 && $result3 && $result4) {
            DB::commit();
            //此处应分发交易完成事件
            event(new OutsideOrderConfirmBehavior($order->order_id));
            return;
        }
        DB::rollBack();
        return;

    }

    //用户是卖家
    private function handleUserSellOrder($order){
        DB::beginTransaction();

        $order = $this->outsideTradeOrder->getRecord($this->orderId, ['*'], 1);

        $trade = $this->outsideTradeDao->getTrade($order->trade_id, ['*'], 1);

        $result1 = $order->update(['order_status' => 3]);

        $userWallet = $this->outsideWalletDao->getOneRecord($order->user_id, $order->coin_id, 1);
        $tradeUserWallet = $this->outsideWalletDao->getOneRecord($order->trade_user_id, $order->coin_id, 1);

        $centerWalletRecord = new CenterWalletRecord();

        $sellerFee = bcmul($order->order_fee, $order->order_coin_num, 8);
        $sellerLockAmount = bcadd($sellerFee, $order->order_coin_num, 8);
        $result2 = $userWallet->decrement('wallet_freeze_balance', $sellerLockAmount);
        $centerWalletRecord->saveOneRecord($order->coin_id, $sellerFee, '场外手续费', $order->user_id, 1);//中央钱包收入

        $buyerFee = bcmul($trade->trade_fee, $order->order_coin_num, 8);
        $buyerAmount = bcsub($order->order_coin_num, $buyerFee, 8);
        $result3 = $tradeUserWallet->increment('wallet_withdraw_balance', $buyerAmount);
        $centerWalletRecord->saveOneRecord($order->coin_id, $buyerFee, '场外手续费', $order->trade_user_id, 1);//中央钱包收入
        $result4 = 1;
        //if (bccomp($trade->trade_left_number,0,8) == 0) $result4 = $trade->update(['trade_status'=>2]);
        if ($result1 && $result2 && $result3 && $result4) {
            DB::commit();
            //交易完成事件
            event(new OutsideOrderConfirmBehavior($order->order_id));
            return;
        }
        DB::rollBack();
        return;

    }







}
