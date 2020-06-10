<?php

namespace App\Console\Commands;

use App\Server\InsideTrade\InsideTradeServer;
use App\Traits\RedisTool;
use Illuminate\Console\Command;

class SaveDayIcicPrice extends Command
{
    use
    RedisTool;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SaveDayIcicPrice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '把icic对qc的价格保存到redis';

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
    public function handle(InsideTradeServer $insideTradeServer)
    {
        //
        $allInsideCoin = $insideTradeServer->getAllCoin();

        $price = 0;
        foreach ($allInsideCoin as $value){
            if ($value['exchange_coin_name'] == 'ICIC' && $value['base_coin_name'] == 'QC' && $value['switch'] == 1){
                $price = $value['current_price'];
            }
        }

        //把当天价格转成qc=>icic刷入redis
        $qc_to_icci_rate = bcdiv(1,$price,2);
        $this->stringSet('qc_to_icic_sto_rate',$qc_to_icci_rate);


    }
}
