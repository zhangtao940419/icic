<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "Web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});
Route::get('/',function(){
    $fp=fopen('lock.txt','a+');
    if (flock($fp,LOCK_EX)){
        sleep(10);
        fwrite($fp,"test");
        flock($fp,LOCK_UN);
    }else{
        echo 'Couldnt lock the file !';
}
fclose($fp);
});
*/

require_once "geth.php";
require_once "bitcoin.php";
//require_once "appapi.php";
//require_once "admin_login.php";

require_once 'admin_route.php';



Route::get('news/index','Web\News\NewsController@index');
Route::get('news/detail','Web\News\NewsController@detail');

Route::get('news/daily_express','Web\News\NewsController@daily_express');

Route::get('help/index','Web\Home\HelpController@index');//

Route::get('register','Web\Home\HelpController@webRegister');

/*
Route::get('phpinfo', function () {
    return phpinfo();
}); */

