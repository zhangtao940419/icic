<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Http\Requests\TransactionRequest;
use App\Model\CoinType;
use App\Http\Controllers\Controller;
use App\Model\InsideSetting;
use App\Traits\RedisTool;
use App\Handlers\Helpers;
use App\Server\InsideTrade\InsideTradeServer;
use Illuminate\Http\Request;


class ChangeController extends Controller
{
    use RedisTool;

    //显示所有交易对
    public function index(InsideTradeServer $insideTradeServer)
    {
        $changes = $insideTradeServer->getAllCoin();

        return view('admin.change.index', compact('changes'));
    }


    public function store(TransactionRequest $request, CoinType $coinType)
    {
        //拼接redis的key
        $HS_key = strtoupper('INSIDE_TEAM_' . $request->base_coin_id . '_' . $request->exchange_coin_id);

        if ($this->redisExists($HS_key)) {
            return back()->with('danger', '该交易对已存在');
        }

        $base_coin_name = $coinType->getCoinName(['coin_id' => $request->base_coin_id]);
        $exchange_coin_name = $coinType->getCoinName(['coin_id' => $request->exchange_coin_id]);

        //准备进入redis的数据
        $info = $request->except('_token');
        $info['base_coin_name'] = $base_coin_name;
        $info['exchange_coin_name'] = $exchange_coin_name;
        $info['CNY_price'] = $this->changeTo_Other_Coin($request->base_coin_id, 'CNY', $request->current_price);
        $info['switch'] = 0;
        $info['begin_price'] = 1;

        $DL_key = strtoupper('TRADE_TEAM_' . $base_coin_name);

        //哈希存储交易对详细信息
        if (!$this->redisExists($HS_key)) {
            $this->redisHmset($HS_key, $info);
        }

        $data = ['coin_id' => $request->exchange_coin_id, 'coin_name' => $exchange_coin_name];

        //存储货币兑换
        if (!in_array(serialize($data), $this->getList($DL_key))) {
            $this->setList($DL_key, serialize($data));
        }
        event(new AdminUserBehavior(auth('web')->user()->id,"创建交易对:{$HS_key}",'交易对'));

        return redirect()->route('coinType.index')->with('success', '创建成功');//暂时跳转到首页
    }



    public function edit($coin_id, $change_coin_id, CoinType $coinType, Helpers $helpers)
    {
        $coin_name = strtolower($coinType->getCoinName(['coin_id' => $coin_id]));

        $change_coin = strtolower($coinType->getCoinName(['coin_id' => $change_coin_id]));

        //拼接表名
        $tableName1 = "time_sharing_1" . '_' . $coin_name . '_' . $change_coin;
        $tableName5 = "time_sharing_5" . '_' . $coin_name . '_' . $change_coin;
        $tableName15 = "time_sharing_15" . '_' . $coin_name . '_' . $change_coin;
        $tableName30 = "time_sharing_30" . '_' . $coin_name . '_' . $change_coin;
        $tableName60 = "time_sharing_60" . '_' . $coin_name . '_' . $change_coin;

        //动态创建表
        $helpers->createTable([$tableName1, $tableName5, $tableName15, $tableName30, $tableName60]);

        $current_coin = $coinType->findOrFail($coin_id);
        $change_coin = $coinType->findOrFail($change_coin_id);

        return view('admin.coin.create', compact('current_coin', 'change_coin'));
    }


    //开启交易对
    public function switch($base_coin_id, $exchange_coin_id, $switch)
    {
        $key = strtoupper('INSIDE_TEAM_' . $base_coin_id . '_' . $exchange_coin_id);

        if ($switch) {
            event(new AdminUserBehavior(auth('web')->user()->id,"关闭交易对:{$key}",'交易对'));
            $this->redisHmset($key,['switch' => 0]);
        } else {
            event(new AdminUserBehavior(auth('web')->user()->id,"开启交易对:{$key}",'交易对'));
            $this->redisHmset($key,['switch' => 1]);
        }

        return back()->with('success', '操作成功');
    }


    //修改数量
    public function changeNumber($base_coin_id, $exchange_coin_id,InsideSetting $insideSetting,Request $request)
    {
        $key = strtoupper('INSIDE_TEAM_' . $base_coin_id . '_' . $exchange_coin_id);
        $data = $this->redisHgetAll($key);
        if (request()->isMethod('get')) {
            $insideSetting = $insideSetting->getSetting($base_coin_id,$exchange_coin_id);
            return view('admin.change.edit', compact('data','insideSetting'));
        } elseif (request()->isMethod('post')) {
            $this->validate($request, [
                'day_sell_num_limit' => 'required',
                'fee' => 'required|numeric',
            ]);
            $this->redisHmset($key, ['vol' => request()->vol]);
//            if (request('day_sell_num_limit') !== null){
                $insideSetting->setOne($base_coin_id,$exchange_coin_id,request('day_sell_num_limit'),request('fee'));
//            }
        }
        event(new AdminUserBehavior(auth('web')->user()->id,"修改交易对",'交易对'));

        return redirect()->route('change.index')->with('success', '操作成功');
    }
}
