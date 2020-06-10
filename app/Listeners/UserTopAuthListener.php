<?php

namespace App\Listeners;

use App\Events\UserTopAuthBehavior;
use App\Model\SettingTJReward;
use App\Model\SettingTopAuthReward;
use App\Model\User;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserTopAuthListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //用户高级认证监听
    }

    /**
     * Handle the event.
     *
     * @param  UserTopAuthBehavior  $event
     * @return void
     */
    public function handle(UserTopAuthBehavior $event)
    {
        //高级认证奖励
        $user = User::find($event->userId);
        if ($user->pid == 0) return;

        $this->pUserReward($user->pid);

        $settings = (new SettingTopAuthReward())->getAll();

        foreach ($settings as $setting) {
            if ($setting->number == 0) return;


            $wallet = (new WalletDetail())->getOneRecord($event->userId, $setting->coin_id);

            if (!$wallet) return;
            $wallet->addUsableBalance($setting->coin_id, $event->userId, $setting->number);

            (new WalletFlow())->insertOne($event->userId, $wallet->wallet_id, $setting->coin_id, $setting->number, 24, 1, '邀请注册奖励', 1);



        }

        return;


    }


    //上级推荐奖励
    public function pUserReward($pid)
    {

//        $user = User::find($pid);
//        if (!$user->pid) return;
//        $s_num = User::where(['pid'=>$user->user_id])->count();
        $s_user_ids = User::where(['pid'=>$pid])->pluck('user_id')->toArray();

        $usableNum = User::whereIn('user_id',$s_user_ids)->where(['user_auth_level'=>2])->count();
        if ($usableNum == 0) return;

        $settings = (new SettingTJReward())->getAll($usableNum);

        foreach ($settings as $setting){
            if ($setting->reward_number == 0) return;

            $wallet = (new WalletDetail())->getOneRecord($pid,$setting->reward_coin_id);

            if (!$wallet) return;
            $wallet->addUsableBalance($setting->reward_coin_id,$pid,$setting->reward_number);

            (new WalletFlow())->insertOne($pid,$wallet->wallet_id,$setting->reward_coin_id,$setting->reward_number,25,1,'推荐奖励',1);
        }



        return;


    }



}
