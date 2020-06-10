<?php

namespace App\Http\Controllers\Admin;

use App\Model\CoinType;
use App\Model\InsideTradeSell;
use App\Model\InsideTradeOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InsideSellController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        if ($request->order_number) {
            $data = [
                ['order_number', 'like', '%' . $request->order_number . '%'],
            ];
        }

        $where = [];
        if (!empty($request->base_coin_id)) {
            $where['base_coin_id'] =  $request->base_coin_id;
        }
        if (!empty($request->exchange_coin_id)) {
            $where['exchange_coin_id'] = $request->exchange_coin_id;
        }

        $username = [];
        if (!empty($request->username)) {
            $username = [['user_name', 'like', '%' . $request->username . '%']];
        }

        $trade_statu = [];
        if ($request->trade_statu != null && $request->trade_statu != 2) {
            $trade_statu = ['trade_statu' => $request->trade_statu];
        }

        //时间搜索
        $time = [$request->get('begin_time', '2016-10-09 13:51:20'), $request->get('end_time', date('Y-m-d H:i:s', time()))];
        if (empty($request->begin_time) && empty($request->end_time)) {
            $time = ['2016-10-09 13:51:20', date('Y-m-d H:i:s', time())];
        } elseif (empty($request->end_time)) {
            $time = [$request->begin_time, date('Y-m-d H:i:s', time())];
        }



        $coins = CoinType::all();

        $builder = InsideTradeSell::with('getBaseCoin', 'getExchangeCoin', 'getUser')
            ->where('is_usable', 1)
            ->where($data)
            ->Where($where)
            ->whereHas('getUser', function ($query) use($username) {
                $query->where($username);
            })
            ->where($trade_statu)
            ->whereBetween('created_at', $time)
            ->select(['*',DB::raw('sum(want_trade_count-trade_total_num) as hasTrade')])
        ;

        if ($request->price){
            if ($request->price == 1) $builder = $builder->orderBy('unit_price','asc');
            if ($request->price == 2) $builder = $builder->orderBy('unit_price','desc');

        }

        //想要交易的总数量
        $wantSum = $builder->sum('want_trade_count');//dd($wantSum);

        //剩余的数量
        $totalSum = $builder->sum('trade_total_num');

        //已交易的数量
        $transactionSum = $wantSum - $totalSum;

        $builder = $builder->latest()->groupBy('sell_id');

        if ($request->trade_statu == 2){
            $builder = $builder->having(DB::raw('sum(want_trade_count-trade_total_num)'),'>',0);
        }


        $insideTrades = $builder->paginate();

        return view('admin.insidesell.index', compact('insideTrades', 'data', 'coins', 'trade_type', 'where', 'trade_statu', 'wantSum', 'totalSum', 'transactionSum', 'time'));
    }

    public function show(insideTradeSell $insideTradesell)
    {
        return view('admin.insidesell.show', compact('insideTradesell'));
    }
}


