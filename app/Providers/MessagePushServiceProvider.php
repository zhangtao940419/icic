<?php

namespace App\Providers;

use App\Server\MessagePushServers\MessagePush;
use Illuminate\Support\ServiceProvider;

class MessagePushServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('messagePush',function(){
            return new MessagePush();
        });
    }
}
