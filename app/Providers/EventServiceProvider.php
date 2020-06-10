<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        'App\Events\SendEmailEvent' => [
            'App\Listeners\SendEmailCodeListener',//发送邮件
        ],
        'App\Events\AdminUserBehavior' => [
            'App\Listeners\AdminUserBehaviorListener',//后台用户行为记录
        ],
        'App\Events\UserRegisterBehavior' => [
            'App\Listeners\MessagePushRegisterListener',//注册极光im
        ],
        'App\Events\OutsideOrderConfirmBehavior' => [//场外交易完成事件
            'App\Listeners\OutsideTradeCheckListener',//
        ],
        'App\Events\STOBuyBehavior' => [//sto购买事件
            'App\Listeners\STOBuyListener',//
        ],
        'App\Events\UserTopAuthBehavior' => [//用户高级认证事件
            'App\Listeners\UserTopAuthListener',//
        ],




    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
