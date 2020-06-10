<?php

namespace App\Console\Commands;

use App\Jobs\InsideOutTimeAutoTransferToOre;
use App\Model\C2c_User_Last_Trade_Time;
use App\Model\C2CSetting;
use App\Model\C2CTrade;
use App\Model\User;
use App\Model\WalletDetail;
use Illuminate\Console\Command;

class AutoCheckLongTimeNoBuyUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoCheckLongTimeNoBuyUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每日监测超过规定时间未入金的用户';

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
    public function handle(C2CSetting $c2CSetting,C2CTrade $c2CTrade,C2c_User_Last_Trade_Time $c2c_User_Last_Trade_Time,WalletDetail $walletDetail,User $user)
    {
        //
        $setting = $c2CSetting->getOneRecord();//array

        if ($setting['long_time_not_buy_check_day'] == 0) return;

        $s_timestamps = $setting['long_time_not_buy_check_day'] * 24* 3600;
        if ((time() - $setting['start_check_time']) < $s_timestamps) return;

        //查出所有商家
        $sahgnjia_user_ids = $user->where(['is_business'=>1])->pluck('user_id')->toArray();

        //查出所有没有交易的用户
        $has_user_ids = $c2c_User_Last_Trade_Time->where(['type'=>1])->pluck('user_id')->toArray();
        $has_user_ids = array_merge($sahgnjia_user_ids,$has_user_ids);
        if ((time() - $setting['start_check_time']) > $s_timestamps) {
            $userIds = $walletDetail->where(['coin_id' => 8])->whereNotIn('user_id',$has_user_ids)->where(function ($q) {
                $q->where('wallet_usable_balance','>',0)->orWhere('wallet_withdraw_balance','>',0);
            })->pluck('user_id')->toArray();
            $user->whereNotIn('user_id',$has_user_ids)->where(['is_business'=>0,'is_special_user'=>0,'c2c_long_time_not_buy_status'=>0])->update(['c2c_long_time_not_buy_status'=>1]);
            foreach ($userIds as $userId){
                dispatch(new InsideOutTimeAutoTransferToOre($userId));
            }


        }

        $last_timestamp = time() - $s_timestamps;

        $n_user_ids = $c2c_User_Last_Trade_Time->where('timestamp','<',$last_timestamp)->where(['type'=>1])->pluck('user_id')->toArray();
        $n_user_ids = $walletDetail->where(['coin_id' => 8])->whereIn('user_id',$n_user_ids)->where(function ($q) {
            $q->where('wallet_usable_balance','>',0)->orWhere('wallet_withdraw_balance','>',0);
        })->pluck('user_id')->toArray();
        $user->whereIn('user_id',$n_user_ids)->where(['is_business'=>0,'is_special_user'=>0,'c2c_long_time_not_buy_status'=>0])->update(['c2c_long_time_not_buy_status'=>1]);

        foreach ($n_user_ids as $userId){
            dispatch(new InsideOutTimeAutoTransferToOre($userId));
        }






    }
}
