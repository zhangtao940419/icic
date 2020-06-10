<?php

namespace App\Jobs;

use App\Model\TransferLockRecord;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class transfer_lock_auto_free implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lid,$time)
    {
        //
        $this->lid = $lid;
        $this->delay($time);
        $this->onQueue('tts');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(WalletDetail $walletDetail,TransferLockRecord $transferLockRecord,WalletFlow $walletFlow)
    {
        //
        try{

//        DB::beginTransaction();
            $lrecord = $transferLockRecord->find($this->lid);
            if (!$lrecord) return;
            $wallet = $walletDetail->find($lrecord->wallet_id);
            $wallet->decrement('transfer_lock_balance',$lrecord->amount);
            $wallet->increment('wallet_withdraw_balance',$lrecord->amount);
//        $walletFlow->insertOne($wallet->user_id,$wallet->wallet_id,$wallet->coin_id,$lrecord->amount,16,1,'解锁',2);
            $lrecord->delete();
            if ($transferLockRecord->find($this->lid)) dispatch(new transfer_lock_auto_free($this->lid,10));
//        DB::commit();
            return;

        }catch (\Exception $exception){
            dispatch(new transfer_lock_auto_free($this->lid,2));
            return;
        }




    }
}
