<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Handlers\ExcelHtml;
use App\Model\C2CTrade;
use App\Model\InsideTradeOrder;
use App\Model\InsideTradeSell;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class C2cMessageController extends Controller
{
    public function index(Request $request,InsideTradeSell $insideTradeSell)
    {
        $data = [];
        if ($request->trade_order) {
            $data = [
                ['trade_order', 'like', '%' . $request->trade_order . '%']
            ];
        }

        $trade_type = [];
        if (!empty($request->trade_type)) {
            $trade_type = ['trade_type' => $request->trade_type];
        }

        $username = [];
        if (!empty($request->username)) {
//            if (preg_match("/^1[345678]{1}\d{9}$/", $request->username)) {
//                $username = ['user_phone' => $request->username];
//            } else {
//                $username = ['user_name' => $request->username];
//            }
            $username = ['user_phone' => $request->username];
        }
//dd($username);
        $status = [];
        if ($request->status != null) {
            if ($request->status == -1){
                $status = ['check_status'=>0];
            }else{
                $status = ['trade_status' => $request->status];
                if ($request->status == 1) $status['check_status'] = 1;
            }
        }

        //时间搜索
//        $time = [$request->get('begin_time', '2016-10-09 13:51:20'), $request->get('end_time', date('Y-m-d H:i:s', time()))];
        $time = [];
        if (empty($request->begin_time) && empty($request->end_time)) {
//            $time = ['2016-10-09 13:51:20', date('Y-m-d H:i:s', time())];
        } elseif (!empty($request->begin_time) && empty($request->end_time)) {
            $time = [$request->begin_time, date('Y-m-d H:i:s', time())];
        }elseif (!empty($request->begin_time) && !empty($request->end_time)) {
            $time = [$request->get('begin_time', '2016-10-09 13:51:20'), $request->get('end_time', date('Y-m-d H:i:s', time()))];
        }

        $builder = C2CTrade::query()->with(['coin', 'userMsg','userIdentify'])
            ->where($data)
            ->where($trade_type)
            ->where($status);

        if ($username){
            $builder->whereHas('userMsg', function ($query) use($username) {
                    $query->where($username);
                });
        }
        if ($time){
            $builder->whereBetween('created_at', $time);
        }


        //统计数量
        $sum = $builder->sum('trade_number');

        //排序
        if ($order = $request->get('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                $builder->orderBy($m[1], $m[2]);
            }
        }//dd($builder->with('userIdentify')->latest()->get());

        if ($request->excel) return $this->outExcel($builder->with('userIdentify')->latest()->get());
        $trades = $builder->latest('trade_id')->paginate();

        $excel = $request->fullUrl().'&excel=1';
        if (strpos($request->fullUrl(),'?') === false) $excel = $request->fullUrl().'?excel=1';

        return view('admin.c2cmessage.index', compact('trades', 'data', 'sum', 'time' ,'order','excel','insideTradeSell'));
    }








    public function outExcel($trades)
    {
        $header = ['订单id','订单号','挂单用户','用户手机','订单类型','货币类型','订单数量','单价(cny)','订单状态','挂单时间','完成时间'];
        $list = [];
        foreach($trades as $trade){
            $tradeType = ['购买','出售'][$trade->trade_type - 1];
            $tradeStatus = ['已撤销','待接单','交易中','已完成'][$trade->trade_status];
            $list[] = [$trade->trade_id,$trade->trade_order,$trade->userIdentify->identify_name,$trade->userMsg->user_phone,$tradeType,$trade->coin[0]->coin_name,$trade->trade_number,$trade->trade_price,$tradeStatus,$trade->created_at,$trade->updated_at];
        }
        event(new AdminUserBehavior(auth('web')->user()->id,"导出excel:c2c订单信息",'导出excel'));
        return (new ExcelHtml())->ExcelPull('tts_c2c订单信息表',$header,$list);

    }

    public function show(C2CTrade $c2cmessage)
    {
        return view('admin.c2cmessage.show', compact('c2cmessage'));
    }

    public function check(Request $request,C2CTrade $c2CTrade)
    {
        $trade = $c2CTrade->find($request->trade_id);
        if ($trade->trade_type == 2){
            switch ($c2CTrade->checkSellTrade($request->trade_id,$request->status)){
                case 0:
                    return back()->with('danger','操作失败');
                    break;
                case 1:
                    $bdes = $request->status == 1 ? '通过' : '拒绝';
                    event(new AdminUserBehavior(auth('web')->user()->id,"c2c卖单审核:{$bdes},订单号:{$request->trade_id}",'c2c订单审核',$trade->user_id));
                    return back()->with('success','操作成功');
                    break;
            }
        }else{
            switch ($c2CTrade->checkBuyTrade($trade,$request->status)){
                case 0:
                    return back()->with('danger','操作失败');
                    break;
                case 1:
                    $bdes = $request->status == 1 ? '通过' : '拒绝';
                    event(new AdminUserBehavior(auth('web')->user()->id,"c2c买单审核:{$bdes},订单号:{$request->trade_id}",'c2c订单审核',$trade->user_id));
                    return back()->with('success','操作成功');
                    break;
            }
        }


    }


    //c2c用户管理
    public function userList(Request $request)
    {

        $username = [];
        if (!empty($request->username)) {
            if (preg_match("/^1[345678]{1}\d{9}$/", $request->username)) {
                $username = ['user_phone' => $request->username];
            } else {
                $username = ['user_name' => $request->username];
            }
        }

        $query = C2CTrade::query()->with(['userMsg','userIdentify'])
            ->whereHas('userMsg', function ($query) use($username) {
            $query->where($username);
        });

//        $totalTrade = $query->

        if ($request->excel) return $this->outExcelUserList($query->groupBy('user_id')->latest()->get());

        $userList = $query->groupBy('user_id')->latest()->paginate(15);

        $excel = $request->fullUrl().'&excel=1';
        if (strpos($request->fullUrl(),'?') === false) $excel = $request->fullUrl().'?excel=1';

        return view('admin.c2cmessage.userList', compact('userList','excel'));

        dd($userList->toArray());



    }

    public function outExcelUserList($users)
    {

        $header = ['用户id','会员电话','真实姓名','累计买入','累计卖出'];
        $list = [];
        foreach($users as $user){
            $list[] = [$user->userMsg->user_id,$user->userMsg->user_phone,$user->userIdentify->identify_name,$user->getUserTotalBuy(),$user->getUserTotalSell()];
        }
        event(new AdminUserBehavior(auth('web')->user()->id,"导出excel:tts_用户信息表",'导出excel'));
        return (new ExcelHtml())->ExcelPull('tts_c2c用户信息表',$header,$list);

    }

}


