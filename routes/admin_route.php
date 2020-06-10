<?php
//导向下载页面
Route::get('/', 'Admin\LoginController@download');
//登陆视图
Route::get('/admin/login', 'Admin\LoginController@loginview')->name('login');
//极验证
Route::get('/admin/captcha', 'Admin\LoginController@captcha');
//app新闻中心文章汇总
Route::get('/show/news', 'Admin\ShowArticleController@showNews');
//app帮助中心
Route::get('/show/helps', 'Admin\ShowArticleController@showHelps');
//app展示单条帮助中心文章
Route::get('/show/new/{id}', 'Admin\ShowArticleController@showNew');
//下载页面
Route::get('/download', 'Admin\CoinBabDowloadController@dowload')->name('download');
//下载安装包
Route::get('/admin/getfile', 'Admin\CoinBabDowloadController@getfile')->name('getfile');

//ios文件
Route::get('/coinbab/download/ipa', 'Admin\CoinBabDowloadController@getIpa')->name('getIpa');
Route::get('/coinbab/download/plist', 'Admin\CoinBabDowloadController@getPlist')->name('getPlist');
//登陆提交路由
Route::post('/admin/login', 'Admin\LoginController@login')->name('admin.login');
//退出登录
Route::get('/admin/logout', 'Admin\LoginController@logout')->name('logout');

Route::get('admin/sendCodeSMS','Admin\LoginController@sendCodeSMS');

//文章图片上传
Route::post('upload_image', 'Admin\ArticleController@uploadImage')->name('article.upload_image');

//需登陆
Route::group(['middleware' => ['auth:web','CheckPermission'], 'namespace' => 'Admin'], function() {
    Route::get('/admin/setting/index', 'SettingController@index')->name('user.setting.index');//杂项设置
    Route::post('/admin/setting/update', 'SettingController@update')->name('user.setting.update');//杂项设置
    Route::get('/admin/reward_setting/index', 'SettingController@reward_setting')->name('user.reward_setting.index');//系统奖励设置
    Route::post('/admin/reward_setting/update', 'SettingController@reward_setting_update')->name('user.reward_setting.update');//系统奖励设置

    Route::get('/admin/new_tongji', 'TongJiController@new_tongji')->name('user.new_tongji');//统计表
    Route::get('/admin/new_tongji1', 'TongJiController@new_tongji1')->name('user.new_tongji1');//统计表1
    Route::get('/admin/new_tongji2', 'TongJiController@new_tongji2')->name('user.new_tongji2');//统计表1
    //标记取消长时未入金用户
    Route::get('/admin/removeLongtimeStatus/{user}', 'UsersController@removeLongtimeStatus')->name('user.removeLongtimeStatus');

    //用户提问管理
    Route::get('/admin/userQuestion', 'UsersController@userQuestion')->name('user.userQuestion');
    Route::get('/admin/questionDetail/{id}', 'UsersController@questionDetail')->name('user.questionDetail');
    Route::post('/admin/answer/{id}', 'UsersController@answer')->name('user.answers');

    Route::get('/admin/wallet_flow', 'UserWalletController@flow')->name('user.wallet_flow');
    Route::get('/admin/ore_flow', 'UserWalletController@ore_flow')->name('user.ore_flow');

    /// 问题类型管理
    Route::get('/admin/question_type_index', 'UsersController@question_type_index')->name('question_type.index');
    Route::get('/admin/question_type_detail/{id}', 'UsersController@question_type_detail')->name('question_type.detail');
    Route::post('/admin/question_type_update/{id}', 'UsersController@question_type_update')->name('question_type.update');
    Route::get('/admin/question_type_add', 'UsersController@question_type_add')->name('question_type.add');
    Route::post('/admin/question_type_add_store', 'UsersController@question_type_add_store')->name('question_type.store');
    Route::get('/admin/question_type_delete/{id}', 'UsersController@question_type_delete')->name('question_type.delete');

    //首页
    Route::get('/admin', 'PagesController@root')->name('admin.index');
    //文章分类
    Route::resource('/admin/category', 'CategoryController', ['only']);
    //文章管理
    Route::resource('/admin/article', 'ArticleController');


    //货币管理
    Route::resource('/admin/coinType', 'CoinController', ['except' => ['destroy']]);
    //货币描述上传图片
    Route::post('/admin/coinImg', 'CoinController@uploadImage')->name('coin.upload_image');
    //货币开启场外交易
    Route::get('/admin/coinType/open/{coinType}', 'CoinController@open')->name('coinType.open');

    //一键标记内部用户
    Route::get('/admin/users/changeAllInsideUser', 'UsersController@changeAllInsideUser')->name('users.changeAllInsideUser');
    Route::get('/admin/users/s_user/{user_id}', 'UsersController@s_user')->name('users.s_user');
    //会员管理
    Route::resource('/admin/users', 'UsersController', ['only' => ['index', 'show']]);
    //标记特殊用户
    Route::get('/admin/users/changeuser/{user}', 'UsersController@changeUser')->name('users.changeuser');
    //标记内部用户
    Route::get('/admin/users/changeInsideUser/{user}', 'UsersController@changeInsideUser')->name('users.changeinsideuser');

    //给用户发送消息通知
    Route::post('/admin/users/sendNotification', 'UsersController@sendNotification')->name('users.sendNotification');

    //标记sto特殊用户
    Route::get('/admin/users/changeSTOSpecialUser/{user}', 'UsersController@changeSTOSpecialUser')->name('users.changeSTOSpecialUser');
    //冻结用户
    Route::get('/admin/users/frozenUser/{user}', 'UsersController@FrozenUser')->name('users.frozenUser');
    //标记为商家
    Route::get('/admin/users/changebusiness/{user}', 'UsersController@changeBusiness')->name('users.changebusiness');
    //交易信息
    Route::resource('/admin/message', 'MessageController', ['only' => ['index', 'show']]);
    //订单信息
    Route::resource('/admin/order', 'OrderController', ['only' => ['index', 'show']]);
    //填写聊天记录信息
    Route::get('/admin/order/msg/{id}', 'OrderController@showMsg')->name('showMsg');
    //查询填写的聊天信息
    Route::post('/admin/order/postmsg', 'OrderController@postMsg')->name('postMsg');
    //强制撤单
    Route::get('/admin/order/cancelOrder/{trade_order}/{order_number}', 'OrderController@cancelOrder')->name('cancelOrder');
    //强制发货
    Route::get('/admin/order/seedGoods/{trade_order}/{order_number}', 'OrderController@seedGoods')->name('seedGoods');
    //交易对列表
    Route::resource('/admin/change', 'ChangeController', ['only' => ['store', 'index']]);
    //生成交易对
    Route::get('/admin/change/transaction/{base_coin_id}/{exchange_coin_id}', 'ChangeController@edit')->name('change.edit');
    //显示所有生成的交易对
    Route::get('admin/change', 'ChangeController@index')->name('change.index');
    //开启交易对
    Route::get('admin/change/{base_coin_id}/{exchange_coin_id}/{switch}', 'ChangeController@switch')->name('change.switch');
    //修改交易对数量
    Route::any('admin/change/{base_coin_id}/{exchange_coin_id}', 'ChangeController@changeNumber')->name('change.changeNumber');
    //用户高级认证
    Route::resource('/admin/authentication', 'AuthenticationController', ['except' => ['show']]);
    //轮播图
    Route::resource('/admin/banner', 'BannerController', ['except' => ['show']]);

    //场外交易手续费首页
    Route::get('/admin/outside-rate', 'ChangeRateController@outsideindex')->name('outside-rate.index');
    //修改或创建场外交易手续费汇率
    Route::any('/admin/outside-rate/create', 'ChangeRateController@outsideRate')->name('outside-rate.create');

    //场内交易手续费首页
    Route::get('/admin/inside-rate', 'ChangeRateController@insideindex')->name('inside-rate.index');
    //修改或创建场内交易手续费汇率
    Route::any('/admin/inside-rate/create', 'ChangeRateController@insideRate')->name('inside-rate.create');

    //USDT换成CNY的首页
    Route::get('/admin/usdt-cny', 'UsdtToCnyRateController@index')->name('usdt-cny.index');
    //修改或创建USDT换成CNY的汇率
    Route::any('/admin/usdt-cny/create', 'UsdtToCnyRateController@createAndEditRate')->name('usdt-cny.create');
    //后台所有权限
    Route::resource('/admin/permission', 'PermissionController', ['except' => ['show']]);


    //行为记录
    Route::get('/admin/adminUserBehavior','AdminUserController@adminUserBehavior')->name('adminuser.behavior');
    //后台充值记录
    Route::get('/admin/adminRechargeRecords','AdminUserController@adminRechargeRecords')->name('adminuser.adminRechargeRecords');
    //后台用户管理
    Route::resource('/admin/adminuser', 'AdminUserController', ['except' => ['show']]);
    //区块钱包
    Route::get('/admin/blockwallet', 'UserWalletController@blockWalletIndex')->name('blockwallet.index');
//    内部用户usdt划转记录
    Route::get('/admin/getInsideUserUSDTFlow', 'UserWalletController@getInsideUserUSDTFlow')->name('wallet.getInsideUserUSDTFlow');
    //用户资产统计
    Route::get('/admin/UserMoneyTongJi', 'TongJiController@UserMoneyTongJi')->name('tongji.UserMoneyTongJi');
    //用户钱包
    Route::resource('/admin/userwallet', 'UserWalletController', ['except' => ['create', 'store', 'destroy']]);
    //公告
    Route::resource('/admin/notice', 'NoticeController');
    //代币管理
    Route::resource('/admin/token', 'EthTokenController', ['except' => ['show', 'destroy']]);
    //盘面管理
    Route::get('/admin/face', 'FaceController@index')->name('face.index');
    //中心钱包
    Route::get('/admin/centerWalletFlow', 'CenterWalletController@centerWalletFlow')->name('centerwallet.center');
    Route::get('/admin/centerwallet', 'CenterWalletController@index')->name('centerwallet.index');
    //c2c信息管理
    Route::resource('/admin/c2cmessage', 'C2cMessageController', ['only' => ['index', 'show']]);
    //c2c卖单审核
    Route::get('/admin/c2cCheck', 'C2cMessageController@check')->name('c2cTrade.check');
    //c//c2c用户管理
    Route::get('/admin/userList', 'C2cMessageController@userList')->name('c2cTrade.userList');
    //c2c订单管理
    Route::resource('/admin/c2corder', 'C2cOrderController', ['only' => ['index', 'show']]);
    //c2c商家信息管理
    Route::resource('/admin/business', 'BusinessController', ['only' => ['index', 'show']]);
    //c2c后台审核转账记录放币
    Route::get('checkTransferImg/{order_id}/{check_status}','C2cOrderController@checkTransferImg')->name('checkTransferImg');
    //c2c审核开关也
    Route::get('/admin/c2c_check_switch/index','C2cSettingController@checkSwitch')->name('c2c_check_switch.index');
    //c2c审核开关也更新
    Route::post('/admin/c2c_check_switch/update','C2cSettingController@updateCheckSwitch')->name('c2c_check_switch.update');
    //c2c设置
    Route::resource('/admin/c2csetting', 'C2cSettingController', ['except' => 'destroy']);
    //区块链费率设置
    Route::resource('/admin/coinfees', 'CoinFeesController', ['except' => ['show', 'destroy']]);
    //区块链提币开关
    Route::get('/admin/coinfees/open/{coinfees}', 'CoinFeesController@takeSwitch')->name('takeSwitch');
    //区块链充币开关
    Route::get('/admin/coinfees/put/{coinfees}', 'CoinFeesController@putSwitch')->name('putSwitch');
    //提币到chat开关
    Route::get('/admin/coinfees/toChatSwitch/{coinfees}', 'CoinFeesController@toChatSwitch')->name('toChatSwitch');
    //用户提币
    Route::resource('/admin/coinorder', 'CoinOrderController', ['only' => ['index', 'show']]);
	//后台审核提币
    Route::get('/checkWithdraw/{order_id}/{check_status}','CoinOrderController@checkWithdraw')->name('checkWithdraw');
    //上传安装包
    Route::any('/admin/postfile', 'CoinBabDowloadController@androidDownload')->name('post.package');
    //场内买单
    Route::resource('/admin/insideTradebuy', 'InsideBuyController', ['only' => ['index' ,'show']]);
    //场内卖单
    Route::resource('/admin/insideTradesell', 'InsideSellController', ['only' => ['index' ,'show']]);
    //场内订单
    Route::resource('/admin/insideTradeorder', 'InsideOrderController', ['only' => ['index' ,'show']]);
    //邀请奖励设置页面
    Route::get('/admin/UserInvitation', 'UserInvitationController@index')->name('invitation.index');
    //邀请奖励设置
    Route::any('/admin/UserInvitation/set', 'UserInvitationController@post')->name('invitation.post');

    //邀请好友海报
    Route::resource('/admin/poster', 'PosterController',['except' => ['show']]);

    //理财套餐和类型设定
    Route::resource('/admin/InvestmentType', 'InvestmentTypeController');

    //理财套餐详细规则
    Route::resource('/admin/InvestmentRule', 'InvestmentRuleController');

    //理财流水
    Route::resource('/admin/getFinancing', 'FinancingController');


    //sto设置页面
    Route::get('/admin/sto/index','StoController@index')->name('sto.index');
    //sto推荐奖励记录
    Route::get('/admin/sto/reward','StoController@reward')->name('sto.reward');
    //sto设置页面
    Route::get('/admin/sto/setting','StoController@setting')->name('sto.setting');
    //sto设置gengxin
    Route::post('/admin/sto/setting/update','StoController@update')->name('sto.setting.update');

    //Sto单日设置页面
    Route::get('/admin/stoStageDay/setting/{id}', 'StoStageDayController@setting')->name('stoStageDay.setting');
    //Sto单日设置页面
    Route::post('/admin/stoStageDay/update_setting/{id}', 'StoStageDayController@update_setting')->name('stoStageDay.update_setting');

    //Sto列表
    Route::resource('/admin/stoList', 'StoListController');

    //Sto阶段列表
    Route::resource('/admin/stoStage', 'StoStageController');

    //Sto每天阶段发行量
    Route::resource('/admin/stoStageDay', 'StoStageDayController');

    //Sto会员购买列表
    Route::resource('/admin/userBuyStoRecord', 'StoStageController');


    Route::get('/admin/smsProvider', 'SmsProviderController@index')->name('smsProvider.index');
    Route::post('/admin/smsProvider/update', 'SmsProviderController@update')->name('smsProvider.update');



});


