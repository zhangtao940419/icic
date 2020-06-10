<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 9:57
 */

namespace App\Server\InsideTrade;

use App\Model\InsideSetting;
use App\Model\InsideUserSellDayNum;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use App\Traits\Match;
use App\Traits\RedisTool;
use App\Server\InsideTrade\Dao\WalletDetailDao;
use App\Server\InsideTrade\Dao\CenterWalletDao;
use App\Server\InsideTrade\Dao\CoinTypeDao;
use App\Server\InsideTrade\Dao\InsideTradeOrderDao;
use App\Server\InsideTrade\Dao\InsideCountSellDao;
use App\Server\InsideTrade\Dao\InsideCountBuyDao;
use App\Server\InsideTrade\Dao\InsideListSellDao;
use App\Server\InsideTrade\Dao\InsideListBuyDao;
use App\Server\InsideTrade\Dao\InsideTradeSellDao;
use App\Server\InsideTrade\Dao\InsideTradeBuyDao;
use App\Server\InsideTrade\Dao\UserDao;
use Illuminate\Support\Facades\DB;
use App\Jobs\InsideInfo;

class InsideTradeSellServer
{
    use RedisTool,Match;

    private $walletDetailDao=null;
    private $coinTypeDao=null;
    private $insideTradeBuyDao=null;
    private $insideTradeSellDao=null;
    private $insideListBuyDao=null;
    private $insideListSellDao=null;
    private $insideCountBuyDao=null;
    private $insideCountSellDao=null;
    private $insideTradeOrderDao=null;
    private $userDao=null;
    private $centerWalletDao=null;

    //费率
    private $rate=0.002;
    //虚拟币的交易对
    private $tradeTeam='INSIDE_TEAM_';

    //统计用户撮单实际花了多少钱；
    private $has_trade_money=0;

    //用户成交时的购买单价
    private $unit_price=0;

    private $userTradeLock='user:trade:lock:';

    /*构造器*/
    public function __construct(InsideCountBuyDao $insideCountBuyDao,
                                InsideCountSellDao $insideCountSellDao,
                                InsideListBuyDao $insideListBuyDao,
                                InsideListSellDao $insideListSellDao,
                                WalletDetailDao $walletDetailDao,
                                CoinTypeDao $coinTypeDao,
                                InsideTradeOrderDao $insideTradeOrderDao,
                                CenterWalletDao $centerWalletDao,
                                UserDao $userDao,
                                InsideTradeBuyDao $insideTradeBuyDao,
                                InsideTradeSellDao $insideTradeSellDao,InsideSetting $insideSetting)
    {

        $this->insideTradeBuyDao=$insideTradeBuyDao;
        $this->insideTradeSellDao=$insideTradeSellDao;
        $this->insideListBuyDao=$insideListBuyDao;
        $this->insideListSellDao=$insideListSellDao;
        $this->insideCountBuyDao=$insideCountBuyDao;
        $this->insideCountSellDao=$insideCountSellDao;

        $this->walletDetailDao=$walletDetailDao;

        $this->coinTypeDao=$coinTypeDao;

        $this->insideTradeOrderDao=$insideTradeOrderDao;

        $this->centerWalletDao=$centerWalletDao;

//        empty($this->redisHgetAll('INSIDE_RATE')['rate']) ? : $this->rate = $this->redisHgetAll('INSIDE_RATE')['rate'];
        if (request('base_coin_id') && request('exchange_coin_id')) $this->rate = $insideSetting->getFee(request('base_coin_id'),request('exchange_coin_id'));

        $this->userDao =$userDao;

    }

    //获取场内卖单最近委托
    public function getManySell($inSideParam)
    {
        return $this->insideTradeSellDao->getManyTrade($inSideParam['base_coin_id'],$inSideParam['exchange_coin_id'],$inSideParam['user_id']);
    }
    //获取场内卖单匹配记录
    public function getSellMatchRecord($inSideParam)
    {//dd($this->insideTradeOrderDao->getManyRecord(['sell_order_number'=>$inSideParam['order_number']]));
        $records =  $this->insideTradeOrderDao->getManyRecord(['sell_order_number'=>$inSideParam['order_number']]);
        foreach ($records as $key=> $record){
            $records[$key]['trade_poundage'] = bcmul($record['trade_poundage'],$record['unit_price'],12);
        }
        return $records;
    }

    // 获取场内卖单交易历史委托
    public function getInsideHistorySell($inSideParam)
    {
//        $return = $this->insideTradeSellDao->getFinishSell($inSideParam['user_id'], $inSideParam['base_coin_id'], $inSideParam['exchange_coin_id']);
        $return = $this->insideTradeSellDao->getHistorySell($inSideParam['user_id'], $inSideParam['base_coin_id'], $inSideParam['exchange_coin_id']);
        if (!empty($return)) {
            foreach ($return as $k=>$v) {
                $return[$k]['trade_poundage'] = $this->rate;
//                $return[$k]['trade_poundage'] = 0;
            }
        }
        return $return;
    }

    /* 卖单撤销
      * @param
      *   @$userInParam
      *   @$returnMoney
      *
      *  取消挂单逻辑
      *   开启事务；
      *   1）把要返回用户账户的剩余冻结余额返回给用户；
      *   2）将订单标注为撤单；
      *   3）把交易队列中的订单取消；
      *   4）减少相对应的盘面集合中的价格数量；
      *   5）提交或者回滚事务；
      */
    public function cancelInsideSellOrder($inSideParam)
    {
        $order =  $this->insideTradeSellDao->getOneRecord(['order_number'=>$inSideParam['order_number']]);

        //查询撤销的订单是否在锁定的表中
        if($this->sIsMember($this->userTradeLock.$order['base_coin_id'].':'.$order['exchange_coin_id'],$order['order_number'])){
            return -6;//撤销的订单正在交易中，不允许撤销；
        }

        if($order['user_id'] !=$inSideParam['user_id']) return -2;

        //$returnMoney = $order['unit_price'] * $order['trade_total_num'];// 取消挂单后需退还的金额
/*        if($this->walletDetailDao->getWalletDetail()->getCoinFreezeBalance($order['exchange_coin_id'],$inSideParam['user_id'])<$order['trade_total_num'])
            return 0;*/
        DB::beginTransaction();
        $wallet = (new WalletDetail())->getOneRecord($order['user_id'],$order['exchange_coin_id']);
        if($this->walletDetailDao->getWalletDetail()->reduceFreezeBalance($order['exchange_coin_id'], $order['user_id'], $order['trade_total_num'])//回退冻结余额
            && $this->walletDetailDao->addUsableBalance($order['exchange_coin_id'], $order['user_id'], $order['trade_total_num'])//增加可用余额
            && $this->insideTradeSellDao->updateOneRecord(['order_number'=>$order['order_number'],'is_usable'=>1,'trade_statu'=>1],['trade_statu'=>0])//订单状态更新为0撤销状态
            && $this->insideListSellDao->deleteOneRecord(['order_number'=>$order['order_number']])//删除交易队列订单
            && $this->insideCountSellDao->dealCountSell($order,$order['trade_total_num'])//減少盘面数据
            && (new WalletFlow())->insertOne($order['user_id'],$wallet->wallet_id,$order['exchange_coin_id'],$order['trade_total_num'],19,1,'场内撤单',1)//添加流水记录
        ){
            DB::commit();return 1;
        }else{
            DB::rollBack();return 0;
        }
    }

    public function saveInsideSellOrder($SellInSideParam)
    {
       // dd($SellInSideParam);
        if($this->walletDetailDao->getCoinUsableBalance($SellInSideParam['exchange_coin_id'],$SellInSideParam['user_id'])<=($SellInSideParam['trade_total_num']))
            return -3;
        while(1){
            if($this->stringSetNx('INSIDE:TRADE:LOCK:'.$SellInSideParam['base_coin_id'].':'.$SellInSideParam['exchange_coin_id'],'1','5')!=null) {
                //$buyTradeList = array_reverse($this->getZaddByScore($this->buyTradeList.$SellInSideParam['base_coin_id'].'_'.$SellInSideParam['exchange_coin_id'],$this->Bcuml($SellInSideParam['unit_price'],$this->multiple), '+inf'));
                $buyTradeList = $this->insideListBuyDao->getBuyTradeList($SellInSideParam['base_coin_id'],$SellInSideParam['exchange_coin_id'],$SellInSideParam['unit_price'],$SellInSideParam['user_id']);
               // dd($buyTradeList);
                if (!$buyTradeList) {

                    //交易集合里找不到可以交易的数据，则先挂单；
                    if ($this->saveSellOrderAndList($SellInSideParam))
                        return 3;
                    return -4;
                } else {
                    //如果找到可以交易的数据，则进行交易
                    if (!$this->saveSellHandelOrder($SellInSideParam)) return -4;

                   $status=$this->handleSellTrade($buyTradeList,$SellInSideParam);
                        if($status===1){

                            return 2;
                        }
                        if($status===10){

                            return 5;
                        }
                        if($status===0){
                            if(
                            $this->insideTradeSellDao->updateOneRecord(['order_number'=>$SellInSideParam['order_number']],['trade_statu'=>-1])
                          &&  $this->walletDetailDao->addUsableBalance($SellInSideParam['exchange_coin_id'],$SellInSideParam['user_id'],$SellInSideParam['trade_total_num']- $this->has_trade_money)
                          &&  $this->walletDetailDao->reduceFreezeBalance($SellInSideParam['exchange_coin_id'],$SellInSideParam['user_id'],$SellInSideParam['trade_total_num']- $this->has_trade_money)){
                                DB::commit();
                                $walletId = $this->walletDetailDao->getWalletId($SellInSideParam['user_id'],$SellInSideParam['exchange_coin_id']);
                                (new WalletFlow())->insertOne($SellInSideParam['user_id'],$walletId,$SellInSideParam['exchange_coin_id'],$SellInSideParam['trade_total_num']- $this->has_trade_money,22,1,'场内撮单异常返还',1);
                            }else{
                                DB::rollBack();
                            }
                        }
                    return -2;
                }

            }
        }
    }

    /*  保存挂单信息
         *  @param
         *  $inSideParam
         *
         */
    public function saveSellOrderAndList($inSideParam)
    {
        DB::beginTransaction();
        if ($this->saveSellOrder($inSideParam) && $this->saveSellList($inSideParam)){
            DB::commit();
            return 1;
        } else {
            DB::rollBack();
            return 0;
        }

    }

    /*  有存在撮合的情况下进行交易
     *   @param
     *   $inSideParam
     *
     *   思路：
     *    1）先根据最开头的订单查询相关订单，比对查询出来订单的交易额，
     *       a.如果买单的数额大于查出的订单数额，则说明需要继续撮合下一个订单；直到小于查出的订单欲交易数量，同时标志查出来的订单为完成状态；
     *       b，如果买单的数额刚好等于查询出的订单，则只需将两笔订单相互抵消，同时标志查出来的订单为完成状态；
     *       c,如果小于查出来的订单，则说明该订单还不足以消除查出来的订单交易额；
     *
     *      return 0|1;
     */
    public function handleSellTrade($buyTradeData,$sellInSideParam)
    {
        $sellInSideOrder = $this->insideTradeSellDao->getOneRecord(['order_number'=>$sellInSideParam['order_number']]);
        /*          $wait_trade_money = $this->getTotalMoney($sellInSideParam);
                 // dd($wait__trade_money);*/
        $trade_total_num=$sellInSideParam['trade_total_num'];
        // dd($buyTradeData);
        $has_trade_money = 0;
        foreach ($buyTradeData as $key=>$value){
            if($buyResult = $this->insideTradeBuyDao->getOneRecord(['order_number'=>$value['order_number']])){

                if($buyResult['trade_statu'] !=1 ) continue;//是否是可用订单
                $this->sAdd($this->userTradeLock.$buyResult['base_coin_id'].':'.$buyResult['exchange_coin_id'],$buyResult['order_number']);//将交易中的订单锁住；
                //dd($buyResult);
                $trade_total_num=$trade_total_num-$buyResult['trade_total_num'];
                //dd($trade_total_num);
                $this->unit_price =  $buyResult['unit_price'];
                //$has_trade_money = $has_trade_money + $buyResult['trade_total_num'];
                if($trade_total_num>0)
                    if(!$this->dealSellIfGreatThan($buyResult,$sellInSideOrder,(double)$trade_num=$buyResult['trade_total_num']))
                        return 0;
                  $this->has_trade_money =$this->has_trade_money+$buyResult['trade_total_num'];
                if($trade_total_num==0){
                    if(!$this->dealSellIfEqual($buyResult,$sellInSideOrder,(double)$trade_num=$buyResult['trade_total_num']))
                        return 0;
                  $this->has_trade_money =$this->has_trade_money+$buyResult['trade_total_num'];
                return 1;
                }
                if($trade_total_num<0){
                  if(!$this->dealSellIfLessThan($buyResult,$sellInSideOrder,(double)$trade_num=$buyResult['trade_total_num']+$trade_total_num))
                    return 0;
                 $this->has_trade_money =$this->has_trade_money+$buyResult['trade_total_num']+$trade_total_num;
                 return 1;
                }
            }
        }

        if($trade_total_num>0)
        {
            $sellInSideParam['trade_total_num']=$trade_total_num;
            $this->saveSellList($sellInSideParam);
            return 10;
        }

    }

    /*  处理撮合卖订单前将订单入库
            *  @param
            *  $inSideParam
            *   return 0|1
            */
    private function saveSellHandelOrder($inSideParam)
    {
        DB::beginTransaction();
        if ($this->saveSellorder($inSideParam)) {
            DB::commit();
            return 1;
        } else {
            DB::rollBack();
            return 0;
        }
    }

    /*在撮合不到数据的情况，将数据压入相关的集合和数据库*/
    private function saveSellOrder($inSideParam)
    {
        $wallet = (new WalletDetail())->getOneRecord($inSideParam['user_id'],$inSideParam['exchange_coin_id']);
        $oldBalance = $this->walletDetailDao->getWalletBalance($inSideParam['exchange_coin_id'],$inSideParam['user_id']);
        $newBalance['wallet_usable_balance']=$oldBalance['wallet_usable_balance']-$inSideParam['trade_total_num'];
        $newBalance['wallet_freeze_balance']=$oldBalance['wallet_freeze_balance']+$inSideParam['trade_total_num'];
        $newBalance['coin_id'] =$inSideParam['exchange_coin_id'];
        $newBalance['user_id'] =$inSideParam['user_id'];
        //  dump($newBalance);
        $inSideParam['want_trade_count']=$inSideParam['trade_total_num'];
        if ( $this->insideTradeSellDao->insertOneRecord($inSideParam)
            && $this->walletDetailDao->updateBalance($newBalance)
            && (new WalletFlow())->insertOne($wallet['user_id'],$wallet->wallet_id,$wallet->coin_id,$inSideParam['trade_total_num'],7,2,'场内交易',1)
        )
            return 1;
        return 0;
    }



    /* 将买单添加进买单交易队列  更新盘面信息
          * @param
          *  $inSideParam
          *  return 0|1
          */
    private function saveSellList($inSideParam)
    {
        if($this->insideListSellDao->insertOneRecord($inSideParam) && $this->insideCountSellDao->addCountRecord($inSideParam)) return 1;
        return 0;
    }

    /*  处于卖单需要交易数额大于订单的情况
            *  @param
            *  $result:订单；
            *  $inSideParam：准备交易的数据
            */
    private function dealSellIfGreatThan($buyTradeOrder,$sellInSideOrder,$trade_num)
    {

      //  dd(11);
        DB::beginTransaction();
        if(
            $this->insideTradeOrderDao->saveInsideTradeOrder($buyTradeOrder,$sellInSideOrder,$trade_num,$this->unit_price,2)
            &&    $this->insideTradeBuyDao->updateOneRecord(['buy_id'=>$buyTradeOrder['buy_id'],'is_usable'=>1,'trade_statu'=>1],['trade_statu'=>2,'trade_total_num'=>0])
            &&    $this->insideTradeSellDao->getInsideTradeSell()->where('order_number',$sellInSideOrder['order_number'])->decrement('trade_total_num',$trade_num)
            &&    $this->walletDetailDao->dealBuyBalance($buyTradeOrder,$trade_num,$this->unit_price)
            &&    $this->walletDetailDao->dealSellBalance($sellInSideOrder,$trade_num,$this->unit_price)
            &&    $this->insideListBuyDao->deleteOneRecord(['order_number'=>$buyTradeOrder['order_number']])
            &&    $this->insideCountBuyDao->dealCountBuy($buyTradeOrder,$trade_num)
            &&    $this->dealTradeFinishData($this->tradeTeam.$buyTradeOrder['base_coin_id'].'_'.$buyTradeOrder['exchange_coin_id'],$this->unit_price,$trade_num)
        ){
            DB::commit();
            return 1;
        }else {
            DB::rollBack();
            return 0;
        }
    }

    /* 处理卖单刚好等于的情况
      *
      *
      */
    private function dealSellIfEqual($buyTradeOrder,$sellInSideOrder,$trade_num)
    {
        DB::beginTransaction();
        if(
            $this->insideTradeOrderDao->saveInsideTradeOrder($buyTradeOrder,$sellInSideOrder,$trade_num,$this->unit_price,2)
            &&    $this->insideTradeSellDao->updateOneRecord(['sell_id'=>$sellInSideOrder['sell_id'],'is_usable'=>1,'trade_statu'=>1],['trade_statu'=>2,'trade_total_num'=>0])
            &&    $this->insideTradeBuyDao->updateOneRecord(['buy_id'=>$buyTradeOrder['buy_id'],'is_usable'=>1,'trade_statu'=>1],['trade_statu'=>2,'trade_total_num'=>0])
            &&    $this->walletDetailDao->dealBuyBalance($buyTradeOrder,$trade_num,$this->unit_price)
            &&    $this->walletDetailDao->dealSellBalance($sellInSideOrder,$trade_num,$this->unit_price)
            &&    $this->insideListBuyDao->deleteOneRecord(['order_number'=>$buyTradeOrder['order_number']])
            &&    $this->insideCountBuyDao->dealCountBuy($buyTradeOrder,$trade_num)
            &&    $this->dealTradeFinishData($this->tradeTeam.$buyTradeOrder['base_coin_id'].'_'.$buyTradeOrder['exchange_coin_id'],$this->unit_price,$trade_num)

        ){
            DB::commit();
            return 1;
        } else {
            DB::rollBack();
            return 0;
        }
    }

    /* 处理卖单订单交易额少于某个订单的情况
     * @param
     * $sellTradeOrder $buyInSideParam
     *
     *  return
     */
    private function dealSellIfLessThan($buyTradeOrder,$sellInSideOrder,$trade_num)
    {
        DB::beginTransaction();
        if(
            $this->insideTradeOrderDao->saveInsideTradeOrder($buyTradeOrder,$sellInSideOrder,$trade_num,$this->unit_price,2)
            &&    $this->insideTradeSellDao->updateOneRecord(['order_number'=>$sellInSideOrder['order_number']],['trade_statu'=>2,'trade_total_num'=>0])
            &&    $this->insideTradeBuyDao->getInsideTradeBuy()->where('order_number',$buyTradeOrder['order_number'])->decrement('trade_total_num',$trade_num)
            &&    $this->walletDetailDao->dealBuyBalance($buyTradeOrder,$trade_num,$this->unit_price)
            &&    $this->walletDetailDao->dealSellBalance($sellInSideOrder,$trade_num,$this->unit_price)
            &&    $this->insideCountBuyDao->dealCountBuy($buyTradeOrder,$trade_num)
            &&    $this->dealTradeFinishData($this->tradeTeam.$buyTradeOrder['base_coin_id'].'_'.$buyTradeOrder['exchange_coin_id'],$this->unit_price,$trade_num)
        ){
            DB::commit();
            return 1;
        } else {
            DB::rollBack();
            return 0;
        }

    }
    
    /* 处理交易成功后的统计数据
           * @param
           *
           */
    private function dealTradeFinishData($zkey,$unit_price,$trade_num)
    {
        // dump($unit_price);
        if($teamData = $this->redisHgetAll($zkey)){
            if(!isset($teamData['day_vol']))  $teamData['day_vol']=0;
            $begin_price = $teamData['begin_price'];
            $newTeamData = $teamData;
            $newTeamData['vol'] = $teamData['vol'] + $trade_num;
            $newTeamData['day_vol'] = $teamData['day_vol'] + $trade_num;
            $newTeamData['current_price'] =$unit_price;
            $newTeamData['begin_price'] =$begin_price;
            $newTeamData['float_type'] = (($unit_price>$teamData['begin_price'])?'+':'-');
            $newTeamData['max_price'] = (($unit_price>$teamData['max_price'])?$unit_price:$teamData['max_price']);
            $newTeamData['min_price'] = (($unit_price>$teamData['min_price'])?$teamData['min_price']:$unit_price);
            //$newTeamData['price_float'] = $this->Bcdiv(abs($this->bcsub($unit_price,$teamData['current_price'])),$teamData['current_price'],2)*100;//浮动比率
            $newTeamData['price_float'] = $this->Bcdiv(abs($this->Bcsub($unit_price,$teamData['begin_price'])),$teamData['begin_price'],4)*100;//浮动比率
            $newTeamData['CNY_price'] = $unit_price*$this->changeTo_Other_Coin($newTeamData['base_coin_id']);
            // dump($newTeamData['price_float']);
            $this->redisHmset($zkey,$newTeamData);
            $countData= $teamData;
            $countData['vol']=$trade_num;
            $countData['current_price']=$unit_price;
            InsideInfo::dispatch($countData)->onQueue('InsideInfo');
        }
        return 1;
    }

}
