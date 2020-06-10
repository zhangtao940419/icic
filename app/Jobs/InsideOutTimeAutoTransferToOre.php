<?php

namespace App\Jobs;

use App\Model\C2c_User_Last_Trade_Time;
use App\Model\OrePoolTransferRecord;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class InsideOutTimeAutoTransferToOre implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $userid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userid,$delay = 1)
    {
        //

        $this->userid = $userid;

        $this->delay($delay);
        $this->onQueue('tts');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(WalletDetail $walletDetail,C2c_User_Last_Trade_Time $c2c_User_Last_Trade_Time)
    {
        //用户长时间未入金自动把资产转入矿池
        try{

            $wallet = $walletDetail->getOneRecord($this->userid,8);
            if ($wallet->user->is_business || $wallet->user->is_special_user || !$wallet->user->c2c_long_time_not_buy_status) return;

            if ($c2c_User_Last_Trade_Time->getUserLastTime($wallet->user->user_id) > time()){
                $wallet->user->update(['c2c_long_time_not_buy_status'=>0]); return;
            }



            $amount = $wallet->wallet_usable_balance;
            $wAmount = $wallet->wallet_withdraw_balance;



            if ($amount > 0){
                $wallet->decrement('wallet_usable_balance',$amount);
                $wallet->increment('ore_pool_balance',$amount);
                (new OrePoolTransferRecord())->insertOne($wallet->wallet_id,$this->userid,$wallet->coin_id,$amount,8);
                (new WalletFlow())->insertOne($wallet->user_id,$wallet->wallet_id,$wallet->coin_id,$amount,17,2,'超时自动转入矿池',1);
            }

            if ($wAmount > 0){
                $wallet->decrement('wallet_withdraw_balance',$wAmount);
                $wallet->increment('ore_pool_balance',$wAmount);
                (new OrePoolTransferRecord())->insertOne($wallet->wallet_id,$this->userid,$wallet->coin_id,$wAmount,8);
                (new WalletFlow())->insertOne($wallet->user_id,$wallet->wallet_id,$wallet->coin_id,$wAmount,17,2,'超时自动转入矿池',2);
            }



            return;

        }catch (\Exception $exception){

            self::dispatch($this->userid);return;


        }









    }
}
