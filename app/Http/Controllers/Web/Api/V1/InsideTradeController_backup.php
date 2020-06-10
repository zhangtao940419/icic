<?php

namespace App\Http\Controllers\Web\Api\V1;

use App\Logic\InsideTradeLogic;
use App\Model\Admin\Banner;
use App\Model\CoinType;
use App\Model\InsideListBuy;
use App\Model\InsideListSell;
use App\Model\InsideSetting;
use App\Model\InsideTradeBuy;
use App\Model\InsideTradeOrder;
use App\Model\InsideTradeSell;
use App\Model\InsideUserLastTradeTime;
use App\Model\InsideUserSellDayNum;
use App\Model\OutsideTrade;
use App\Traits\RedisTool;
use App\Model\WalletDetail;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\BaseController;
use App\Traits\Tools;
use App\Server\InsideTrade\InsideTradeServer;
use App\Rules\PointRule;



class InsideTradeController_backup extends BaseController
{
    use Tools,RedisTool;

    private $inSideTradeServer;
    private $insideTradeLogic;


    public function __construct(InsideTradeServer $insideTradeServer, InsideTradeLogic $insideTradeLogic)
    {
        $this->inSideTradeServer = $insideTradeServer;
        $this->insideTradeLogic = $insideTradeLogic;

    }


    /*
     * 获取场内所有的信息
     *  @param void
     *
     *  return array
     */
    public function getTradeTeamList(OutsideTrade $outsideTrade, Banner $banner)
    {
        $tradeTeamList = $this->inSideTradeServer->getAllCoin();
        foreach ($tradeTeamList as $key=>$value){
            if ($value['switch'] == 0) unset($tradeTeamList[$key]);
        }
        sort($tradeTeamList);
        //轮播图
        $banner = $banner->getBanner();

        //随机获取三个交易
//        $transaction_pair = $outsideTrade->getThreeTransaction();

        $transaction_pairs = [];

//        if ($transaction_pair) {
//            foreach ($transaction_pair as $v) {
//                $v['coin_name'] = $v['get_coin']['coin_name'];
//                $v['user_img'] = $v['get_user_info']['user_headimg'];
//                $v['CNY_price'] = $this->changeTo_Other_Coin($v['get_coin']['coin_id']);
//                unset($v['get_user_info'], $v['get_coin']);
//                $transaction_pairs[] = $v;
//            }
//        }

        $data['banner'] = $banner;
        $data['Transaction_pairs'] = $transaction_pairs;
        $data['tradeTeamList'] = $tradeTeamList;

        return response()->json(['message' => '数据获取成功!', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $data]);
    }

    //icic矿池检测
    public function checkOreBalance($baseCoinId,$exchangeId)
    {
        $user = current_user();
        $icicCoin = (new CoinType())->getRecordByCoinName(env('COIN_SYMBOL'));
        if ($baseCoinId != $icicCoin->coin_id && $exchangeId != $icicCoin->coin_id) return true;
        (new InsideUserLastTradeTime())->insertOne($user->user_id,$baseCoinId,$exchangeId);//更新最后交易时间

        $oreBalance = (new WalletDetail())->getCoinOrePoolBalance($icicCoin->coin_id,$user->user_id);
        if ($oreBalance == 0 || $oreBalance < $icicCoin->coin_fees->ore_pool_min){
            return false;
        }
        return true;

    }

    //检测卖出次数
    public function checkSellNum($userId,$baseCoinId,$exchangeCoinId,$tradeType)
    {

            if ($tradeType == 0) return true;
        if (\App\Model\User::find($userId)->is_special_user) return true;

        $inside_setting_sell_num_limit = (new InsideSetting())->getOneDaySellNumLimit($baseCoinId,$exchangeCoinId);
        if ($inside_setting_sell_num_limit === false) return true;
        if ((new InsideUserSellDayNum())->getTodayNum($userId,$baseCoinId,$exchangeCoinId) >= $inside_setting_sell_num_limit) return false;return true;

    }

    /*  场内交易发起挂单入库
     *  @param
     *   user_id :买单发起的发用户id;
     *
     *   unit_price:买入的价格，单价；
     *   base_coin_id：当trade_type为0时，代表想用该币种买入exchange_coin_id；当trade_type为1时则代表想用exchange_coin_id买入该币种；
     *   exchange_coin_id：当trade_type为0时，代表想用base_coin_id买入该币种；当trade_type为1时则代表想用该币种买入base_coin_id；
     *   trade_total_num：买单发起的准备交易的总数量；
     *   trade_type：交易类型；
     *   return @费率按用户得到什么虚拟货币就扣什么虚拟货币
     */
    public function saveInsideTrade(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'user_id' => 'required|integer',
            'unit_price' => 'required|numeric|min:0.0001',
            'base_coin_id' => 'required|integer',
            'trade_type' => 'required|integer',
            'exchange_coin_id' => 'required|integer',
            'trade_total_num' => ['required',new PointRule],
        ])) return response()->json($result);
        if (!$this->checkOreBalance($request->base_coin_id,$request->exchange_coin_id)) return api_response()->zidingyi('矿池余额不足,不能进行交易');
        if (!$this->checkSellNum($request->user_id,$request->base_coin_id,$request->exchange_coin_id,$request->trade_type)) return api_response()->zidingyi('超过有效交易次数');

        //if ($request->user_id != 1333)return response()->json(['message' => '系统维护,暂停交易', 'status_code' => self::STATUS_INSIDE_ORDER_HAS_TRADE_SOME]);
        //return response()->json(['message' => '系统维护,暂停交易', 'status_code' => self::STATUS_INSIDE_ORDER_HAS_TRADE_SOME]);
        //return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数有误.']);
        $inSideOrderParam = array_merge($request->all(), ['order_number' => ($request->trade_type ? 'sell':'buy') . time() . rand(1000, 9999) . $request->all()['user_id']]);
        //dd($inSideOrderParam);
        $result=$this->insideTradeLogic->saveInsideOrder($inSideOrderParam);
        $this->redisDelete('INSIDE:TRADE:LOCK:'.$inSideOrderParam['base_coin_id'].':'.$inSideOrderParam['exchange_coin_id']);//解redis锁
        $this->redisDelete('user:trade:lock:'.$inSideOrderParam['base_coin_id'].':'.$inSideOrderParam['exchange_coin_id']);//解交易订单锁
        if($result===2){
            if($request->trade_type == 1) (new InsideUserSellDayNum())->incTodayNum($request->user_id,$request->base_coin_id,$request->exchange_coin_id);
            return response()->json(['message' => '交易已完成!', 'status_code' => self::STATUS_INSIDE_ORDER_TRADE_SUCCESS]);
        }
        if($result===3){
            if($request->trade_type == 1) (new InsideUserSellDayNum())->incTodayNum($request->user_id,$request->base_coin_id,$request->exchange_coin_id);
            return response()->json(['message' => '已将订单入库等待匹配!', 'status_code' => self::STATUS_INSIDE_ORDER_WAIT_TRADE]);
        }
        if($result===-3)
            return response()->json(['message' => '交易币种余额不足', 'status_code' => self::STATUS_CODE_BALANCE_UNENOUGH]);
        if($result===-2)
            return response()->json(['message' => '订单撮合失败，请联系管理员', 'status_code' => self::STATUS_INSIDE_ORDER_TRADE_ERROR]);
        if($result===-4)
            return response()->json(['message' => '系统出错，请联系管理员', 'status_code' => self::STATUS_CODE_HANDLE_FAIL]);
        if($result===5){
            if($request->trade_type == 1) (new InsideUserSellDayNum())->incTodayNum($request->user_id,$request->base_coin_id,$request->exchange_coin_id);
            return response()->json(['message' => '订单已交易一部分，其余部分等待交易', 'status_code' => self::STATUS_INSIDE_ORDER_HAS_TRADE_SOME]);
        }


    }

    /*  场内盘面
     *  @param
     *
     *
     *
     */
    public function getTradeDisksurface(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'base_coin_id' => 'required|integer',
            'exchange_coin_id' => 'required|integer',
        ])) return response()->json($result);
        $inParam = $request->all();
        // dd($this->inSideTradeServer->getDisksurfaceServer($inParam));
        return response()->json(['message' => '数据获取成功！', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $this->inSideTradeServer->getDisksurfaceServer($inParam)]);
    }

    /* 场内撤销
     *  @param
     *  Request $request
     *  return json
     */
    public function cancelInsideOrder(Request $request)
    {
        if($result = $this->verifyField($request->all(),[
            'user_id' => 'required|integer',
            'order_number' => 'required|string',
            'trade_type' => 'required|integer',
        ])) return response()->json($result);
        // dd($this->inSideTradeServer->getDisksurfaceServer($inParam));

        $inParam =$request->all();

        $result = $this->insideTradeLogic->cancelInsideOrder($inParam);
        if($result===1){
            if($request->trade_type == 1){
                if ((new InsideTradeOrder())->getSellOrderDealNum($request->order_number) == 0){
                    $sellOrder = (new InsideTradeSell())->getOrder($request->order_number);
                    (new InsideUserSellDayNum())->decTodayNum($request->user_id,$sellOrder->base_coin_id,$sellOrder->exchange_coin_id);
                }
            }
                return response()->json(['message' => '撤单成功！', 'status_code' => self::STATUS_CODE_SUCCESS]);
        }

        if($result===0)
            return response()->json(['message' => '撤单失败！', 'status_code' => self::STATUS_INSIDE_CANNEL_FAIL]);
        if($result===-6)
            return response()->json(['message' => '系统正在撮单，不允许撤单！', 'status_code' => self::STATUS_INSIDE_CANNEL_FAIL]);
        return response()->json(['message' => '系统出错，请联系管理员！', 'status_code' => self::STATUS_CODE_HANDLE_FAIL]);

    }



    /* 获取场内交易历史委托
     * @param
     * Request $request
     *
     * return json
     */
    public function getInsideHistoryTrade(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'user_id' => 'required|integer',
            'base_coin_id' => 'required|integer',
            'exchange_coin_id' => 'required|integer',
            'trade_type' => 'required|integer',
        ])) return response()->json($result);

        $inSideParam = $request->all();
        $data = $this->insideTradeLogic->getInsideHistoryTrade($inSideParam);
        //dd($data);

        return response()->json(['message' => '数据获取成功！', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $data]);
    }

    /* 获取场内订单匹配记录
     * @param
     * Request $request
     *
     * return json
     */
    public function getCarefulInsideHistoryTrade(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'order_number' => 'required|string',
            'trade_type' => 'required|integer',
        ])) return response()->json($result);

        $inSideParam = $request->all();
        $data = $this->insideTradeLogic->getCarefulInsideHistoryTrade($inSideParam);

        return response()->json(['message' => '数据获取成功！', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $data]);
    }


    //获取单个交易对价格
    public function getOneTradeTeamList(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'base_coin_id' => 'required|integer',
            'exchange_coin_id' => 'required|integer',
        ])) return response()->json($result);

        $key = 'INSIDE_TEAM_' . $request->base_coin_id . '_' . $request->exchange_coin_id;
        $res = $this->redisHgetAll($key);

        if (!empty($res)) {
            $data['base_coin_name'] = $res['base_coin_name'];
            $data['exchange_coin_name'] = $res['exchange_coin_name'];
            $data['current_price'] = $res['current_price'];
        }

        return response()->json(['message' => '数据获取成功!', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $data]);
    }


    //获取最近委托
    public function getTrade(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'base_coin_id' => 'required|integer',
            'exchange_coin_id' => 'required|integer',
            'user_id' => 'required|integer',
            'trade_type' => 'required|integer'
        ])) return response()->json($result);

        $inSideParam = $request->all();
        $res = $this->insideTradeLogic->getManyTrade($inSideParam);

//        dd($res);
        //查询交易费率
        $change_rate = !empty($this->redisHgetAll('INSIDE_RATE')['rate']) ? : 0.0002;

        if (!empty($res)) {
            foreach ($res as $k=>$v) {
                $res[$k]['finish_num'] = $v['want_trade_count'] - $v['trade_total_num'];
                $res[$k]['charge'] = number_format($change_rate * $v['want_trade_count'], 5);
                //获取已交易匹配记录
                $res[$k]['trading_record'] = $this->insideTradeLogic->getCarefulInsideHistoryTrade(['order_number'=>$v['order_number'],'trade_type'=>$inSideParam['trade_type']]);
            }
        }

        return response()->json(['message' => '查询成功', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $res]);
    }

    //场内查询购买货币的剩余余额
//    public function getBalance(Request $request, InsideTrade $insideTrade)
//    {
//        if ($result = $this->verifyField($request->all(), [
//            'base_coin_id' => 'required|integer',
//            'exchange_coin_id' => 'required|string',
//        ])) return response()->json($result);
//
//        $balance = $insideTrade->getBalance($request->base_coin_id, $request->exchange_coin_id);
//
//        return response()->json(['message' => '查询成功', 'status_code' => self::STATUS_CODE_SUCCESS, 'balance' => $balance]);
//    }


    /* 管理系统请求盘面数据
     *
     *
     *
     */
    public function adminGetTradeDisksurface(Request $request)
    {

        if ($result = $this->verifyField($request->all(), [
            'base_coin_id' => 'required|integer',
            'exchange_coin_id' => 'required|integer',
            'pageSize' => 'required|integer',
        ])) return response()->json($result);
        $inParam = $request->all();
        // dd($this->inSideTradeServer->getDisksurfaceServer($inParam));
        return response()->json(['message' => '数据获取成功！', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $this->inSideTradeServer->adminGetTradeDisksurfaceServer($inParam)]);

    }
    //获取特定价格用户挂单详细
    public function adminGetInsideList(Request $request,InsideTradeSell $insideTradeSell,InsideTradeBuy $insideTradeBuy)
    {

        if ($request->type == 1){//卖单
            $data = $insideTradeSell->with(['user.userIdentify'])->where(['unit_price'=>$request->price,'base_coin_id' => $request->base_coin_id,'exchange_coin_id' => $request->exchange_coin_id,'trade_statu'=>1])->get();

        }else{

            $data = $insideTradeBuy->with(['user.userIdentify'])->where(['unit_price'=>$request->price,'base_coin_id' => $request->base_coin_id,'exchange_coin_id' => $request->exchange_coin_id,'trade_statu'=>1])->get();

        }

        return response()->json(['message' => '数据获取成功！', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $data]);

    }

    //场内货币的剩余余额
    public function getUserCoinBalance(Request $request, WalletDetail $walletDetail)
    {
        if ($result = $this->verifyField($request->all(), [
            'coin_id' => 'required|integer',
            'user_id' => 'required|integer',
        ])) return response()->json($result);

        $data['balance'] = $walletDetail->getCoinUsableBalance($request->coin_id,$request->user_id);

        return response()->json(['message' => '查询成功', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $data]);
    }

    /*
     *  控制场内交易开关接口
     *
     *  @param
     *
     *  Request $request
     */
    public function insideTradeSwitch(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'base_coin_id' => 'required|integer',
            'exchange_coin_id' => 'required|integer',
            'user_id' => 'required|integer',
        ])) return response()->json($result);

        $inParam = $request->all();

        $result['tradeTeamMessage'] = $this->inSideTradeServer->insideTradeSwitchServer($inParam);

        return response()->json(['message' => '查询成功', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $result]);

    }

    /*
        redis
     */
    public function redisTest()
    {
        $this->inSideTradeServer->redisTest();
    }


    //货币简介
    public function getCoinContent(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'coin_id' => 'required|integer',
        ])) return response()->json($result);

        $data['content'] = CoinType::where('coin_id', $request->coin_id)->value('coin_content');

        return response()->json(['message' => '查询成功', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $data]);
    }


    //邀请界面
    public function Invitation(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'user_id' => 'required|integer',
        ])) return response()->json($result);

        $data['code'] = User::findOrFail($request->user_id)->select('user_Invitation_code');
        $data['son'] = User::where('pid', $request->user_id)->count();
//        $data[''] = User::where('pid', $request->user_id)->count();
        return response()->json(['message' => '查询成功', 'status_code' => self::STATUS_CODE_SUCCESS, 'data' => $data]);
    }
}
