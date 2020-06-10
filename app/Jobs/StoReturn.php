<?php

namespace App\Jobs;

use App\Model\StoUserWallet;
use App\Model\UserBuyStoCoinRecord;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class StoReturn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    //sto返还

    protected $buyRecordId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($buyRecordId)
    {
        //
        $this->buyRecordId = $buyRecordId;

        $this->onQueue('tts');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try{
            DB::beginTransaction();

            $record = UserBuyStoCoinRecord::query()->find($this->buyRecordId);

            $baseWallet = (new WalletDetail())->getOneRecord($record->user_id,$record->base_coin_id);

            $exWallet = (new StoUserWallet())->getUserWalletByCoinId($record->user_id,$record->exchange_coin_id);

            $baseWallet->increment('wallet_usable_balance',$record->base_trade_number);
            (new WalletFlow())->insertOne($record->user_id,$baseWallet->wallet_id,$baseWallet->coin_id,$record->base_trade_number,29,1,'sto返还',1);
            $exWallet->decrement('usable_balance',$record->exchange_trade_number);


            DB::commit();return;







        }catch (\Exception $exception){
            DB::rollBack();return;
        }









    }
}
