<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Model\CoinType;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UsdtToCnyRateController extends Controller
{
    use RedisTool;
    public function index()
    {
        $key = strtoupper('usdt-cny-rate');
        $rate = $this->redisHgetAll($key);

        return view('admin.usdt_to_cny.index', compact('rate'));
    }

    public function createAndEditRate(Request $request)
    {
        $key = strtoupper('usdt-cny-rate');
        $rate = $this->redisHgetAll($key);

        if ($request->isMethod('GET')) {
            return view('admin.usdt_to_cny.create_or_edit', compact('rate'));
        } elseif($request->isMethod('POST')) {
        if($request->rate > 10 || $request->rate < 6) {
             return back()->with('danger', '汇率必须在6-10之间');
        }
        $this->redisHmset($key, ['rate' => $request->rate]);
        $usdtId = CoinType::where('coin_name','USDT')->value('coin_id');
        DB::table('coin_exchange_rate')->where(['virtual_coin_id'=>$usdtId])->update(['rate'=>$request->rate]);

            event(new AdminUserBehavior(auth('web')->user()->id,"修改usdt汇率",'usdt汇率'));

        return redirect()->route('usdt-cny.index')->with('success', '创建成功');
        }
    }

}
