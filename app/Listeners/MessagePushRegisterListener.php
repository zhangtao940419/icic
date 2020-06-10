<?php

namespace App\Listeners;

use App\Events\UserRegisterBehavior;
use App\Model\SettingTJReward;
use App\Model\User;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessagePushRegisterListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegisterBehavior  $event
     * @return void
     */
    public function handle(UserRegisterBehavior $event)
    {
        //



    }
}
