<?php

namespace App\Console\Commands;

use App\Jobs\InsideOutTimeAutoTransferToOre;
use App\Model\InsideUserLastTradeTime;
use App\Model\WalletDetail;
use App\Traits\RedisTool;
use Illuminate\Console\Command;

class InsideTradeTimeCheck extends Command
{
    use
    RedisTool;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsideTradeTimeCheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检测用户在指定间隔有没有交易';

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
    public function handle(InsideUserLastTradeTime $insideUserLastTradeTime,WalletDetail $walletDetail)
    {
        //
        //检查开关
        $swith = $this->stringGet('inside_trade_time_check_switch');
        if ($swith == null) return;

        $days = $this->stringGet('inside_trade_check_times');
        if ($days == null) $days = 5;

        $check_timestamps = $days * 24 * 60 *60;

        if ((time() - $swith) < $check_timestamps) return;

        //查询所有场内余额不为0且没有交易过的用户
        $has_userids = $insideUserLastTradeTime->pluck('user_id')->toArray();

        if (!$has_userids) return;


        $wallets = $walletDetail->whereNotIn('user_id',$has_userids)->where(['coin_id' => 8])->where('wallet_usable_balance','>',0)->get(['user_id']);

        foreach ($wallets as $wallet){
            //分发队列处理
            dispatch(new InsideOutTimeAutoTransferToOre($wallet->user_id));
        }

        //查询所有有交易但时间超过间隔时间的用户
        $last_timestamps = time() - $days * 24 * 60 *60;

        $user_ids = $insideUserLastTradeTime->where('timestamp','<',$last_timestamps)->pluck('user_id')->toArray();

        $wallets = $walletDetail->whereIn('user_id',$user_ids)->where(['coin_id' => 8])->where('wallet_usable_balance','>',0)->get(['user_id']);


        foreach ($wallets as $wallet){
            //分发队列处理
            dispatch(new InsideOutTimeAutoTransferToOre($wallet->user_id));
        }




    }
}
