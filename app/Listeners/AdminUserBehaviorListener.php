<?php

namespace App\Listeners;

use App\Events\AdminUserBehavior;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminUserBehaviorListener
{
    protected $adminUserBehavior;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(\App\Model\Admin\AdminUserBehavior $adminUserBehavior)
    {
        //
        $this->adminUserBehavior = $adminUserBehavior;
    }

    /**
     * Handle the event.
     *
     * @param  AdminUserBehavior  $event
     * @return void
     */
    public function handle(AdminUserBehavior $event)
    {
        //
        return $this->adminUserBehavior->saveOneRecord($event->data);
    }
}
