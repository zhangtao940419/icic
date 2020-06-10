<?php

namespace App\Jobs;

use App\Model\CoinType;
use App\Model\OrePoolTransferRecord;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use App\Traits\RedisTool;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrePoolDayAutoFree implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,RedisTool;
    //每日释放矿池/场内手续费

    public $walletId;

    public $balance;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($walletId,$balance)
    {
        //
        $this->walletId = $walletId;
        $this->balance = $balance;
        $this->onQueue('tts');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(WalletDetail $walletDetail)
    {
        //计算个人矿池占总矿池的比例乘以手续费收入合计icic加入用户场内钱包


//        $coin = $coinType->getRecordByCoinName(env('COIN_SYMBOL'));
        try{
            $wallet = $walletDetail->find($this->walletId);

            if ($wallet->user->is_new) return;

            $userBalance = $this->balance;
            if ($userBalance == 0) return;

            $totalDayIncome = $this->stringGet('day_income_qc_to_icic');
            $totalDayIncome = bcdiv($totalDayIncome,2,0);
            $totalOre = $this->stringGet('user_ore_pool_total');

            if ($totalDayIncome == 0 || $totalOre == 0) return;


            $rate = bcdiv($userBalance,$totalOre,8);
            if (bccomp($rate,0,8) == 0) return;

            $freeAmount = bcmul($rate,$totalDayIncome,0);

            if ($freeAmount > $userBalance) $freeAmount = $userBalance;
            if ($freeAmount < 1) return;


            $wallet->decOrePoolBalance($freeAmount);
            $wallet->addUsableBalance($wallet->coin_id,$wallet->user_id,$freeAmount);

            (new WalletFlow())->insertOne($wallet->user_id,$wallet->wallet_id,$wallet->coin_id,$freeAmount,23,1,'平台分红',1);
            (new OrePoolTransferRecord())->insertOne($wallet->wallet_id,$wallet->user_id,$wallet->coin_id,$freeAmount * -1,6);


            return;
        }catch (\Exception $exception){
            return;
        }














    }
}
