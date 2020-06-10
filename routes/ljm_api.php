<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 13:20
 */

$api->group(['middleware'=>['auth.api','checkPrimaryAuth']], function ($api) {
    $api->post('saveOutsideOrder', 'OutsideTradeController@saveOutsideTrade');//场外发起广告，卖单或者买单
//$api->post('updateOneTradeOrder','OutsideTradeController@updateOneTradeOrder');//更新单个订单信息
    $api->get('cancelTradeOrder', 'OutsideTradeController@cancelTradeOrder');//撤销某个广告信息
    $api->get('getCoinAndArea', 'CoinType@getCoinAndWorldArae');//获取场外交易所有订单
    $api->post('saveUserOrder', 'OutsideTradeController@saveUserOrder');//保存卖单下单相关订单信息
    $api->get('userConfirmOrder', 'OutsideTradeController@userConfirmOrder');//卖单买家确认已付钱接口
    $api->get('sellConfirmSend', 'OutsideTradeController@sellConfirmSend');//卖家确认发货
    //$api->get('userGetSelfTrade', 'OutsideTradeController@userGetSelfTrade');//用户获取自己发起的广告接口
    $api->get('userGetSelfTradeOrder', 'OutsideTradeController@userGetSelfTradeOrder');//用户获取自己购买的订单接口

    $api->get('cancelUserTradeOrder', 'OutsideTradeController@cancelUserTradeOrder')->middleware('CheckProtectMode');//撤销某个具体订单

    $api->post('saveInsideTrade', 'InsideTradeController@saveInsideTrade')->middleware('CheckProtectMode','checkTopAuth','checkBankCard');/*->middleware('checkPrimaryAuth')*///场内交易下单交易
    $api->get('cancelInsideOrder', 'InsideTradeController@cancelInsideOrder')->middleware('CheckProtectMode');//取消场内订单


    //$api->get('redisTest', 'InsideTradeController@redisTest');//场内交易获取历史交易

    $api->get('getSomeUserTrade', 'OutsideTradeController@getSomeUserTrade');//场内交易获取历史交易
    $api->post('saveComplain', 'OutsideComplainController@saveComplain');//用户订单投诉

    $api->get('getInsideHistoryTrade', 'InsideTradeController@getInsideHistoryTrade')->name('getInsideHistoryTrade');//获取场内历史交易记录
//获取场内历史详细交易
    $api->get('getCarefulInsideHistoryTrade', 'InsideTradeController@getCarefulInsideHistoryTrade')->name('getCarefulInsideHistoryTrade');//获取场内历史交易记录


    //获取货币余额
    $api->get('getUserCoinBalance', 'InsideTradeController@getUserCoinBalance');
    //最近委托
    $api->get('getTrade', 'InsideTradeController@getTrade');
    //邀请页面接口
    $api->get('getUserInvitation', 'CommonController@getUserInvitation');

    //获取单个交易对价格
    $api->get('getOneTradeTeamList', 'InsideTradeController@getOneTradeTeamList');
//获取货币交易的剩余数量
    $api->get('getBalance', 'InsideTradeController@getBalance');
//获取货币描述
    $api->get('getCoinContent', 'InsideTradeController@getCoinContent');
//获取个人理财总统计数据
    $api->get('getFinancCount', 'FinancingController@getFinancCount');
//用户到期提取理财货币
    $api->get('getFinancOrder', 'FinancingController@getFinancOrder');
//用户未到期提取理财货币
    $api->get('cancelFinancOrder', 'FinancingController@cancelFinancOrder')->middleware('CheckProtectMode');
//用户理财到期提取理财货币
    $api->get('getFinancMoney', 'FinancingController@getFinancMoney')->middleware('CheckProtectMode');
//用户理财下单
    $api->post('setFinancOrder', 'FinancingController@setFinancOrder')->middleware('CheckProtectMode');
//用户获取理财历史记录
    $api->get('getFinancHistory', 'FinancingController@getHistoryOrder');
//获取理财的类型
    $api->get('getInvestType', 'FinancingController@getInvestType');
//获取理财的虚拟币类型
    $api->get('getInvestCoin', 'FinancingController@getInvestCoin');
//获取理财虚拟货币的余额
    $api->get('getInvestCoinBalance', 'FinancingController@getInvestCoinBalance');
//获取所有的理财套餐
    $api->get('getInvestSetMeal', 'FinancingController@getInvestSetMeal');
});

    $api->get('getTradeTeamList', 'InsideTradeController@getTradeTeamList');//场内交易首页
    $api->get('getOutsideAllOrder', 'OutsideTradeController@getOutsideAllOrder');//获取场外交易所有订单
    $api->get('getOneTradeOrder', 'OutsideTradeController@getOneTradeOrder');//获取单个订单信息
    $api->get('getTradeDisksurface', 'InsideTradeController@getTradeDisksurface');//场内交易获取盘面信息
    $api->get('adminGetTradeDisksurface', 'InsideTradeController@adminGetTradeDisksurface');//后台获取盘面信息
//获取特定价格用户挂单详细
    $api->get('adminGetInsideList', 'InsideTradeController@adminGetInsideList');
    $api->get('insideTradeSwitch', 'InsideTradeController@insideTradeSwitch');//
    $api->get('userGetSelfTrade', 'OutsideTradeController@userGetSelfTrade');//用户获取自己发起的广告接口



//获取货币描述
$api->get('getCoinContent', 'InsideTradeController@getCoinContent');

