<?php

namespace App\Listeners;

use App\Events\SendEmailEvent;
use App\Traits\Tools;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailCodeListener implements ShouldQueue
{
    use Tools;
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
     * @param  SendEmailEvent  $event
     * @return void
     */
    public function handle(SendEmailEvent $event)
    {
        //sleep(5);
        $this->sendEmailMessage($event->email,$event->code);
    }
}
