<?php

namespace App\Console\Commands;

use App\Handlers\Helpers;
use Illuminate\Console\Command;

class SumMoney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SumMoney';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '开始计算各个货币的总和,并更新数据';

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
    public function handle(Helpers $helpers)
    {
        $this->info("开始每天23时更新数据...");

        $helpers->summary();

        $this->info("更新成功！");
    }
}
