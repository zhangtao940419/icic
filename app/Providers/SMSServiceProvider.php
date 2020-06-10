<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Server\SMSServers\SMS;

class SMSServiceProvider extends ServiceProvider
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
        $this->app->bind('sms',function() {
            return new SMS();
        });
    }
}
