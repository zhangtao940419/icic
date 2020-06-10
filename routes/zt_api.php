<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 13:20
 */

$api->get('sendCodeSMS','CommonController@sendCodeSMS');//发送验证码

$api->any('checkRegisterCode','RegisterController@checkRegisterCode');//检测注册短信验证码是否正确

$api->post('register','RegisterController@register')->middleware('CheckProtectMode');//保存用户账号密码信息

$api->post('login', 'LoginController@login');//用户登录

$api->post('verifyPasswordCode','LoginController@verifyPasswordCode');//找回密码检测验证码

$api->post('retrievePassword', 'LoginController@retrievePassword')->middleware('CheckProtectMode');//用户找回密码

$api->get('sendEmailT','CommonController@sendEmailT');

$api->get('getPrivacyPolicy','CommonController@getPrivacyPolicy');//隐私政策

//*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//outside api
$api->post('saveOutsideTrade','OutsideTradeController@saveOutsideTrade')->middleware(['checkOutsideWallets']);//场外挂单
$api->post('cancelOutsideTrade','OutsideTradeController@cancelTrade');//撤销广告
$api->get('getAllArea','OutsideTradeController@getAllArea');//获取所有国家
$api->get('getAllCoin','OutsideTradeController@getAllCoin');//获取所有coin
$api->get('getAllCurrency','OutsideTradeController@getAllCurrency');//获取所有coin
$api->get('getAllOutsideTrade','OutsideTradeController@getAllTrade');//获取所有coin
$api->get('getOutsidePersonalMsg','OutsideTradeController@getPersonalMsg');//获取某个人的信息
$api->get('getOneOutsideTrade','OutsideTradeController@getOneTrade');//获取某个广告详情
$api->get('getUserOutsideTrade','OutsideTradeController@getUserTrade');//获取TA的广告
$api->get('tradeManage','OutsideTradeController@tradeManage');//广告管理
$api->get('getCoinPrice','OutsideTradeController@getCoinPrice');//jiage


$api->post('acceptOutsideTtrade','OutsideTransactionController@acceptTtrade')->middleware(['checkOutsideWallets']);//场外接单
$api->post('buyCancelOutsideOrder','OutsideTransactionController@buyCancelOrder');//场外撤销交易
$api->post('ConfirmOutsideBuyOrder','OutsideTransactionController@confirmBuyOrder');//场外买家确认付款
$api->post('sellerConfirmOutsideOrder','OutsideTransactionController@sellerConfirmOrder');//场外卖确认收款,交易完成
$api->post('trust','OutsideTransactionController@trust');//信任与取消
$api->post('defriend','OutsideTransactionController@defriend');//屏蔽与取消
$api->get('checkTrustStatus','OutsideTransactionController@checkTrustStatus');//屏蔽与取消
$api->get('getAboutUserList','OutsideTransactionController@getAboutUserMsg');//信任管理
$api->get('getOrderWithOther','OutsideTransactionController@getOrderWithOther');//我与他的交易
$api->get('getMyOutsideOrder','OutsideTransactionController@getMyOrder');//我的交易
$api->get('getOrderDetail','OutsideTransactionController@getOrderDetail');//我的交易xiangq
$api->post('outsideOrderComment','OutsideTransactionController@comment');//pingjia
$api->get('getTradeOrderList','OutsideTransactionController@getTradeOrderList');//广告成交详情列表


////////场外钱包api
$api->get('getOutsideBalance','UserOutsideWalletController@getBalance');//获取余额
$api->get('outsideWalletIndex','UserOutsideWalletController@index')->middleware(['checkOutsideWallets','UpdateOutsideWallets']);//钱包首页
$api->get('getOutsideWalletDetail','UserOutsideWalletController@getWalletDetail');//钱包详情
$api->get('getOutsideRechargeMsg','UserOutsideWalletController@getRechargeMsg')->middleware(['checkOutsideWalletAddress']);//充值信息
$api->get('getOutsideWithdrawMsg','UserOutsideWalletController@getWithdrawMsg');//提币信息
$api->post('ousideWithdraw','UserOutsideWalletController@withdraw');//提币信息

////极光im
$api->get('getJGUserAccount','MessagePushController@getUserAccount');

//$api->get('mockLogin','PersonalCenterController@mockLogin');//模拟登陆获取token

$api->group(['middleware'=>['auth.api']], function ($api) {

    //用户消息通知
    $api->get('myNotifiablesCount','PersonalCenterController@myNotifiablesCount');
    $api->get('myNotifiables','PersonalCenterController@myNotifiables');
    $api->get('readNotifiable','PersonalCenterController@readNotifiable');
    $api->get('getWarningNotifiable','PersonalCenterController@getWarningNotifiable');

    //邀请好友
    $api->get('getInvitaInfo','PersonalCenterController@getInvitaInfo');//获取邀请好友概况信息
    $api->get('getInvitaRules','PersonalCenterController@getInvitaRules');//返佣规则
    $api->get('getInvitaUsers','PersonalCenterController@getInvitaUsers');//邀请记录
    $api->get('getInvitaCommission','PersonalCenterController@getInvitaCommission');//返佣记录
    $api->get('getInvitaRanking','PersonalCenterController@getInvitaRanking');//获取邀请好友返佣排行榜
    $api->get('getInvitaPoster','PersonalCenterController@getInvitaPoster');//获取邀请好友返佣排行榜

    //personal center api
    $api->any('logout', 'LoginController@logout');//用户退出

    $api->get('getUserSettingMsg','PersonalCenterController@getUserSettingMsg');//获取用户设置页面的相关信息

    $api->post('primaryAuth','PersonalCenterController@primaryAuth')->middleware('CheckProtectMode');//初级认证-

    $api->get('getAuthMsg','PersonalCenterController@getAuthMsg');//获取认证信息

    $api->post('topAuth','PersonalCenterController@topAuth')->middleware('checkPrimaryAuth')->middleware('CheckProtectMode');//提交高级认证

    $api->post('setHeadImg','PersonalCenterController@setHeadImg')->middleware('CheckProtectMode');//update head img

    $api->post('setPayPassword','PersonalCenterController@setPayPassword')->middleware('CheckProtectMode');//set pay password

    $api->get('sendEmail','PersonalCenterController@sendEmail');//send email code message

    $api->post('setEmail','PersonalCenterController@setEmail')->middleware('CheckProtectMode');//set email

    $api->get('getBankList','PersonalCenterController@getBankList');//get the bank list

    $api->post('bindBankCard','PersonalCenterController@bindBankCard')->middleware('checkPrimaryAuth')->middleware('CheckProtectMode');//bind bank card

    $api->post('updateBankCard','PersonalCenterController@updateBankCard')->middleware('CheckProtectMode');//update user bank card

    $api->get('getUserBankCards','PersonalCenterController@getUserBankCards');//user bank cards

    $api->post('updateLoginPassword','PersonalCenterController@updateLoginPassword')->middleware('CheckProtectMode');//update login password

    $api->post('updatePayPassword','PersonalCenterController@updatePayPassword')->middleware('CheckProtectMode');//update login password

    $api->post('retrievePayPassword','PersonalCenterController@retrievePayPassword')->middleware('CheckProtectMode');//update login password


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//c2c api
    $api->any('saveC2CTrade','C2CTradeController@saveUserTrade')->middleware('checkPrimaryAuth','checkTopAuth','checkBankCard','checkPayPassword','checkC2CTrade')->middleware('CheckProtectMode');//c2c挂单
    $api->any('confirmTrade','C2CTradeController@confirmTrade')->middleware('checkPrimaryAuth','checkTopAuth','checkBankCard')->middleware('CheckProtectMode');//确认订单信息
    $api->get('C2CUOrderList','C2CTradeController@orderList')->middleware('checkPrimaryAuth','checkTopAuth','checkBankCard');//交易记录
    $api->get('C2CUOrderDetail','C2CTradeController@orderDetail')->middleware('checkPrimaryAuth','checkTopAuth','checkBankCard');//交易记录

    $api->get('C2COrderList','BusinessCenterController@orderList')->middleware('checkBusiness');//商家订单
    $api->get('C2COrderDetail','BusinessCenterController@orderDetail')->middleware('checkBusiness');//商家订单详情

    $api->any('C2CTradeList','BusinessCenterController@getTradeList')->middleware('checkBusiness');//商家中心挂单列表
    $api->get('receiptC2CTrade','BusinessCenterController@receiptTrade')->middleware('checkBusiness','checkTopAuth','checkBankCard','checkC2CBusinessRecept')->middleware('CheckProtectMode');//商家中心接单
    $api->get('confirmBuyOrder','BusinessCenterController@confirmBuyOrder')->middleware('checkBusiness')->middleware('CheckProtectMode');//确认用户买单
    $api->any('confirmSellOrder','BusinessCenterController@confirmSellOrder')->middleware('checkBusiness')->middleware('CheckProtectMode');//确认用户卖单

    $api->post('uploadTransferImg','BusinessCenterController@uploadTransferImg')->middleware('checkBusiness')->middleware('CheckProtectMode');//img


    $api->any('verifyPayPassword','UserAuthController@verifyUserPayPassword');//验证支付密码接口
    $api->any('checkUserAuth','UserAuthController@checkUserAuth');//检查是否需要输入密码



    $api->get('checkBusinessAuth','CommonController@checkBusinessAuth');//查询是否是商家


//*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //wallet api

    $api->get('wallet','UserWalletController@index')->middleware('updateUserWallets','checkWallet');//钱包首页数据
//    $api->get('wallet','UserWalletController@index')->middleware('checkWallet');//钱包首页数据

    $api->get('getWalletDetail','UserWalletController@getWalletDetail')->middleware('updateUserWallet');//获取指定钱包的余额等详情
//    $api->get('getWalletDetail','UserWalletController@getWalletDetail');//获取指定钱包的余额等详情


    $api->get('getRechargeMsg','UserWalletController@getRechargeMsg')->middleware('checkCoinWallet','CheckProtectMode');//充值

    $api->get('getWithdrawMsg','UserWalletController@getWithdrawMsg')->middleware('checkCoinWallet');//提币信息

    $api->any('withdrawCoin','UserWalletController@withdrawCoin')->middleware('CheckProtectMode');//用户提币接口

    $api->post('transferAccounts','UserWalletController@transferAccounts')->middleware('CheckProtectMode');//资产划转
    //获取划转锁定的订单列表
    $api->get('getTransferLockOrder','UserWalletController@getTransferLockOrder');

    $api->get('getCoinFlow','UserWalletController@getCoinFlow');//资产flow

    //获取矿池资产
    $api->get('getOrePoolWallet','UserWalletController@getOrePoolWallet');
    //获取矿池资产详情
    $api->get('getOrePoolWalletDetail','UserWalletController@getOrePoolWalletDetail');
    //获取矿池划转注意事项
    $api->get('getOrePoolTransferMsg','UserWalletController@getOrePoolTransferMsg');
    //矿池icic划转(场内->矿池或者可提->矿池)
    $api->post('orePoolTransfer','UserWalletController@orePoolTransfer');

    //获取资金划转说明
    $api->get('getTransferMsg','UserWalletController@getTransferMsg');
    //获取特定货币的余额
    $api->get('getUserCoinBalance','UserWalletController@getUserCoinBalance');

    //查询是否是内部用户
    $api->get('checkIsInsideUser','UserAuthController@checkIsInsideUser');
    ////内部用户划转usdt
    $api->post('transferUSDT','UserWalletController@transferUSDT')->middleware('InsideUser','checkPayPassword','CheckProtectMode');




    //////////杂项
    $api->get('getInvitationQRCode','CommonController@getInvitationQRCode');






    //sto
//获取我的推荐人
    $api->get('getMyPUser','StoController@getMyPUser');
//绑定推荐人
    $api->post('bindMyPUser','StoController@bindMyPUser')->middleware('CheckProtectMode');
    //获取所有sto项目
    $api->get('getAllSTOProject','StoController@getAllSTOProject');
    //项目详情
    $api->get('getStoDetail','StoController@getStoDetail')->middleware('checkPrimaryAuth','checkTopAuth','checkBankCard');
    //获取正在发行中的阶段详情
    $api->get('getStoStageDetail','StoController@getStoStageDetail')->middleware('checkPrimaryAuth','checkTopAuth','checkBankCard');
    //获取icic可用余额
    $api->get('getICICUsableBalance','StoController@getICICUsableBalance');
    //购买
    $api->post('buy','StoController@buy')->middleware('checkPrimaryAuth','checkTopAuth','checkBankCard','CheckProtectMode');

    //获取购买页面的交易记录
    $api->get('getBuyRecord','StoController@getBuyRecord');
    //sto资产
    $api->get('getStoWallets','StoController@getStoWallets');
    //资产详情
    $api->get('getStoWalletDetail','StoController@getWalletDetail');

    //获取提取阶段
    $api->get('getCoinFreeStage','StoController@getCoinFreeStage');
    //sto提取
    $api->post('free','StoController@free');

//获取联系客服的说明文字
    $api->get('getUserQuestionMsg','UserQuestionController@getUserQuestionMsg');
    //获取所有问题类型
    $api->get('getAllQuestionType','UserQuestionController@getAllQuestionType');
    //用户上传图片
    $api->post('uploadImage','UserQuestionController@uploadImage');
    //提交问题
    $api->post('submitQuestion','UserQuestionController@submitQuestion');
    //反馈列表
    $api->get('getQuestionList','UserQuestionController@getQuestionList');
    //获取用户弹框
    $api->get('getUserNewsRemind','UserQuestionController@getUserNewsRemind');

    ////合约api
    //获取合约首页信息
    $api->get('getContractMsg','ContractController@getContractMsg');
    $api->get('getContractMarket','ContractController@getContractMarket');
    $api->post('contractBuy','ContractController@buy');
    $api->get('getBuyRecords','ContractController@getBuyRecords');

});

//STO
//获取会员购买订单
$api->get('getUserBuyOrders','StoController@getUserBuyOrders');

$api->get('test','TestController@index');
$api->get('test1','TestController@test1');
$api->post('postt','TestController@postt');
$api->get('sto_test','TestController@sto');

$api->get('getC2CMsg','C2CTradeController@getC2CMsg');//获取c2c信息

////////////////////////////////huobi api
$api->get('hb','TestController@huoBi');
$api->get('allMerged','HuoBiController@getAllSymbolMerged');//火币行情
$api->get('oneMerged','HuoBiController@getOneSymbolMerged');//dange行情

$api->get('symbolDepth','HuoBiController@getSymbolDepth');//depth


$api->get('kLine','HuoBiController@getKLine');//火币行情

$api->get('historyTrade','HuoBiController@getHistoryTrade');//历史成交

$api->get('getCoinDes','HuoBiController@getCoinDes');//币种简介

$api->get('getHLAllSymbol','HuoBiController@getHLAllSymbol');//互链交易对


////////////////////////////////

$api->any('getUSDTBalance','UserWalletController@getUSDTBalance');//获取usdt余额信息


//杂项
//理财说明
$api->get('getFinancingExplain','CommonController@getFinancingExplain');
//关于我们
$api->get('getAboutUs','CommonController@getAboutUs');

//公告
$api->get('getNewNotice','CommonController@getNewNotice');


//获取最新安卓版本
$api->get('getAndroid', 'CommonController@getAndroid');

//获取最新Ios版本
$api->get('getIos', 'CommonController@getIos');

$api->get('getUserIdentifyArea', 'CommonController@getUserIdentifyArea');

