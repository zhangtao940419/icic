<?php

namespace App\Http\Controllers\Admin;

use App\Model\CoinType;
use App\Model\InsideTradeOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InsideOrderController extends Controller
{
    public function index(Request $request)
    {
        $order_number = [];
        if (!empty($request->order_number)) {
            $order_number = [
                ['order_number', 'like', '%' . $request->order_number . '%'],
            ];
        }

        $where = [];
        if (!empty($request->base_coin_id) || !empty($request->exchange_coin_id)) {
            $where = ['base_coin_id' => $request->base_coin_id, 'exchange_coin_id' => $request->exchange_coin_id];
        }

        $trade_type = [];
        if ($request->trade_type != null) {
            $trade_type = ['trade_type' => $request->trade_type];
        }

        $username = [];
        if (!empty($request->username)) {
            $username = [['user_name', 'like', '%' . $request->username . '%']];
        }


        $coins = CoinType::all();
        $insideTradeOrders = InsideTradeOrder::with('getBaseCoin', 'getExchangeCoin', 'user', 'tradeUser')
            ->where('is_usable', 1)
            ->where($order_number)
            ->Where($where)
            ->where($trade_type)
            ->whereHas('user', function ($query) use($username) {
                $query->where($username);
            })
            ->latest()->paginate();


        return view('admin.inside_order.index', compact('insideTradeOrders', 'coins', 'trade_type', 'where'));
    }


    public function show(InsideTradeOrder $insideTradeorder)
    {
        return view('admin.inside_order.show', compact('insideTradeorder'));
    }
}


