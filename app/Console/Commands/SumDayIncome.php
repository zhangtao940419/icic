<?php

namespace App\Console\Commands;

use App\Model\CenterWalletDayIncome;
use App\Model\CoinType;
use App\Model\WalletDetail;
use App\Server\InsideTrade\InsideTradeServer;
use App\Traits\RedisTool;
use Illuminate\Console\Command;

class SumDayIncome extends Command
{
    use
    RedisTool;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SumDayIncome';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '开始计算今日中央钱包的QC收入';

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
    public function handle(CenterWalletDayIncome $centerWalletDayIncome,InsideTradeServer $insideTradeServer,WalletDetail $walletDetail,CoinType $coinType)
    {
        //Helpers $helpers开始计算今日中央钱包的QC收入并刷入redis
        $this->info("开始计算今日中央钱包的QC收入...");

        $rkey = 'day_income_qc_to_icic';

        $todayIncome = $centerWalletDayIncome->getTodayIncome();

        $allInsideCoin = $insideTradeServer->getAllCoin();

        $price = 0;
        foreach ($allInsideCoin as $value){
            if ($value['exchange_coin_name'] == 'ICIC' && $value['base_coin_name'] == 'QC' && $value['switch'] == 1){
                $price = $value['current_price'];
            }
        }



        $this->stringSetex($rkey,24*3600,bcdiv($todayIncome,$price,0));




        $this->info("更新成功！");
    }
}
