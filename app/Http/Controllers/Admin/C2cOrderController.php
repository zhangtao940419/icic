<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Handlers\ExcelHtml;
use App\Model\C2CTradeOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class C2cOrderController extends Controller
{
    public function index(Request $request)
    {
        //订单号搜索
        $data = [];
        if (!empty($request->trade_order)) {
            $data =  ['order_number', $request->trade_order];
        }

        //交易类型搜索
        $trade_type = [];
        if (!empty($request->trade_type)) {
            $trade_type = ['trade_type' => $request->trade_type];
        }

        //订单状态搜索
        $order_status = [];
        if ($request->order_status != null) {
            $order_status = ['order_status' => $request->order_status];
        }

        //时间搜索
        $time = [];
        if (empty($request->begin_time) && empty($request->end_time)) {
//            $time = ['2016-10-09 13:51:20', date('Y-m-d H:i:s', time())];
        } elseif (!empty($request->begin_time) && empty($request->end_time)) {
            $time = [$request->begin_time, date('Y-m-d H:i:s', time())];
        }elseif (!empty($request->begin_time) && !empty($request->end_time)) {
            $time = [$request->get('begin_time', '2016-10-09 13:51:20'), $request->get('end_time', date('Y-m-d H:i:s', time()))];
        }

        //用户名筛选
        $username = [];
        if ($request->username) {
            if (preg_match("/^1[345678]{1}\d{9}$/", $request->username)) {
                $username =  [['user_phone', 'like', '%' . $request->username . '%']];
            } else {
                $username = [['user_name', 'like', '%' . $request->username . '%']];
            }
        }

        //交易方搜索
        $business_username = [];
        if ($request->business_username) {
            if (preg_match("/^1[345678]{1}\d{9}$/", $request->business_username)) {
                $business_username =  [['user_phone', 'like', '%' . $request->business_username . '%']];
            } else {
                $business_username = ['user_name' => $request->business_username];
            }
        }

        $builder = C2CTradeOrder::query()->with(['tradeMsg', 'tradeMsg.coin', 'tradeMsg.currency', 'tradeMsg.userMsg', 'user'])
            ->where(function ($query) use($data) {
                $query->where($data);
            })
            ->where($order_status);


        if ($trade_type){
            $builder->whereHas('tradeMsg', function ($query) use ($trade_type) {
                $query->where($trade_type);
            });
        }
        if ($username){
            $builder->whereHas('tradeMsg.userMsg', function ($query) use($username) {
                $query->where($username);
            });
        }
        if ($business_username){
            $builder->whereHas('user', function ($query) use ($business_username) {
                $query->where($business_username);
            });
        }
        if ($time){
            $builder = $builder->whereBetween('created_at', $time);
        }

//        $builder = C2CTradeOrder::query()->with(['tradeMsg', 'tradeMsg.coin', 'tradeMsg.currency', 'tradeMsg.userMsg', 'user'])
//            ->where(function ($query) use($data) {
//                $query->where($data);
//            })
//            ->whereHas('tradeMsg', function ($query) use ($trade_type) {
//                $query->where($trade_type);
//            })
//            ->where($order_status)
//            ->whereBetween('created_at', $time)
//            ->whereHas('tradeMsg.userMsg', function ($query) use($username) {
//                $query->where($username);
//            })
//            ->whereHas('user', function ($query) use ($business_username) {
//                $query->where($business_username);
//            });

//dd($builder->with(['userIdentify','tradeMsg.userIdentify'])->latest()->get());
        if ($request->excel) return $this->outExcel($builder->with(['userIdentify','tradeMsg.userIdentify'])->latest()->get());
        //统计数量
        $builder_ex = C2CTradeOrder::with(['tradeMsg', 'tradeMsg.coin', 'tradeMsg.currency', 'tradeMsg.userMsg', 'user'])
            ->Join('c2c_trade', 'c2c_trade.trade_id', '=', 'c2c_trade_order.trade_id')
            ->where(function ($query) use($data) {
                $query->where($data);
            })
            ->where($order_status);


        if ($trade_type){
            $builder_ex->whereHas('tradeMsg', function ($query) use ($trade_type) {
                $query->where($trade_type);
            });
        }
        if ($username){
            $builder_ex->whereHas('tradeMsg.userMsg', function ($query) use($username) {
                $query->where($username);
            });
        }
        if ($business_username){
            $builder_ex->whereHas('user', function ($query) use ($business_username) {
                $query->where($business_username);
            });
        }
        if ($time){
            $builder_ex->whereBetween('c2c_trade_order.created_at', $time);
        }

//        $sum = C2CTradeOrder::with(['tradeMsg', 'tradeMsg.coin', 'tradeMsg.currency', 'tradeMsg.userMsg', 'user'])
//            ->Join('c2c_trade', 'c2c_trade.trade_id', '=', 'c2c_trade_order.trade_id')
//            ->where(function ($query) use($data) {
//                $query->where($data);
//            })
//            ->whereHas('tradeMsg', function ($query) use ($trade_type) {
//                $query->where($trade_type);
//            })
//            ->where($order_status)
//            ->whereBetween('c2c_trade_order.created_at', $time)
//            //买方搜索
//            ->whereHas('tradeMsg.userMsg', function ($query) use($username) {
//                $query->where($username);
//            })
//            ->whereHas('user', function ($query) use ($business_username) {
//                $query->where($business_username);
//            })
////            ->get()
//            ->sum('trade_number');
        $sum = $builder_ex->sum('trade_number');
//dd($sum);
//        $sum = $builder
//            ->Join('c2c_trade', 'c2c_trade.trade_id', '=', 'c2c_trade_order.trade_id')
//            ->sum('c2c_trade.trade_number');

        //排序
        if ($c2corder = $request->get('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $c2corder, $m)) {
                $teq = C2CTradeOrder::with(['tradeMsg', 'tradeMsg.coin', 'tradeMsg.currency', 'tradeMsg.userMsg', 'user'])
                    ->Join('c2c_trade', 'c2c_trade.trade_id', '=', 'c2c_trade_order.trade_id')
                    ->where(function ($query) use($data) {
                        $query->where($data);
                    })
                    ->whereHas('tradeMsg', function ($query) use ($trade_type) {
                        $query->where($trade_type);
                    })
                    ->where($order_status)
                    ->whereHas('tradeMsg.userMsg', function ($query) use($username) {
                        $query->where($username);
                    })
                    ->whereHas('user', function ($query) use ($business_username) {
                        $query->where($business_username);
                    })
                    ->orderBy($m[1], $m[2]);
                if ($time) $teq->whereBetween('c2c_trade_order.created_at', $time);
                $orders = $teq
                    ->paginate(10);
            }
        } else {
            $orders = $builder->latest('order_id')->paginate(10);
        }

        $excel = $request->fullUrl().'&excel=1';
        if (strpos($request->fullUrl(),'?') === false) $excel = $request->fullUrl().'?excel=1';

        return view('admin.c2corder.index', compact('orders', 'data', 'where', 'sum', 'time', 'c2corder','excel'));
    }

    public function outExcel($orders)
    {
        $header = ['交易id','交易号','挂单用户','用户手机','订单类型','货币类型','交易数量','单价(cny)','付款码','商家姓名','商家手机','商家银行卡','交易状态','接单时间','完成时间'];
        $list = [];
        foreach($orders as $order){
            $tradeType = ['用户购买','用户出售'][$order->tradeMsg->trade_type - 1];
            $orderStatus = ['已撤销','商家已接单,待确认','商家已确认,待审核','已完成','超时自动撤单'][$order->order_status];
            $list[] = [$order->order_id,$order->order_number,$order->tradeMsg->userIdentify->identify_name,$order->tradeMsg->userMsg->user_phone,$tradeType,$order->tradeMsg->coin[0]->coin_name,$order->tradeMsg->trade_number,$order->tradeMsg->trade_price,$order->order_pay_number,$order->userIdentify->identify_name,$order->user->user_phone,$order->bank_card_no,$orderStatus,$order->created_at,$order->updated_at];
        }
        event(new AdminUserBehavior(auth('web')->user()->id,"导出excel:c2c订单信息",'导出excel'));
        return (new ExcelHtml())->ExcelPull('tts_c2c交易信息表',$header,$list);

    }

    public function show(C2CTradeOrder $c2corder)
    {
        return view('admin.c2corder.show', compact('c2corder'));
    }




    /*后台审核接口*/
    public function checkTransferImg($order_id, $check_status)
    {
        //1通过2拒绝
        if (!in_array($check_status,[1,2])) {
            return back()->with('danger', '操作失败');
        }
        $des = ['通过','拒绝'][$check_status-1];
        switch ((new C2CTradeOrder())->confirmTransfer($order_id,$check_status)){
            case 0:
                return back()->with('danger', '操作失败');
                break;
            case 1:
                event(new AdminUserBehavior(auth('web')->user()->id,"商家买单审核:{$des},订单号:{$order_id}",'商家买单审核',C2CTradeOrder::find($order_id)->business_user_id));
                return back()->with('success', '操作成功');
                break;
            case 2:
                break;
        }

    }

}


