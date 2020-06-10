<?php

namespace App\Console\Commands;

use App\Model\CoinType;
use App\Model\WalletDetail;
use App\Traits\RedisTool;
use Illuminate\Console\Command;

class OrePoolDayAutoFree extends Command
{
    use
    RedisTool;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OrePoolDayAutoFree';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '结算每日手续费收入释放用户矿池';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(WalletDetail $walletDetail,CoinType $coinType)
    {
        //

        $this->info("开始每天23:25释放...");

        //计算用户矿池总金额
        $coin = $coinType->getRecordByCoinName(env('COIN_SYMBOL'));
        $userOrePoolTotal = $walletDetail->getOrePoolTotalAmount($coin->coin_id);
        $this->stringSetex('user_ore_pool_total',24*3600,bcadd($userOrePoolTotal,0,0));

        $wallets = $walletDetail->where(['coin_id' => $coin->coin_id])->where('ore_pool_balance' ,'>' ,'0')->select(['wallet_id','ore_pool_balance'])->get();

        foreach ($wallets as $wallet){

            dispatch(new \App\Jobs\OrePoolDayAutoFree($wallet->wallet_id,$wallet->ore_pool_balance));


        }


        $this->info("释放成功！");



    }
}
