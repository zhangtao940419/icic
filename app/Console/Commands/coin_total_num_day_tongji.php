<?php

namespace App\Console\Commands;

use App\Model\CoinTotalDayTongji;
use App\Model\CoinType;
use Illuminate\Console\Command;

class coin_total_num_day_tongji extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coin_total_num_day_tongji';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每日统计币余额';

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
    public function handle(CoinType $coinType,CoinTotalDayTongji $coinTotalDayTongji)
    {
        //
        $coins = $coinType->all();


        foreach ($coins as $coin){
            $ut = (new CoinType())->userTotalAmount($coin->coin_id);
            $ct = (new CoinType())->feeAmount($coin->coin_id);
            (new CoinTotalDayTongji())->insertOne($coin->coin_id,$ut,$ct,$ut + $ct);


        }

        return;

    }
}
