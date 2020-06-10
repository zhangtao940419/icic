<?php

namespace App\Console\Commands;

use App\Model\User;
use App\Traits\RedisTool;
use Illuminate\Console\Command;

class CreateInvitaRanking extends Command
{
    use RedisTool;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createInvitaRanking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $users = User::query()->whereHas('wallet_flows', function ($q) {
            $q->where('coin_id',8)->where('flow_type',13);
        })->cursor();

        foreach ($users as $user) {
            $amount = $user->wallet_flows()->where(['flow_type'=>13,'coin_id'=>8])->sum('flow_number');

            $this->setZincrbyScore('icicInvitaRanking',$amount,'uid_'.$user->user_id);

            echo "OK--".$user->user_id."\r\n";
        }

        echo "success";
    }
}
