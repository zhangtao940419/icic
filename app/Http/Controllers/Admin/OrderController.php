<?php

namespace App\Http\Controllers\Admin;

use App\Model\CoinType;
use App\Model\OutsideTradeOrder;
use App\Server\OutsideTradeServer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Communication;

class OrderController extends Controller
{
    use Communication;

    public function index(Request $request)
    {
        $data = [];
        if ($request->order_number) {
            $data = [
                ['order_number', 'like', '%' . $request->order_number . '%']
            ];
        }

        $where = [];
        if (!empty($request->coin_id)) {
            $where = ['coin_id' => $request->coin_id];
        }

        $order_type = [];
        if ($request->order_type != null) {
            $order_type = ['order_type' => $request->order_type];
        }

        $username = [];
        if (!empty($request->username)) {
            $username = [['user_name', 'like', '%' . $request->username . '%']];
        }


        $coins = CoinType::all();


        $orders = OutsideTradeOrder::with(['getUserInfo', 'getCoin', 'getOrderInfo'])
            ->where($data)
            ->where($where)
            ->where($order_type)
            ->whereHas('getUserinfo', function ($query) use($username) {
                $query->where($username);
            })
            ->latest()->paginate();

        return view('admin.order.index', compact('orders', 'coins'));
    }

    public function show(OutsideTradeOrder $order)
    {
        return view('admin.order.show', compact('order'));
    }

    //强制撤单
    public function cancelOrder($trade_order, $order_number, OutsideTradeServer $outsideTradeServer)
    {
        $data = [
            'trade_order' => $trade_order,
            'order_number' => $order_number
        ];

        $res = $outsideTradeServer->cancelUserTradeOrderServe($data);

        if ($res == 1) {
            return back()->with('success', '操作成功');
        }
    }

    //强制发货
    public function seedGoods($trade_order, $order_number, OutsideTradeServer $outsideTradeServer)
    {

        $data = [
            'trade_order' => $trade_order,
            'order_number' => $order_number
        ];

        $res = $outsideTradeServer->confirmSend($data);

        if ($res == 1) {
            return back()->with('success', '操作成功');
        }
    }

    //查看聊天记录
    public function showMsg($id)
    {
        $order = OutsideTradeOrder::findOrFail($id);

        return view('admin.order.showmsg', compact('order'));
    }

    public function postMsg(Request $request)
    {
//        $res = $this->getMsg($request->all());
//
//        if ($res.)
//        return $res;
    }
}
