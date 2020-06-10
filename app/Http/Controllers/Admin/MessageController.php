<?php

namespace App\Http\Controllers\Admin;

use App\Model\CoinType;
use App\Model\OutsideTrade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function index(Request $request, OutsideTrade $outsideTrade)
    {
        $data = [];
        if ($request->trade_order) {
            $data = [
                ['trade_order', 'like', '%' . $request->trade_order . '%'],
            ];
        }

        $where = [];
        if (!empty($request->coin_id)) {
            $where = ['coin_id' => $request->coin_id];
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

        $outsideTrades = $outsideTrade->with(['getUserinfo', 'getCoin'])
            ->where($data)
            ->where($where)
            ->where($trade_type)
            ->whereHas('getUserinfo', function ($query) use($username) {
                $query->where($username);
            })
            ->latest()->paginate();

        return view('admin.message.index', compact('outsideTrades', 'coins'));
    }

    public function show($id)
    {
        $outsideTrade = OutsideTrade::findOrFail($id);

        return view('admin.message.show', compact('outsideTrade'));
    }
}


