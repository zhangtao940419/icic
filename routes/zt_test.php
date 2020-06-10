<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 13:20
 */


//Route::get('asss', 'Web\Api\V1\TestController@index');
$api->get('getC2CMsg','C2CTradeController@getC2CMsg')->middleware('auth.api');
$api->any('getUSDTBalance','WalletController@getUSDTBalance')->middleware('auth.api');
$api->group(['middleware'=>['auth.api']], function ($api) {


    $api->get('sendEmail','UserSettingController@sendEmail');//发送邮箱验证码
    $api->post('saveEmail','UserSettingController@bindEmail');//保存邮箱
    $api->post('setPayPassword','UserSettingController@setPayPassword');//保存邮箱
    //$api->post('primaryAuth','UserSettingController@primaryAuth');//chuji认证
    $api->get('checkUserAuthLevel','UserSettingController@checkUserAuthLevel');//查询用户的认证等级
    $api->post('setHeadImage','UserSettingController@setHeadImage');//上传头像
    $api->any('cardIsAvailable','UserSettingController@cardIsAvailable');//检查身份证是否可用
    $api->post('uploadIdentifyCard','UserSettingController@uploadIdentifyCard');//上传身份证照片
    $api->post('topAuth','UserSettingController@topAuth');//提交高级认证
    $api->post('updateLoginPassword','UserSettingController@updateLoginPassword');//修改登录密码
    $api->post('updatePayPassword','UserSettingController@updatePayPassword');//修改资金密码
    $api->get('getUserSettingMsg','UserSettingController@getUserSettingMsg');//获取用户设置页面的相关信息

    $api->post('updatePayPassword1','UserSettingController@updatePayPassword1');//手机验证码修改资金密码

    $api->any('primaryAuth','UserSettingController@bindBankCard');//初级认证--绑定银行卡验证
    $api->any('changeBankCard','UserSettingController@changeBankCard');//更换银行卡


    $api->get('logout','LoginController@logout');//用户退出登录

    $api->post('checkLoginPassword','CommonController@checkLoginPassword');//检查登录密码是否正确
    $api->post('checkPayPassword','CommonController@checkPayPassword');//检查资金密码是否正确
    $api->get('getExtraUserMsg','CommonController@getExtraUserMsg');//获取额外的用户信息  交易数  好评率  信任数
    $api->get('getBankList','CommonController@getBankList');//获取银行列表
    $api->get('getUserAuthMsg','CommonController@getUserAuthMsg');//用户姓名手机信息
    $api->get('getBankCardList','CommonController@getBankCardList');//用户银行卡列表

    $api->get('checkBusinessAuth','CommonController@checkBusinessAuth');//查询是否是商家

    $api->get('addTrust','OutsideTransactionController@addTrust');//添加信任
    $api->get('removeTrust','OutsideTransactionController@removeTrust');//取消信任
    $api->get('addShield','OutsideTransactionController@defriend');//屏蔽用户
    $api->get('removeShield','OutsideTransactionController@removeDefriend');//屏蔽用户
    $api->get('checkTrustStatus','OutsideTransactionController@checkTrustStatus');//查询某个用户的信任和
    $api->get('getTrustMgtMsg','OutsideTransactionController@getTrustMgtMsg');//信任管理--信任我的人/我信任的人/我屏蔽的人

    $api->any('wallet','WalletController@index')->middleware('checkWallet','updateUserWallets');//钱包首页数据
    $api->get('getWalletDetail','WalletController@getWalletDetail')->middleware('updateUserWallet');//获取指定钱包的余额等详情
    $api->any('withdrawCoin','WalletController@withdrawCoin');//用户提币接口
    $api->get('getWithdrawOrders','WalletController@getWithdrawOrders')->middleware('updateWithdrawStatus');//提币订单记录
    $api->get('getWithdrawMsg','WalletController@getWithdrawMsg')->middleware('checkCoinWallet');//提币信息
    $api->get('getRechargeMsg','WalletController@getRechargeMsg')->middleware('checkCoinWallet');//充值


    $api->any('saveC2CTrade','C2CTradeController@saveUserTrade')->middleware('checkTopAuth','checkBankCard','checkC2CTrade');//c2c挂单
    $api->any('confirmTrade','C2CTradeController@confirmTrade')->middleware('checkTopAuth','checkBankCard');//确认订单信息
    $api->get('C2CUOrderList','C2CTradeController@orderList')->middleware('checkTopAuth','checkBankCard');//交易记录
    $api->get('C2CUOrderDetail','C2CTradeController@orderDetail')->middleware('checkTopAuth','checkBankCard');//交易记录

    $api->get('C2COrderList','BusinessCenterController@orderList')->middleware('checkPrimaryAuth','checkBusiness');//商家订单
    $api->get('C2COrderDetail','BusinessCenterController@orderDetail')->middleware('checkPrimaryAuth','checkBusiness');//商家订单详情

    $api->any('C2CTradeList','BusinessCenterController@getTradeList')->middleware('checkBusiness');//商家中心挂单列表
    $api->get('receiptC2CTrade','BusinessCenterController@receiptTrade')->middleware('checkPrimaryAuth','checkBusiness','checkBankCard','checkC2CBusinessRecept');//商家中心接单
    $api->get('confirmBuyOrder','BusinessCenterController@confirmBuyOrder')->middleware('checkPrimaryAuth','checkBusiness');//确认用户买单
    $api->any('confirmSellOrder','BusinessCenterController@confirmSellOrder')->middleware('checkPrimaryAuth','checkBusiness');//确认用户卖单

    $api->post('uploadTransferImg','BusinessCenterController@uploadTransferImg')->middleware('checkBusiness');//img


    $api->any('verifyPayPassword','UserAuthController@verifyUserPayPassword');//验证支付密码接口
    $api->any('checkUserAuth','UserAuthController@checkUserAuth');//检查是否需要输入密码

    //最近委托
    $api->get('getTrade', 'InsideTradeController@getTrade');

    $api->any('transferAccounts','WalletController@transferAccounts');



});

//撤销c2c卖单脚本
$api->get('cancelSellOrder','TestController@cancelSellOrder');

//查询所有虚拟货币
$api->get('getVirtualCoin', 'CommonController@getVirtualCoin');

//获取最新公告
$api->get('getNewNotice', 'CommonController@getNotice');

//获取最新Ios版本
$api->get('getIos', 'CommonController@getIos');

//获取最新安卓版本
$api->get('getAndroid', 'CommonController@getAndroid');

$api->group(['middleware' => 'CheckVersion'], function ($api) {
    $api->get('checkSuccess', 'AdminWalletController@checkSuccess');

    $api->get('getUSDTOrders', 'WalletController@getUSDTOrders');//获取usdt交易记录

//$api->get('sendBABC','TestController@sendBABC');//
    $api->any('sendBABCB', 'TestController@sendBABCB');//发送babc脚本慎用

    $api->get('getWithdrawFee', 'WalletController@getWithdrawFee');

    $api->get('asss', 'TestController@index');

    $api->get('checkTransferImg', 'BusinessCenterController@checkTransferImg')->name('checkTransfer');//c2c后台审核转账记录放币


    $api->any('checkWithdrawB', 'AdminWalletController@checkWithdraw');//后台审核提币接口备用

    $api->get('getInfo', 'TestController@getInfo');//用于测试的路由
    $api->get('sendE', 'TestController@sendEmail');

    $api->get('getCurrencyExchange', 'CommonController@queryCurrencyExchange');//汇率查询接口


//查询所有国家
    $api->get('getCountryInfo', 'CommonController@getCountryInfo');

//查询所有国家币种
    $api->get('getAllCountryCoin', 'CommonController@getAllCountryCoin');


//获取单个交易对价格
    $api->get('getOneTradeTeamList', 'InsideTradeController@getOneTradeTeamList');

//获取虚拟货币兑换成人名币的价格
    $api->get('changeTo_else_Coin', 'CommonController@changeTo_else_Coin');

//获取货币交易的剩余数量
    $api->get('getBalance', 'InsideTradeController@getBalance');

//获取货币余额
    $api->get('getUserCoinBalance', 'InsideTradeController@getUserCoinBalance');

//获取虚拟货币交易各个时间段
    $api->get('getChanges/{base_coin_name}-{exchange_coin_name}/{time}', 'BbchangeController@getChanges');

//用户卖货币时获取用户可卖的数量(用户想卖余额+手续费)
    $api->get('getUserBalance', 'OutsideTransactionController@getUserBalance');

//获取场外订单状态
    $api->get('getOrderStatus', 'OutsideTransactionController@getOrderStatus');

//获取最大值和最小值区间,并转化为人民币
    $api->get('getPriceRange', 'OutsideTransactionController@getPriceRange');

//差评数量加一
    $api->get('addPraise', 'CommonController@addPraise');

    //主分支

});