<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 11:24
 */

namespace App\Logic;


use App\Events\OutsideOrderConfirmBehavior;
use App\Exceptions\ApiException;
use App\Handlers\ExchangeHelper;
use App\Http\Response\ApiResponse;
use App\Model\CenterWalletRecord;
use App\Model\UserDatum;
use App\Server\OutsideTrade\Dao\OutsideTrade;
use App\Server\OutsideTrade\Dao\OutsideTradeOrderDao;
use App\Server\OutsideTrade\Dao\OutsideWalletDao;
use App\Traits\FileTools;
use App\Traits\RedisTool;
use App\Traits\Tools;
use Illuminate\Support\Facades\DB;

class OutsideTransactionLogic
{
    use Tools, RedisTool, ApiResponse, FileTools;

    private $outsideTradeOrderDao;
    private $outsideWalletDao;
    private $outsideTradeDao;

    private $exchangeHelper;

    private $fee = '0.007';//0.7%

    public function __construct(OutsideTradeOrderDao $outsideTradeOrderDao, OutsideWalletDao $outsideWalletDao, OutsideTrade $outsideTradeDao, ExchangeHelper $exchangeHelper)
    {
        $this->outsideTradeOrderDao = $outsideTradeOrderDao;
        $this->outsideWalletDao = $outsideWalletDao;
        $this->outsideTradeDao = $outsideTradeDao;
        $this->exchangeHelper = $exchangeHelper;
    }


    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiException
     * 接单
     */
    public function acceptTrade($data)
    {
//        dd()
        DB::beginTransaction();
        $records = $this->outsideTradeOrderDao->where(['user_id' => $data['user_id']])->whereIn('order_status', [1, 2])->count();
        if ($records >= 5) throw new ApiException('有未完成交易', 3504);

        $trade = $this->outsideTradeDao->getTrade($data['trade_id'], ['*'], 1);

        if ($data['user_id'] == $trade->user_id) throw new ApiException('不能接自己的单', 3504);
        if ($trade->trade_status != 1) throw new ApiException('订单不可用', 3504);

        if ($trade->trade_is_visual == 0) {
            $timeLimit = json_decode($trade->trade_limit_time, true);
            $h = (int)date('H');
            if ($h < $timeLimit[0] || $h > $timeLimit[1])
                throw new ApiException('非接单时间', 3504);
        }

        if ($trade->trade_with_trust){
            if (!in_array($data['user_id'],$this->getTrustList($trade->user_id))) throw new ApiException('对方限制了受信任的人进行交易', 3504);
        }

        if ($trade->trade_price_type == 1) {
            $money = bcmul($data['order_coin_num'], $trade->trade_price, 8);
            $price = $trade->trade_price;
        } else {
            $price = $this->exchangeHelper->getCoinToCurrency($trade->coin->coin_name, $trade->currency->currency_code, 1, 10, $trade->trade_premium_rate);
//            if ($trade->trade_premium_rate < 0){
//                $rate = ($trade->trade_premium_rate * -1)/100;
//                $price = bcsub($nowPrice,bcmul($rate,$nowPrice,8),8);
//            }else{
//                $rate = $trade->trade_premium_rate / 100;
//                $price = bcadd($nowPrice,bcmul($rate,$nowPrice,8),8);
//            }
            if ($trade->trade_type == 0 && (bccomp($trade->trade_ideality_price, $price, 8) == 1))
                throw new ApiException('当前价格过低', 3504);
            if ($trade->trade_type == 1 && (bccomp($price, $trade->trade_ideality_price, 8) == 1))
                throw new ApiException('当前价格过高', 3504);
            $money = bcmul($data['order_coin_num'], $price, 8);
        }
        //dd($money);
        if (bccomp($money, $data['order_total_money'], 8) != 0)
            throw new ApiException('订单信息已过期,请刷新', 3504);

        if ($money < $trade->trade_min_limit_price || $money > $trade->trade_max_limit_price)
            throw new ApiException('金额区间限制', 3504);

        if (bccomp($data['order_coin_num'], $trade->trade_left_number, 8) == 1)//可用不足
            throw new ApiException('订单信息已过期,请刷新', 3504);

        $data['order_price'] = $price;
        $data['trade_id'] = $trade->trade_id;
        $data['trade_user_id'] = $trade->user_id;
        $data['coin_id'] = $trade->coin_id;
        $data['currency_id'] = $trade->currency_id;
        $data['order_fee'] = $this->fee;
        $data['order_number'] = 'cw' . ['b', 's'][$trade->trade_type] . $data['user_id'] . time() . rand(1000, 9999);
        $data['order_type'] = $trade->trade_type;

        $result1 = $trade->decrement('trade_left_number', $data['order_coin_num']);

        if ($trade->trade_type == 0) {//用户购买

            if (
                $result1
                && ($orderId = $this->outsideTradeOrderDao->saveOrder($data))
            ) {
                DB::commit();
                return $this->successWithData(['order_id'=>$orderId]);
            }
            DB::rollBack();
            return $this->error();
        } else {//用户售出
            $userWallet = $this->outsideWalletDao->getOneRecord($data['user_id'], $trade->coin_id, 1);
            $fee = bcmul($data['order_coin_num'], $this->fee, 8);
            $amount = bcadd($fee, $data['order_coin_num'], 8);

            if (bccomp($amount, $userWallet->wallet_usable_balance, 8) == 1)
                throw new ApiException('余额不足', 3008);

            if (
                $result1
                && $userWallet->decrement('wallet_usable_balance', $amount)
                && $userWallet->increment('wallet_freeze_balance', $amount)
                && ($orderId = $this->outsideTradeOrderDao->saveOrder($data))
            ) {
                DB::commit();
                return $this->successWithData(['order_id'=>$orderId]);
            }
            DB::rollBack();
            return $this->error();

        }


    }


    /**
     * 买家取消
     */
    public function buyCancelOrder($data)
    {
        DB::beginTransaction();

        $order = $this->outsideTradeOrderDao->getRecord($data['order_id'], ['*'], 1);

        if ($order->order_status != 1) throw new ApiException('订单不可取消', 3504);
        $trade = $this->outsideTradeDao->getTrade($order->trade_id, ['*'], 1);

        if ($data['user_id'] != $order->user_id && $data['user_id'] != $order->trade_user_id)
            throw new ApiException('订单不可用', 3504);

        if ($data['user_id'] == $order->user_id && $trade->trade_type == 0) {//买家为正常用户
            if (
                $order->update(['order_status' => 0])
                && $trade->increment('trade_left_number', $order->order_coin_num)
            ) {
                DB::commit();
                return $this->success();
            }
            DB::rollBack();
            return $this->error();
        } elseif ($data['user_id'] == $order->trade_user_id && $trade->trade_type == 1) {

            $fee = bcmul($order->order_coin_num, $order->order_fee, 8);
            $amount = bcadd($fee, $order->order_coin_num, 8);
            $userWallet = $this->outsideWalletDao->getOneRecord($order->user_id, $order->coin_id, 1);

            if (
                $order->update(['order_status' => 0])
                && $trade->increment('trade_left_number', $order->order_coin_num)
                && $userWallet->increment('wallet_usable_balance', $amount)
                && $userWallet->decrement('wallet_freeze_balance', $amount)
            ) {
                DB::commit();
                return $this->success();
            }
            DB::rollBack();
            return $this->error();

        }

        throw new ApiException('订单不可用', 3504);


    }

    //买家确认
    public function confirmBuyOrder($data)
    {
        $filePath = '/app/outside/' . $this->putImage($data['image'], date('Y-m', time()), 'outside');
        DB::beginTransaction();

        $order = $this->outsideTradeOrderDao->getRecord($data['order_id'], ['*'], 1);

        $trade = $this->outsideTradeDao->getTrade($order->trade_id);

        if (
            $order->order_status != 1
            || ($order->user_id != $data['user_id'] && $order->trade_user_id != $data['user_id'])
            || ($order->user_id == $data['user_id'] && $trade->trade_type != 0)
            || ($order->trade_user_id == $data['user_id'] && $trade->trade_type != 1)
        ) {
            throw new ApiException('订单不可用', 3504);
        }

        if ($order->update(['order_status' => 2, 'order_pay_prove' => $filePath])) {
            DB::commit();
            return $this->success();
        }
        DB::rollBack();
        return $this->error();

    }

    //卖家确认收款,交易完成
    public function sellerConfirmOrder($data)
    {
        DB::beginTransaction();

        $order = $this->outsideTradeOrderDao->getRecord($data['order_id'], ['*'], 1);

        $trade = $this->outsideTradeDao->getTrade($order->trade_id, ['*'], 1);

        if (
            $order->order_status != 2
            || ($order->user_id != $data['user_id'] && $order->trade_user_id != $data['user_id'])
            || ($order->user_id == $data['user_id'] && $trade->trade_type != 1)
            || ($order->trade_user_id == $data['user_id'] && $trade->trade_type != 0)
        ) throw new ApiException('订单不可用', 3504);

        $result1 = $order->update(['order_status' => 3]);

        $userWallet = $this->outsideWalletDao->getOneRecord($order->user_id, $order->coin_id, 1);
        $tradeUserWallet = $this->outsideWalletDao->getOneRecord($order->trade_user_id, $order->coin_id, 1);

        $centerWalletRecord = new CenterWalletRecord();

        if ($order->user_id == $data['user_id']) {//用户是卖家
            $sellerFee = bcmul($order->order_fee, $order->order_coin_num, 8);
            $sellerLockAmount = bcadd($sellerFee, $order->order_coin_num, 8);
            $result2 = $userWallet->decrement('wallet_freeze_balance', $sellerLockAmount);
            $centerWalletRecord->saveOneRecord($order->coin_id, $sellerFee, '场外手续费', $order->user_id, 1);//中央钱包收入

            $buyerFee = bcmul($trade->trade_fee, $order->order_coin_num, 8);
            $buyerAmount = bcsub($order->order_coin_num, $buyerFee, 8);
            $result3 = $tradeUserWallet->increment('wallet_withdraw_balance', $buyerAmount);
            $centerWalletRecord->saveOneRecord($order->coin_id, $buyerFee, '场外手续费', $order->trade_user_id, 1);//中央钱包收入
            $result4 = 1;
            //if (bccomp($trade->trade_left_number,0,8) == 0) $result4 = $trade->update(['trade_status'=>2]);
            if ($result1 && $result2 && $result3 && $result4) {
                DB::commit();
                //交易完成事件
                event(new OutsideOrderConfirmBehavior($order->order_id));
                return $this->success();
            }
            DB::rollBack();
            return $this->error();
        } else {//挂单者是卖家

            $sellerFee = bcmul($trade->trade_fee, $order->order_coin_num, 8);
            $sellerLockAmount = bcadd($sellerFee, $order->order_coin_num, 8);
            $result2 = $tradeUserWallet->decrement('wallet_freeze_balance', $sellerLockAmount);
            $centerWalletRecord->saveOneRecord($order->coin_id, $sellerFee, '场外手续费', $order->trade_user_id, 1);//中央钱包收入

            $buyerFee = bcmul($order->order_fee, $order->order_coin_num, 8);
            $buyerAmount = bcsub($order->order_coin_num, $buyerFee, 8);
            $result3 = $userWallet->increment('wallet_withdraw_balance', $buyerAmount);
            $centerWalletRecord->saveOneRecord($order->coin_id, $buyerFee, '场外手续费', $order->trade_user_id, 1);//中央钱包收入
            $result4 = 1;
            //if (bccomp($trade->trade_left_number,0,8) == 0) $result4 = $trade->update(['trade_status'=>2]);
            if ($result1 && $result2 && $result3 && $result4) {
                DB::commit();
                //此处应分发交易完成事件
                event(new OutsideOrderConfirmBehavior($order->order_id));
                return $this->success();
            }
            DB::rollBack();
            return $this->error();

        }

    }

    //和别人的交易记录
    public function getOrderWithOther($data)
    {

        $orders = $this->outsideTradeOrderDao->getWithOtherOrders($data['user_id'], $data['target_userid'])->toArray();
        foreach ($orders as &$order) {

            if ($data['target_userid'] == $order['user_id']) {
                $order['order_type'] = $order['order_type'] ? 0 : 1;
                $order['user'] = $order['order_user'];
            } else {
                $order['user'] = $order['trade_user'];
            }
            unset($order['trade_user']);
            unset($order['order_user']);
        }

        return $this->successWithData($orders);

    }


    //wodedingdan
    public function getMyOrder($data)
    {

        $orders = $this->outsideTradeOrderDao->getMyOrders($data['user_id'], $data['status'])->toArray();
//dd($orders);
        foreach ($orders as &$order) {

            if ($data['user_id'] == $order['trade_user_id']) {
                $order['trade_user'] = $order['order_user'];
                $order['order_type'] = $order['order_type'] ? 0 : 1;
                $order['comment'] = $order['trade_user_comment'];
            } else {
                $order['comment'] = $order['order_user_comment'];
            }
            unset($order['order_user']);
        }

        return $this->successWithData($orders);

    }

    //订单详情
    public function getOrderDetail($data)
    {
        $order = $this->outsideTradeOrderDao->getOrderDetail($data['user_id'], $data['order_id']);
        if (!$order) throw new ApiException('订单不可用', 3504);
        $order = $order->toArray();

        if ($data['user_id'] == $order['trade_user_id']) {
            $order['order_type'] = $order['order_type'] ? 0 : 1;
            if ($order['order_type'] == 0) {
                $order['buy_user'] = $order['trade_user'];
                $order['sell_user'] = $order['order_user'];
            } else {
                $order['buy_user'] = $order['order_user'];
                $order['sell_user'] = $order['trade_user'];
            }
            $order['comment'] = $order['trade_user_comment'];
        } else {
            if ($order['order_type'] == 0) {
                $order['buy_user'] = $order['order_user'];
                $order['sell_user'] = $order['trade_user'];
            } else {
                $order['buy_user'] = $order['trade_user'];
                $order['sell_user'] = $order['order_user'];
            }
            $order['comment'] = $order['order_user_comment'];
        }
        unset($order['trade_user']);
        unset($order['order_user']);
        return $this->successWithData($order);
    }

    //pingjia
    public function comment($data)
    {
        $order = $this->outsideTradeOrderDao->getRecord($data['order_id']);

        if ($order->user_id != $data['user_id'] && $order->trade_user_id != $data['user_id']) throw new ApiException('订单不可用', 3504);
        if ($order->order_status != 3) throw new ApiException('交易未完成', 3504);

        if ($order->user_id == $data['user_id']){
            if ($order->order_user_comment) return $this->success();
            $order->update(['order_user_comment'=>$data['comment']]);
            if($data['comment'] == 1) (new UserDatum())->addTradeFavourableComment($order->trade_user_id);
        }else{
            if ($order->trade_user_comment) return $this->success();
            $order->update(['trade_user_comment'=>$data['comment']]);
            if($data['comment'] == 1) (new UserDatum())->addTradeFavourableComment($order->user_id);
        }

        return $this->success();
    }


    public function getTradeOrderList($data)
    {
        $orders = $this->outsideTradeOrderDao->getTradeOrderList($data['trade_id'])->toArray();

        return $this->successWithData($orders);

    }


}