<?php

namespace App\Observers;

use App\Model\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\Model\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Model\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // TODO 当更新积分时 检测用户场外等级 升级

    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Model\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Model\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Model\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
