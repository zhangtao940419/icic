<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\Tools;
use App\Traits\RedisTool;

class GetWeek extends Command
{
    use Tools, RedisTool;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getweek';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取今天星期';

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
    public function handle()
    {
        $this->info("开始获取今天星期...");

        $week = $this->getToday();

        $this->stringSet('this_week',(string)$week);

        $this->info("存入redis成功");
    }
}
