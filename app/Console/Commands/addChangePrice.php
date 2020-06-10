<?php

namespace App\Console\Commands;

use App\Handlers\Helpers;
use Illuminate\Console\Command;

class addChangePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addChangePrice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成变动的价格';

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
        $this->info("开始每分钟写入...");

        $helpers->getCurrentPrice();

        $this->info("写入成功！");
    }
}
