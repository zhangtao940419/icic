<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Admin\Article;
use App\Model\C2CTradeOrder;
use App\Model\CenterWallet;
use App\Model\CoinTradeOrder;
use App\Model\CoinType;
use App\Model\OutsideTradeOrder;
use App\Model\User;
use App\Model\UserIdentify;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{
    use RedisTool;
    public function root(User $user,Request $request)
    {
        $users = $user->getlatestUser();

        $moneys = CenterWallet::with('CoinType')->get();
        //用户总数
        $userCount = User::count();
        //场外订单
        $outsideOrderCount = OutsideTradeOrder::count();
        //c2c订单
        $c2cOrderCount = C2CTradeOrder::count();
        //文章数
        $articleCount = Article::count();
        //提币订单
        $coinOrderCount = CoinTradeOrder::count();
        //需要认证的会员个数
        $userIdentifyCount = UserIdentify::where('status', 1)->count();
        //usdt换成cny费率
        $usdtRate = $this->redisHget(strtoupper('usdt-cny-rate'), 'rate');
        //场内交易费率
        $insideRate = $this->redisHget('INSIDE-RATE', 'rate');
        //场外交易费率
        $outsideRate = $this->redisHget('OUTSIDE-RATE', 'rate');
        //已完成高级认证人数
        $finishUser = User::where('user_auth_level', 2)->count();
        //完成初级认证人数
        $authUser = User::where('user_auth_level', 1)->count();
        //所有虚拟币种
        $coins = CoinType::all();
//dd((new CoinType())->totalAmount(5));
        $user = $request->user();
        $data = [];
        foreach ($user->permissions as $v) {
            $data[] = $v->route;
        }
        $permission = in_array(\Route::currentRouteName(),$data) ? 1 :0;//dd($permission);
        //dd(20);
        return view('root', compact('coins','users', 'moneys', 'userCount', 'outsideOrderCount', 'c2cOrderCount', 'articleCount', 'coinOrderCount', 'userIdentifyCount', 'usdtRate', 'insideRate', 'outsideRate', 'finishUser', 'authUser','permission'));
    }
}
