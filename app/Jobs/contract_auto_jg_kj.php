<?php

namespace App\Jobs;

use App\Model\CenterWallet;
use App\Model\ContractActivity;
use App\Model\ContractPriceFloat;
use App\Model\ContractUserBuyRecords;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class contract_auto_jg_kj implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $activityId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($activityId,$delay)
    {
        //
        $this->activityId = $activityId;
        $this->delay($delay);
        $this->onQueue('tts');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ContractActivity $contractActivity,ContractUserBuyRecords $contractUserBuyRecords,WalletDetail $walletDetail,ContractPriceFloat $contractPriceFloat)
    {
        //
        $activity = $contractActivity->find($this->activityId);
        if ($activity->jg_status == 1) return;

        //交割

        if ($activity->now_price > $activity->last_price){
            $jg_status = 1;
        }elseif ($activity->now_price == $activity->last_price){
            $jg_status = 2;
        }else {
            $jg_status = 3;
        }
        $activity->update(['jg_status'=>$jg_status]);


        //下一期创建
        $new_jg_times = strtotime($activity->jg_time) + 6 + (5*60);
        (new ContractActivity())->insertOne($activity->activity_no + 1,$activity->coin_id,$activity->now_price,$contractPriceFloat->getNewestPrice(),date('Y-m-d H:i:s',$new_jg_times));


        //开奖
        $recs = $contractUserBuyRecords->getActivityRecord($this->activityId);

        foreach ($recs as $rec){
            if ($rec->type != $jg_status){

                $rec->update(['reward' => -1*$rec->amount]);

                continue;
            }


            $wallet = $walletDetail->getOneRecord($rec->user_id,$activity->coin_id);
            $fee = 0.01*$rec->amount;
            $wallet->addUsableBalance($activity->coin_id,$rec->user_id,($rec->amount * 2)-$fee);
            $rec->update(['reward' => 2*$rec->amount,'fee' => $fee]);
            (new WalletFlow())->insertOne($rec->user_id,$wallet->wallet_id,$activity->coin_id,($rec->amount * 2)-$fee,28,1,'合约盈亏',1,1,$fee);
            (new CenterWallet())->addCenterCoinBalance($activity->coin_id,$fee);


        }

        $activity->update(['status'=>1]);




        //销毁变量
        unset($activity);unset($contractActivity);unset($contractUserBuyRecords);unset($walletDetail);unset($wallet);unset($fee);




    }




}
