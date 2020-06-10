<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\C2CTradeOrder;
use App\Model\C2CTrade;
use Illuminate\Support\Facades\DB;
use App\Model\WalletDetail;

class C2CAutoCancelOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $orderId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderId)
    {
        //
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        DB::beginTransaction();
        $order = C2CTradeOrder::where(['order_id'=>$this->orderId,'is_usable'=>1])->lockForUpdate()->first();
        if (!$order || $order->order_status!=1){
            DB::rollBack();return;
        }
        $trade = C2CTrade::find($order->trade_id);
        $wallet = WalletDetail::select('wallet_usable_balance','wallet_id')->where(['user_id'=>$order->business_user_id,'coin_id'=>$trade->coin_id,'is_usable'=>1])->lockForUpdate()->first();

        if ($order && ($order->order_status == 1)){

            if (
                $order->update(['order_status'=>4])
                && $trade->update(['trade_status'=>0])
                && $wallet->increment('wallet_withdraw_balance',$trade->trade_number)
                && $wallet->decrement('wallet_freeze_balance',$trade->trade_number)
            ){
                DB::commit();return;
            }
            DB::rollBack();return;

        }
        DB::rollBack();return;

    }
}
