<?php

namespace App\Console;

use App\Console\Commands\AutoCheckLongTimeNoBuyUser;
use App\Console\Commands\coin_total_num_day_tongji;
use App\Console\Commands\Contract;
use App\Console\Commands\InsideTradeTimeCheck;
use App\Console\Commands\OrePoolDayAutoFree;
use App\Console\Commands\SaveDayIcicPrice;
use App\Console\Commands\SumDayIncome;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Handlers\Helpers;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\InsertDatabase1::class,
        Commands\InsertDatabase5::class,
        Commands\InsertDatabase15::class,
        Commands\InsertDatabase30::class,
        Commands\InsertDatabase60::class,
        Commands\GetWeek::class,
        Commands\SumMoney::class,
        Commands\addChangePrice::class,
        Commands\StoProjectAutoUpdateStatus::class,
        SumDayIncome::class,
        OrePoolDayAutoFree::class,
        SaveDayIcicPrice::class,
//        InsideTradeTimeCheck::class
        AutoCheckLongTimeNoBuyUser::class,
        coin_total_num_day_tongji::class,
        Contract::class

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('InsertTable_one_min')->everyMinute();
        $schedule->command('InsertTable_five_min')->everyFiveMinutes();
        $schedule->command('InsertTable_fifteen_min')->everyFifteenMinutes();
        $schedule->command('InsertTable_thirty_min')->everyThirtyMinutes();
        $schedule->command('InsertTable_one_hour')->hourly();
        $schedule->command('getweek')->daily();
        $schedule->command('SumMoney')->dailyAt('23:00');
        $schedule->command('addChangePrice')->dailyAt('00:02');
        $schedule->command('sto_status_update')->everyMinute();
        $schedule->command('SumDayIncome')->dailyAt('23:20');
//        $schedule->command('SumDayIncome')->everyMinute();
        $schedule->command('OrePoolDayAutoFree')->dailyAt('23:22');
//        $schedule->command('OrePoolDayAutoFree')->everyMinute();
        $schedule->command('SaveDayIcicPrice')->dailyAt('23:59');
//        $schedule->command('InsideTradeTimeCheck')->dailyAt('23:58');
//        $schedule->command('AutoCheckLongTimeNoBuyUser')->dailyAt('23:55');
       // $schedule->command('AutoCheckLongTimeNoBuyUser')->everyThirtyMinutes();

//        $schedule->command('AutoCheckLongTimeNoBuyUser')->everyMinute();
        $schedule->command('coin_total_num_day_tongji')->dailyAt('21:30');
        $schedule->command('AutoCheckWithdrawOrder')->everyMinute();//自动审核提币

        $schedule->command('contract')->everyMinute();
    }




    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
