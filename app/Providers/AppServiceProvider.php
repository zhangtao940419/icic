<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        \Carbon\Carbon::setLocale('zh');
        \App\Model\Admin\Category::observe(\App\Observers\CategoryObserve::class);
        \App\Model\CoinType::observe(\App\Observers\CoinObserve::class);
        \App\Model\CenterWallet::observe(\App\Observers\CenterWalletObserver::class);
        \App\Model\WalletDetail::observe(\App\Observers\WalletDetailObserver::class);
        \App\Model\User::observe(\App\Observers\UserObserver::class);
        \App\Model\OutsideWalletDetail::observe(\App\Observers\OutsideWalletDetailObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        app('api.exception')->register(function (\Exception $exception) {
            $request = \Request::capture();
            return app('App\Exceptions\Handler')->render($request, $exception);
        });
    }
}
