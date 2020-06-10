<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedPermissionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $data = [
            //会员管理
            [
                'id' => 1,
                'name' => '会员管理',
                'route' => '',
                'parents_id' => 0,
            ],
            //财务管理
            [
                'id' => 2,
                'name' => '财务管理',
                'route' => '',
                'parents_id' => 0,
            ],
            //资讯模块
            [
                'id' => 3,
                'name' => '资讯模块',
                'route' => '',
                'parents_id' => 0,
            ],
            //权限管理
            [
                'id' => 4,
                'name' => '权限管理',
                'route' => '',
                'parents_id' => 0,
            ],
            //货币管理
            [
                'id' => 5,
                'name' => '货币管理',
                'route' => '',
                'parents_id' => 0,
            ],
            //场外交易
            [
                'id' => 6,
                'name' => '场外交易',
                'route' => '',
                'parents_id' => 0,
            ],
            //场外交易
            [
                'id' => 7,
                'name' => '场内交易',
                'route' => '',
                'parents_id' => 0,
            ],
            //商家中心
            [
                'id' => 8,
                'name' => '商家中心',
                'route' => '',
                'parents_id' => 0,
            ],
            //系统初始化
            [
                'id' => 9,
                'name' => '系统配置',
                'route' => '',
                'parents_id' => 0,
            ],
            [
                'id' => 42,
                'name' => '其他设置',
                'route' => '',
                'parents_id' => 0,
            ],

            //会员中心
            [
                'id' => 10,
                'name' => '管理会员',
                'route' => 'users.index',
                'parents_id' => 1,
            ],
            [
                'id' => 11,
                'name' => '查看单个会员详情',
                'route' => 'users.show',
                'parents_id' => 10,
            ],
            [
                'id' => 12,
                'name' => '标记特殊会员',
                'route' => 'users.changeuser',
                'parents_id' => 10,
            ],
            [
                'id' => 13,
                'name' => '删除单个会员',
                'route' => 'users.destroy',
                'parents_id' => 10,
            ],
            [
                'id' => 14,
                'name' => '管理文章分类',
                'route' => 'category.index',
                'parents_id' => 3,
            ],

            [
                'id' => 15,
                'name' => '创建文章分类视图',
                'route' => 'category.create',
                'parents_id' => 14,
            ],
            [
                'id' => 16,
                'name' => '提交文章分类',
                'route' => 'category.store',
                'parents_id' => 14,
            ],

            [   'id' => 17,
                'name' => '编辑文章分类视图',
                'route' => 'category.edit',
                'parents_id' => 14,
            ],

            [
                'id' => 18,
                'name' => '提交编辑文章分类',
                'route' => 'category.update',
                'parents_id' => 14,
            ],

            [
                'id' => 19,
                'name' => '删除文章分类',
                'route' => 'category.destroy',
                'parents_id' => 14,
            ],
            //管理后台用户
            [
                'id' => 20,
                'name' => '管理后台用户',
                'route' => 'adminuser.index',
                'parents_id' => 4,
            ],
            [
                'id' => 21,
                'name' => '添加后台用户视图',
                'route' => 'adminuser.create',
                'parents_id' => 20,
            ],
            [
                'id' => 22,
                'name' => '提交后台用户',
                'route' => 'adminuser.store',
                'parents_id' => 20,
            ],
            [
                'id' => 23,
                'name' => '修改后台用户视图',
                'route' => 'adminuser.edit',
                'parents_id' => 20,
            ],
            [
                'id' => 24,
                'name' => '提交修改后台用户',
                'route' => 'adminuser.update',
                'parents_id' => 20,
            ],
            [
                'id' => 25,
                'name' => '删除后台用户',
                'route' => 'adminuser.destroy',
                'parents_id' => 20,
            ],
            //货币管理
            [
                'id' => 26,
                'name' => '货币管理',
                'route' => 'coinType.index',
                'parents_id' => 5,
            ],
            [
                'id' => 27,
                'name' => '创建货币视图',
                'route' => 'coinType.create',
                'parents_id' => 26,
            ],
            [
                'id' => 28,
                'name' => '提交货币',
                'route' => 'coinType.store',
                'parents_id' => 26,
            ],
            [
                'id' => 29,
                'name' => '查看货币交易对',
                'route' => 'coinType.show',
                'parents_id' => 26,
            ],
            //场外订单管理
            [
                'id' => 30,
                'name' => '场外交易信息管理',
                'route' => 'message.index',
                'parents_id' => 6,
            ],
            [
                'id' => 31,
                'name' => '查看单个场外交易',
                'route' => 'message.show',
                'parents_id' => 30,
            ],
            //场外订单管理
            [
                'id' => 32,
                'name' => '场外订单管理',
                'route' => 'order.index',
                'parents_id' => 6,
            ],
            [
                'id' => 33,
                'name' => '查看单个场外订单',
                'route' => 'order.show',
                'parents_id' => 32,
            ],
            //交易对管理
            [
                'id' => 34,
                'name' => '交易对管理',
                'route' => 'change.index',
                'parents_id' => 7,
            ],
            [
                'id' => 65,
                'name' => '开启交易对',
                'route' => 'change.switch',
                'parents_id' => 34,
            ],
            [
                'id' => 35,
                'name' => '生成交易对视图',
                'route' => 'change.edit',
                'parents_id' => 34,
            ],
            [
                'id' => 36,
                'name' => '提交交易对',
                'route' => 'change.store',
                'parents_id' => 34,
            ],
            //用户认证管理
            [
                'id' => 37,
                'name' => '用户认证管理',
                'route' => 'authentication.index',
                'parents_id' => 1,
            ],
            [
                'id' => 38,
                'name' => '显示所有用户认证',
                'route' => 'authentication.index',
                'parents_id' => 37,
            ],
            [
                'id' => 39,
                'name' => '提交高级验证视图',
                'route' => 'authentication.edit',
                'parents_id' => 37,
            ],
            [
                'id' => 40,
                'name' => '同意高级验证',
                'route' => 'authentication.update',
                'parents_id' => 37,
            ],
            [
                'id' => 41,
                'name' => '驳回高级验证',
                'route' => 'authentication.destroy',
                'parents_id' => 37,
            ],
            //轮播图管理管理
            [
                'id' => 43,
                'name' => '轮播图管理',
                'route' => 'banner.index',
                'parents_id' => 42,
            ],
            [
                'id' => 44,
                'name' => '创建轮播图视图',
                'route' => 'banner.create',
                'parents_id' => 43,
            ],
            [
                'id' => 45,
                'name' => '提交轮播图',
                'route' => 'banner.store',
                'parents_id' => 43,
            ],
            [
                'id' => 46,
                'name' => '修改轮播图视图',
                'route' => 'banner.edit',
                'parents_id' => 43,
            ],
            [
                'id' => 47,
                'name' => '提交修改轮播图',
                'route' => 'banner.update',
                'parents_id' => 43,
            ],
            [
                'id' => 48,
                'name' => '删除轮播图',
                'route' => 'banner.destroy',
                'parents_id' => 43,
            ],
            //权限管理
            [
                'id' => 49,
                'name' => '权限管理',
                'route' => 'permission.index',
                'parents_id' => 4,
            ],
            [
                'id' => 50,
                'name' => '创建权限视图',
                'route' => 'permission.create',
                'parents_id' => 49,
            ],
            [
                'id' => 51,
                'name' => '提交权限',
                'route' => 'permission.store',
                'parents_id' => 49,
            ],
            [
                'id' => 52,
                'name' => '编辑权限视图',
                'route' => 'permission.edit',
                'parents_id' => 49,
            ],
            [
                'id' => 53,
                'name' => '编辑权限提交',
                'route' => 'permission.update',
                'parents_id' => 49,
            ],
            [
                'id' => 54,
                'name' => '删除权限',
                'route' => 'permission.destroy',
                'parents_id' => 49,
            ],
            //交易手续费管理
            [
                'id' => 60,
                'name' => '交易手续费',
                'route' => 'outside-rate.index',
                'parents_id' => 9,
            ],
            [
                'id' => 62,
                'name' => '初始化交易费率',
                'route' => 'outside-rate.create',
                'parents_id' => 60,
            ],
            [
                'id' => 63,
                'name' => 'usdt汇率',
                'route' => 'usdt-cny.index',
                'parents_id' => 9,
            ],
            [
                'id' => 64,
                'name' => '初始化usdt汇率',
                'route' => 'usdt-cny.create',
                'parents_id' => 63,
            ],
            //用户钱包管理
            [
                'id' => 66,
                'name' => '用户钱包',
                'route' => 'userwallet.index',
                'parents_id' => 2,
            ],
            [
                'id' => 67,
                'name' => '查看用户钱包',
                'route' => 'userwallet.show',
                'parents_id' => 66,
            ],
            [
                'id' => 68,
                'name' => '编辑用户钱包视图',
                'route' => 'userwallet.edit',
                'parents_id' => 66,
            ],
            [
                'id' => 69,
                'name' => '提交用户钱包修改',
                'route' => 'userwallet.update',
                'parents_id' => 66,
            ],
            [
                'id' => 70,
                'name' => 'App公告',
                'route' => 'notice.index',
                'parents_id' => 3,
            ],
            [
                'id' => 71,
                'name' => '创建App公告视图',
                'route' => 'notice.create',
                'parents_id' => 70,
            ],
            [
                'id' => 72,
                'name' => '提交创建App公告',
                'route' => 'notice.store',
                'parents_id' => 70,
            ],
            [
                'id' => 73,
                'name' => '编辑App公告视图',
                'route' => 'notice.edit',
                'parents_id' => 70,
            ],
            [
                'id' => 74,
                'name' => '提交编辑App公告',
                'route' => 'notice.update',
                'parents_id' => 70,
            ],
            [
                'id' => 75,
                'name' => '删除App公告',
                'route' => 'notice.destroy',
                'parents_id' => 70,
            ],
            [
                'id' => 76,
                'name' => 'ETH代币管理',
                'route' => 'token.index',
                'parents_id' => 5,
            ],
            [
                'id' => 77,
                'name' => '添加ETH代币视图',
                'route' => 'token.create',
                'parents_id' => 76,
            ],
            [
                'id' => 78,
                'name' => '提交添加ETH代币',
                'route' => 'token.store',
                'parents_id' => 76,
            ],
            [
                'id' => 79,
                'name' => '编辑ETH代币视图',
                'route' => 'token.edit',
                'parents_id' => 76,
            ],
            [
                'id' => 80,
                'name' => '提交编辑ETH代币',
                'route' => 'token.update',
                'parents_id' => 76,
            ],
            [
                'id' => 81,
                'name' => '文章管理',
                'route' => 'article.index',
                'parents_id' => 3,
            ],
            [
                'id' => 82,
                'name' => '创建文章视图',
                'route' => 'article.create',
                'parents_id' => 81,
            ],
            [
                'id' => 83,
                'name' => '提交创建文章',
                'route' => 'article.store',
                'parents_id' => 81,
            ],
            [
                'id' => 84,
                'name' => '编辑文章视图',
                'route' => 'article.edit',
                'parents_id' => 81,
            ],
            [
                'id' => 85,
                'name' => '提交编辑文章',
                'route' => 'article.update',
                'parents_id' => 81,
            ],
            [
                'id' => 86,
                'name' => '查看单个文章',
                'route' => 'article.show',
                'parents_id' => 81,
            ],
            [
                'id' => 87,
                'name' => '删除文章',
                'route' => 'article.destroy',
                'parents_id' => 81,
            ],
            [
                'id' => 88,
                'name' => '中心钱包',
                'route' => 'centerwallet.index',
                'parents_id' => 2,
            ],
            [
                'id' => 89,
                'name' => '盘口数据',
                'route' => 'face.index',
                'parents_id' => 7,
            ],
            [
                'id' => 90,
                'name' => 'c2c交易信息',
                'route' => 'c2cmessage.index',
                'parents_id' => 8,
            ],
            [
                'id' => 91,
                'name' => 'c2c交易详细信息',
                'route' => 'c2cmessage.show',
                'parents_id' => 90,
            ],
            [
                'id' => 92,
                'name' => 'c2c订单信息',
                'route' => 'c2corder.index',
                'parents_id' => 8,
            ],
            [
                'id' => 93,
                'name' => 'c2c订单详细信息',
                'route' => 'c2corder.show',
                'parents_id' => 92,
            ],
            [
                'id' => 94,
                'name' => 'c2c订单审核发币',
                'route' => 'checkTransferImg',
                'parents_id' => 92,
            ],
            [
                'id' => 95,
                'name' => '区块链交易费率',
                'route' => 'coinfees.index',
                'parents_id' => 5,
            ],
            [
                'id' => 96,
                'name' => '创建区块链交易费率视图',
                'route' => 'coinfees.create',
                'parents_id' => 95,
            ],
            [
                'id' => 97,
                'name' => '提交创建区块链交易费率',
                'route' => 'coinfees.store',
                'parents_id' => 95,
            ],
            [
                'id' => 98,
                'name' => '编辑区块链交易费率视图',
                'route' => 'coinfees.edit',
                'parents_id' => 95,
            ],
            [
                'id' => 99,
                'name' => '提交编辑区块链交易费率',
                'route' => 'coinfees.update',
                'parents_id' => 95,
            ],
            [
                'id' => 100,
                'name' => '强制撤销场外订单',
                'route' => 'cancelOrder',
                'parents_id' => 32,
            ],
            [
                'id' => 101,
                'name' => '强制发货场外订单',
                'route' => 'seedGoods',
                'parents_id' => 32,
            ],
            [
                'id' => 103,
                'name' => '区块链充币开关',
                'route' => 'putSwitch',
                'parents_id' => 95,
            ],
            [
                'id' => 104,
                'name' => '区块链提币开关',
                'route' => 'takeSwitch',
                'parents_id' => 95,
            ],
            [
                'id' => 105,
                'name' => '上传安装包',
                'route' => 'post.package',
                'parents_id' => 42,
            ],
            [
                'id' => 106,
                'name' => '提币订单',
                'route' => 'coinorder.index',
                'parents_id' => 2,
            ],
            [
                'id' => 107,
                'name' => '审核提币',
                'route' => 'checkWithdraw',
                'parents_id' => 106,
            ],
            [
                'id' => 108,
                'name' => '后台首页',
                'route' => 'admin.index',
                'parents_id' => 49,
            ],
            [
                'id' => 109,
                'name' => '标记商家',
                'route' => 'users.changebusiness',
                'parents_id' => 10,
            ],
            [
                'id' => 110,
                'name' => '初始化场内交易费率',
                'route' => 'inside-rate.create',
                'parents_id' => 60,
            ],
            [
                'id' => 111,
                'name' => '修改交易对数量',
                'route' => 'change.changeNumber',
                'parents_id' => 34,
            ],
            [
                'id' => 112,
                'name' => '查看提币详细信息',
                'route' => 'coinorder.show',
                'parents_id' => 106,
            ],
            [
                'id' => 113,
                'name' => 'c2c审核转账记录放币',
                'route' => 'checkTransferImg',
                'parents_id' => 92,
            ],
            [
                'id' => 114,
                'name' => '虚拟币场内外开启开关',
                'route' => 'coinType.open',
                'parents_id' => 26,
            ],
            [
                'id' => 115,
                'name' => '场内买单信息',
                'route' => 'insideTradebuy.index',
                'parents_id' => 7,
            ],
            [
                'id' => 116,
                'name' => '场内买单详细信息',
                'route' => 'insideTradebuy.show',
                'parents_id' => 115,
            ],
            [
                'id' => 117,
                'name' => '用户详细信息',
                'route' => 'users.show',
                'parents_id' => 20,
            ],
            [
                'id' => 118,
                'name' => '场内卖单信息',
                'route' => 'insideTradesell.index',
                'parents_id' => 7,
            ],
            [
                'id' => 119,
                'name' => '场内卖单详细信息',
                'route' => 'insideTradesell.show',
                'parents_id' => 118,
            ],
            [
                'id' => 120,
                'name' => '冻结用户',
                'route' => 'users.frozenUser',
                'parents_id' => 10,
            ],
            [
                'id' => 121,
                'name' => '邀请奖励设置',
                'route' => 'invitation.index',
                'parents_id' => 9,
            ],
            [
                'id' => 122,
                'name' => '修改或创建邀请奖励设置',
                'route' => 'invitation.post',
                'parents_id' => 121,
            ],
            [
                'id' => 123,
                'name' => '理财模块',
                'route' => '',
                'parents_id' => 0,
            ],
            [
                'id' => 124,
                'name' => '理财类型设置',
                'route' => 'InvestmentType.index',
                'parents_id' => 123,
            ],
            [
                'id' => 125,
                'name' => '理财套餐设置',
                'route' => 'InvestmentRule.index',
                'parents_id' => 123,
            ],
            [
                'id' => 126,
                'name' => '会员购买流水',
                'route' => 'getFinancing.index',
                'parents_id' => 123,
            ],
            [
                'id' => 127,
                'name' => '理财套餐创建页面',
                'route' => 'InvestmentRule.create',
                'parents_id' => 123,
            ],
            [
                'id' => 128,
                'name' => '理财套餐编辑页面',
                'route' => 'InvestmentRule.edit',
                'parents_id' => 123,
            ],

        ];
        \DB::table('permissions')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        App\Model\Admin\Permission::truncate();
    }
}
