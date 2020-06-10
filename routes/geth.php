<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/6/27
 * Time: 13:46
 */

Route::get('geth','Web\GethController@personal');
Route::get('transaction','Web\GethController@transaction');
Route::get('balance','Web\GethController@balance');
Route::get('contract','Web\GethController@contract');