<?php

namespace App\Console\Commands;

use App\Handlers\Helpers;
use Illuminate\Console\Command;

class InsertDatabase60 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertTable_one_hour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每一小时按时插入数据库';

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
        $this->info("开始每一小时写入...");

        $helpers->getAll('60');

        $this->info("写入成功！");
    }
}
