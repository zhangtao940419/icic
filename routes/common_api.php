<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 13:20
 */




$api->get('exeption','TestController@exeption');




//第三方api服务

//获取icic汇率
$api->get('getCnyExchangeRate','OpenApiController@getCnyExchangeRate');

//获取币种实时价格
$api->get('getCoinPrice/{coin_name}','OpenApiController@getCoinPrice');

//查询是否是tts地址
$api->get('open/checkIsAddress/{address}','OpenChatApiController@checkIsAddress');
//chat转账
$api->get('open/chatRecharge','OpenChatApiController@chatRecharge');
//plc转账
$api->get('open/plcRecharge','OpenChatApiController@plcRecharge');

////获取币种价格
$api->get('getCoinCnyPrice/{coin_name}','OpenApiController@getCoinCnyPrice');

//获取深度图
$api->get('getDepth/{coin_name}','OpenApiController@getDepth');

//币价日期折线图
$api->get('getPriceLine/{coin_name}/{days}','OpenApiController@getPriceLine');