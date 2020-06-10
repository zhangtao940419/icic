<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Model\CoinTradeOrder;
use App\Http\Controllers\Controller;
use App\Model\CoinType;
use App\Server\CoinServers\GethServer;
use App\Model\EthToken;
use App\Server\AdminCoinServer;
use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethTokenServer;
use Illuminate\Http\Request;
use App\Traits\RedisTool;

class CoinOrderController extends Controller
{

    use RedisTool;


    private $coinSymbol;

    public function __construct()
    {
        $this->coinSymbol = env('COIN_SYMBOL');
    }

    public function index(Request $request)
    {
        $data = [];
        if (!empty($request->order_type)) {
            $data = ['order_type' => $request->order_type];
        }
        $where = [];
        if (!empty($request->coin_id)) {
            $where = ['coin_id' => $request->coin_id];
        }

        $search = [];
        if (!empty($request->username)) {
            if (preg_match("/^1[345678]{1}\d{9}$/", $request->username)) {
                $search =  ['user_phone' => $request->username];
            } else {
                $search = ['user_name' => $request->username];
            }
        }

        $addressTo = [];
        $addressFrom = [];
        $address = $request->address;
        if (!empty($address)) {
            $addressTo = function ($q) use ($request){
                $q->where('order_trade_to',$request->address)->orWhere('order_trade_from',$request->address);
            };
//            $addressTo = ['order_trade_to' => $request->address];
//            $addressFrom = ['order_trade_from' => $request->address];
        }
        $needShow = 0;
        if ($data && $addressTo){
            $needShow = $data['order_type'];
        }

        $order_id = [];
        if (!empty($request->order_id)) {
            $order_id = ['order_id' => $request->order_id];
        }

        $time = [$request->get('begin_time', '2016-10-09 13:51:20'), $request->get('end_time', date('Y-m-d H:i:s', time()))];
        if (empty($request->begin_time) && empty($request->end_time)) {
            $time = ['2016-10-09 13:51:20', date('Y-m-d H:i:s', time())];
        } elseif (empty($request->end_time)) {
            $time = [$request->begin_time, date('Y-m-d H:i:s', time())];
        }

        $order_check_status = [];
        if ($request->order_check_status != null) {
            $order_check_status = ['order_check_status' => $request->order_check_status];
        }


        $coins = CoinType::all();

        $builder = CoinTradeOrder::query()->with('user.userIdentify', 'coinName')->where('is_usable' , 1)
            ->where($data)
            ->where($where)
            ->whereHas('user', function ($query) use ($search) {
                $query->where($search);
            })
            ->Where($addressTo)
//            ->orWhere($addressFrom)
            ->where($order_id)
            ->where($order_check_status)
            ->whereBetween('created_at', $time)
            ->orderBy('order_check_status', 'asc');


        //统计
        $count = $builder->sum('order_trade_money');

        //排序
        if ($order = $request->get('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                $builder->orderBy($m[1], $m[2]);
            }
        }

        $coinorder = $builder->latest()->paginate();

        return view('admin.coin_order.index', compact('coinorder', 'data', 'coins', 'where', 'search', 'count', 'time', 'order','needShow','address'));
    }

    public function show(CoinTradeOrder $coinorder)
    {
        return view('admin.coin_order.show', compact('coinorder'));
    }


    /*后台审核提币接口*/
    public function checkWithdraw($order_id, $check_status, CoinTradeOrder $coinTradeOrder,AdminCoinServer $adminCoinServer)
    {
        $order = $coinTradeOrder->getRecordById($order_id);

        if ($this->redisExists('WITHDRAW_'.$order_id))
            return back()->with('danger', '请勿重复提交');
        $this->stringSet('WITHDRAW_'.$order_id, $order_id);

        switch ($order['coin_name']['coin_name']) {
            case 'BTC':
                $result = $adminCoinServer->checkWithdrawCoin((new BitCoinServer()), $order, $check_status);
                break;
            case 'ETH':
                $result = $adminCoinServer->checkWithdrawCoin((new gethServer()), $order, $check_status);
                break;

            case 'USDT':
                $token = (new EthToken())->getRecordByCoinId($order['coin_id'])->toArray();
                $order['token'] = $token;
                $result = $adminCoinServer->checkWithdrawCoin((new GethTokenServer($token['token_contract_address'], $token['token_contract_abi'])), $order, $check_status);
                break;
            default:
//            case $this->coinSymbol:
                $token = (new EthToken())->getRecordByCoinId($order['coin_id'])->toArray();
                $order['token'] = $token;
                if ($token && $token['token_contract_address'] !== '0x'){
                    $result = $adminCoinServer->checkWithdrawCoin((new GethTokenServer($token['token_contract_address'], $token['token_contract_abi'])), $order, $check_status);
                }else{
                    $result = $adminCoinServer->checkWithdrawCoin((new GethServer()), $order, $check_status);
                }

                break;

        }
        $this->redisDelete('WITHDRAW_' . $order_id);
        switch ($result) {
            case 0:
                return back()->with('danger', '未知的错误,请联系管理员');
                break;
            case 1:

//                $data = ['content' => request()->user()->username . '于' . $order['updated_at'] . '将order_id为' . $order['order_id'] . "的订单" . $check_status == 1 ? '允许提币了' : '拒绝提币了'];
//                \DB::table('operation_log')->insert($data);
                $status = $check_status == 1 ? '通过' : '拒绝';
                event(new AdminUserBehavior(auth('web')->user()->id,"审核提币_{$status}_订单号:{$order_id}",'审核提币',$order['user_id']));

                return back()->with('success', '操作成功');
                break;
            case 2:

                return back()->with('danger', '中央钱包余额不足');
                break;
            case 3:

                return back()->with('danger', '操作失败');
                break;
            default:

                return back()->with('danger', '未开放提币');
                break;
        }
    }


}
