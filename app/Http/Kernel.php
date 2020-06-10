<?php

namespace App\Http;

use App\Http\Middleware\AuthMiddleware\InsideUser;
use App\Http\Middleware\CheckBankCard;
use App\Http\Middleware\CheckProtectMode;
use App\Http\Middleware\OutsideMiddleware\CheckCoinWallet;
use App\Http\Middleware\OutsideMiddleware\CheckOutsideWallets;
use App\Http\Middleware\OutsideMiddleware\UpdateUserWallets;
use App\Http\Middleware\Sto\CheckDayBuyTime;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\Cors::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'Web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'auth.api' => \App\Http\Middleware\ValidateApi::class,
        'admin.login' => \App\Http\Middleware\LoginMiddleware::class,
        'admin.cors' => \App\Http\Middleware\Cors::class,
        'checkWallet' => \App\Http\Middleware\CheckUserWallet::class,
        'updateUserWallet' => \App\Http\Middleware\UpdateUserWallet::class,
        'checkCoinWallet' => \App\Http\Middleware\CheckCoinWallet::class,
        'updateUserWallets' => \App\Http\Middleware\UpdateUserWallets::class,
        'updateWithdrawStatus' => \App\Http\Middleware\UpdateWithdrawStatus::class,

        'checkBankCard' => \App\Http\Middleware\CheckBankCard::class,
        'checkBusiness' => \App\Http\Middleware\CheckBusiness::class,
        'checkPrimaryAuth' => \App\Http\Middleware\AuthMiddleware\PrimaryAuth::class,
        'checkTopAuth' => \App\Http\Middleware\AuthMiddleware\TopAuth::class,

        'checkC2CTrade' => \App\Http\Middleware\C2C\C2CSaveTrade::class,
        'checkC2CBusinessRecept' => \App\Http\Middleware\C2C\C2CBusinessReceptOrder::class,

        'checkPayPassword' => \App\Http\Middleware\AuthMiddleware\PayPassword::class,
        //检查权限
        'CheckPermission' => \App\Http\Middleware\CheckPermission::class,
        //检查版本号
        'CheckVersion' => \App\Http\Middleware\CheckVersion::class,

        'checkOutsideWallets' => CheckOutsideWallets::class,
        'checkOutsideWalletAddress' => CheckCoinWallet::class,
        'UpdateOutsideWallets' => UpdateUserWallets::class,

        'InsideUser' => InsideUser::class,

        'CheckProtectMode' => CheckProtectMode::class,

        'STOCheckDayBuyTime' => CheckDayBuyTime::class,

    ];
}
