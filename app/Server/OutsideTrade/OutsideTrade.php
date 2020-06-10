<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 16:36
 */

namespace App\Server\OutsideTrade;

use App\Exceptions\ApiException;
use App\Handlers\ExchangeHelper;
use App\Http\Response\ApiResponse;
use App\Model\CoinType;
use App\Model\User;
use App\Model\WorldArea;
use App\Model\WorldCurrency;
use App\Server\OutsideTrade\Dao\OutsideTrade as OutsideTradeDao;
use App\Server\OutsideTrade\Dao\OutsideTradeOrderDao;
use App\Server\OutsideTrade\Dao\OutsideWalletDao;
use App\Traits\Tools;
use Illuminate\Support\Facades\DB;

class OutsideTrade
{
    use ApiResponse,Tools;

    private $outsideTradeDao;
    private $outsideWalletDao;
    private $outsideTradeOrderDao;
    private $fee = 0.007;
    public function __construct(OutsideTradeDao $outsideTradeDao,OutsideWalletDao $outsideWalletDao)
    {
        $this->outsideTradeDao = $outsideTradeDao;
        $this->outsideWalletDao = $outsideWalletDao;
    }


    public function saveOutsideTrade($data)
    {//dd($data);
        //return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数有误.']);
        $outSideOrderParam = array_merge($data, ['trade_order' => 'cw' . time() . rand(1000, 9999) . $data['user_id']]);
        /*入库前逻辑检查*/

        try{
            if (
                ($trades = $this->outsideTradeDao->getRecords(['user_id' => $data['user_id'], 'coin_id' => $data['coin_id'],'trade_status'=>1, 'trade_type' => $data['trade_type']]))
                && count($trades->toArray()) >= 2
            ) throw new ApiException('同一币种不允许发布两次以上的广告，请先处理相关的广告', 3019);
            DB::beginTransaction();

            if ($data['trade_type'] == 0) {//售出

                $amount = bcadd(bcmul($this->fee, $data['trade_number'], 8),$data['trade_number'],8);
                $userWallet = $this->outsideWalletDao->getOneRecord($data['user_id'], $data['coin_id'], 1);
                if (bccomp($amount, $userWallet->wallet_usable_balance, 8) > 0) throw new ApiException('余额不足', 3008);

                $outSideOrderParam['trade_fee'] = $this->fee;

                if (
                    $userWallet->decrement('wallet_usable_balance', $amount)
                    && $userWallet->increment('wallet_freeze_balance', $amount)
                    && $this->outsideTradeDao->saveTrade($outSideOrderParam)
                ) {
                    DB::commit();
                    return $this->success();
                }
                DB::rollBack();return $this->error();

            }else{//买入
                if ($this->outsideTradeDao->saveTrade($outSideOrderParam)) {
                    DB::commit();return $this->success();
                }
                DB::rollBack();return $this->error();
            }

            DB::rollBack();return $this->error();

        }catch (\ErrorException $exception){
            DB::rollBack();return $this->error();
        }


    }

    public function getCoinPrice($data)
    {
        $exchangeHelper = new ExchangeHelper();
        $coin = CoinType::find($data['coin_id']);
        $currency = WorldCurrency::find($data['currency_id']);
        //if (!$currency)


        $price = $exchangeHelper->getCoinToCurrency($coin->coin_name,$currency->currency_code,1,10);
        return $this->successWithData(['price'=>$price]);

    }




    /*  撤销广告
     *  @param Request $request
     *  param:
     *  trade_id:订单id
     *  user_id:用户id
     *  @return \Illuminate\Http\JsonResponse
     */

    public function cancelTrade($data){

        DB::beginTransaction();

        $trade = $this->outsideTradeDao->getTrade($data['trade_id'],['*'],1);

        if (
            $data['user_id'] != $trade->user_id
            || $trade->trade_status != 1
            || (bccomp(0,$trade->trade_left_number,8) == 0)
        ) throw new ApiException('订单不可用',3504);

        $this->outsideTradeOrderDao = new OutsideTradeOrderDao();

        $ongoingOrderNum = $this->outsideTradeOrderDao->where(['trade_id'=>$data['trade_id']])->whereIn('order_status',[1,2])->count();
        if ($ongoingOrderNum != 0) throw new ApiException('有进行中的交易,不能撤单',3504);

        $tradeLeftFee = bcmul($trade->trade_left_number,$trade->trade_fee,8);
        $leftAmount = bcadd($tradeLeftFee,$trade->trade_left_number,8);

        if($trade->trade_type == 0){//出售
            $userWallet = (new OutsideWalletDao())->getOneRecord($data['user_id'],$trade->coin_id,1);

            if (
                $trade->update(['trade_status'=>0])
                && $userWallet->decrement('wallet_freeze_balance',$leftAmount)
                && $userWallet->increment('wallet_usable_balance',$leftAmount)
            ){
                DB::commit();return $this->success();
            }
            DB::rollBack();return $this->error();

        }else{//买入

            if ($trade->update(['trade_status'=>0])) {
                DB::commit();return $this->success();
            }
            DB::rollBack();return $this->error();

        }


    }

//获取所有地区
    public function getAllArea()
    {
        $data = (new WorldArea())->getWorldArea();//dd($data);
        return $this->successWithData($data);

    }

    //获取所有币种
    public function getAllCoin()
    {
        $data = CoinType::select('coin_id','coin_name')->where(['is_outside'=>1])->get();
        return $this->successWithData($data);

    }

    public function getAllCurrency()
    {
        $data = WorldCurrency::select(['currency_code','currency_id'])->get();
        return $this->successWithData($data);

    }

    /* 获取场外市场广告订单的信息
         *  @param Request $request
         *  param:
         *  trade_type:挂单类型
         *  coin_id:虚拟货币类型
         *  location_id:国家地区id
         *  page:页数
         *  page_size:每页获取的数据大小
         *  @return \Illuminate\Http\JsonResponse
         */
    public function getAllTrade($data){

        $exchangeHelper = new ExchangeHelper();
        $trades = $this->outsideTradeDao->getOutsideTrade($data);

        foreach ($trades as $key => &$trade)
        {
//            $trade['get_money_type'] = explode(',',$trade['get_money_type']);
//            $trade['trade_limit_time'] = json_decode($trade['trade_limit_time'],true);
            if ($trade['trade_price_type'] == 1){
                $leftMoney = bcmul($trade['trade_left_number'],$trade['trade_price'],2);//dd($leftMoney);
//                dd($leftMoney);
            }else{
                $leftMoney = $exchangeHelper->getCoinToCurrency($trade['coin']['coin_name'],$trade['currency']['currency_code'],$trade['trade_left_number'],10,$trade['trade_premium_rate']);
                $trade['trade_price'] = $exchangeHelper->getCoinToCurrency($trade['coin']['coin_name'],$trade['currency']['currency_code'],1,10,$trade['trade_premium_rate']);
                if ($data['trade_type'] == 0){
                    if (bccomp($trade['trade_price'],$trade['trade_ideality_price'],4) == -1){
                        unset($trades[$key]);continue;
                    }
                }else{
                    if (bccomp($trade['trade_ideality_price'],$trade['trade_price'],4) == -1){//dd(1);
                        unset($trades[$key]);continue;
                    }
                }
                //dd($leftMoney);
            }
            if (bccomp($trade['trade_min_limit_price'],$leftMoney,8) == 1) unset($trades[$key]);
            if (bccomp($trade['trade_max_limit_price'],$leftMoney,8) == 1) $trade['trade_max_limit_price'] = $leftMoney;

        }
        if ($data['trade_type'] == 0){
            $trades = $this->arraySort($trades,'trade_price',SORT_ASC);
        }else{
            $trades = $this->arraySort($trades,'trade_price');
        }
//dd($trades);
        return $this->successWithData($trades);


    }



    public function getPersonalMsg($data)
    {
        $user = (new User())->with('datum')->select(['user_id','user_name','user_phone','user_headimg','outside_grade','outside_point'])->find($data['user_id'])->toArray();
        $tradeNum = $this->outsideTradeDao->where(['user_id'=>$data['user_id'],'coin_id'=>$data['coin_id'],'trade_status'=>1])->count();
//dd($data);
        $historyOrderNum = (new OutsideTradeOrderDao())->where(['coin_id'=>$data['coin_id'],'order_status'=>3])->where(function ($q) use ($data){
            $q->where('user_id',$data['user_id'])->orWhere('trade_user_id',$data['user_id']);
        })->sum('order_coin_num');//dd($historyOrderNum->toArray());

        $user['trade_num'] = $tradeNum;$user['history_order_num'] = $historyOrderNum;$user['next_grade_point'] = $user['outside_grade'] * 1000;

        return $this->successWithData($user);
    }



    /* 获取单个订单的信息
             * @param Request $request
             *  param:
             *  trade_order:订单信息
             *  trade_id:订单id
             *  Carbon::parse(date("Y-M-d",time()))->dayOfWeek 获取当天是星期几0~6 星期日~星期六;
             * @return \Illuminate\Http\JsonResponse
             */
    public function getOneTradeOrder($tradeId)
    {
        $exchangeHelper = new ExchangeHelper();
        $trade = $this->outsideTradeDao->getTrade($tradeId)->toArray();
        if ($trade['trade_price_type'] == 1){
            $leftMoney = bcmul($trade['trade_left_number'],$trade['trade_price'],2);
            //dd($leftMoney);
        }else{
            $leftMoney = $exchangeHelper->getCoinToCurrency($trade['coin']['coin_name'],$trade['currency']['currency_code'],$trade['trade_left_number'],10,$trade['trade_premium_rate']);
            $trade['trade_price'] = $exchangeHelper->getCoinToCurrency($trade['coin']['coin_name'],$trade['currency']['currency_code'],1,10,$trade['trade_premium_rate']);
            if ($trade['trade_type'] == 0){
                if (bccomp($trade['trade_price'],$trade['trade_ideality_price'],4) == -1) throw new ApiException('订单不可用',3504);
            }else{
                if (bccomp($trade['trade_ideality_price'],$trade['trade_price'],4) == -1) throw new ApiException('订单不可用',3504);
            }
            //dd($leftMoney);
        }
        if (bccomp($trade['trade_min_limit_price'],$leftMoney,8) == 1) throw new ApiException('订单不可用',3504);
        if (bccomp($trade['trade_max_limit_price'],$leftMoney,8) == 1) $trade['trade_max_limit_price'] = $leftMoney;unset($trade['trade_limit_time']);
//        $trade['get_money_type'] = explode(',',$trade['get_money_type']);
//        $trade['trade_limit_time'] = json_decode($trade['trade_limit_time'],true);

        return $this->successWithData($trade);


    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 获取某个人的广告
     */
    public function getUserTrade($userId)
    {

        $exchangeHelper = new ExchangeHelper();
        $trades = $this->outsideTradeDao->getUserTrades(['user_id'=>$userId,'trade_status'=>1]);
//dd($trades);
        foreach ($trades as $key => &$trade)
        {
//            $trade['get_money_type'] = explode(',',$trade['get_money_type']);
//            $trade['trade_limit_time'] = json_decode($trade['trade_limit_time'],true);
            if ($trade['trade_price_type'] == 1){
                $leftMoney = bcmul($trade['trade_left_number'],$trade['trade_price'],2);
                //dd($leftMoney);
            }else{
                $leftMoney = $exchangeHelper->getCoinToCurrency($trade['coin']['coin_name'],$trade['currency']['currency_code'],$trade['trade_left_number'],10,$trade['trade_premium_rate']);
                $trade['trade_price'] = $exchangeHelper->getCoinToCurrency($trade['coin']['coin_name'],$trade['currency']['currency_code'],1,10,$trade['trade_premium_rate']);
                if ($trade['trade_type'] == 0){//卖出广告
                    if (bccomp($trade['trade_price'],$trade['trade_ideality_price'],4) == -1){
                        unset($trades[$key]);continue;//理想价格过线
                    }
                }else{
                    if (bccomp($trade['trade_ideality_price'],$trade['trade_price'],4) == -1){
                        unset($trades[$key]);continue;
                    }
                }
                //dd($leftMoney);
            }
            if (bccomp($trade['trade_min_limit_price'],$leftMoney,8) == 1) unset($trades[$key]);//所剩金额不足以显示
            if (bccomp($trade['trade_max_limit_price'],$leftMoney,8) == 1) $trade['trade_max_limit_price'] = $leftMoney;

        }
//dd($trades);
        return $this->successWithData($trades);

    }


    //广告管理
    public function tradeManage($userId,$status)
    {
        //status 1进行2已完成3下架
        $exchangeHelper = new ExchangeHelper();



        $trades = $this->outsideTradeDao->tradeManage($userId,$status)->toArray();

        foreach ($trades as &$trade){

            if ($trade['trade_price_type'] != 1){
                $trade['trade_price'] = $exchangeHelper->getCoinToCurrency($trade['coin']['coin_name'],$trade['currency']['currency_code'],1,10,$trade['trade_premium_rate']);
            }

        }
        return $this->successWithData($trades);

    }


}