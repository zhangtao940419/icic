<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 12:36
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Http\Controllers\Web\Api\V1'], function ($api) {
        require_once "ljm_api.php";
        require_once "zt_api.php";
        require_once "common_api.php";
    });
    $api->group(['namespace' => 'App\Http\Controllers\Web\Api\V1','middleware'=>'auth.api'], function ($api) {


    });
});

