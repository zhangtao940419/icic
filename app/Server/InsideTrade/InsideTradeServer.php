<?php

namespace App\Server\InsideTrade;

use App\Model\CoinDes;
use App\Model\CoinType;
use App\Traits\RedisTool;
use App\Traits\Match;
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

class InsideTradeServer
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
     //数据扩大1000000倍；
     private $multiple=1000000;
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
                                InsideTradeSellDao $insideTradeSellDao)
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

        empty($this->redisHgetAll('INSIDE_RATE')['rate']) ? : $this->rate = $this->redisHgetAll('INSIDE_RATE')['rate'];

        $this->userDao =$userDao;

    }

      /* 获取盘面数据
       *
       */
      public function  getDisksurfaceServer($inParam)
      {
          $newDisksur['buy'] = $this->insideCountBuyDao->getBuyDisksurface($inParam);
          $newDisksur['sell'] = $this->insideCountSellDao->getSellDisksurface($inParam);
          $newDisksur['tradeTeam'] = $this->redisHgetAll($this->tradeTeam.$inParam['base_coin_id'].'_'.$inParam['exchange_coin_id']);

          return $newDisksur;
      }

      /*后台管理请求盘面数据
       *
       *
       */
      public function adminGetTradeDisksurfaceServer($inParam)
      {
          $newDisksur['buy'] = $this->insideCountBuyDao->getBuyDisksurface($inParam);
          $newDisksur['sell'] = $this->insideCountSellDao->adminGetSellDisksurface($inParam);
          $newDisksur['tradeTeam'] = $this->redisHgetAll($this->tradeTeam.$inParam['base_coin_id'].'_'.$inParam['exchange_coin_id']);
//           $DisksurfaceData = $this->getDisksurfaceServer($inParam);

           return $newDisksur;
      }

       /*  场内开关控制
        *
        *
        */
      public function insideTradeSwitchServer($insideParam)
      {
         $tradeTeamMessage['trade_switch']=0;
         $tradeTeamMessage['user_auth']=0;
         $tradeTeam =  $this->redisHgetAll($this->tradeTeam.$insideParam['base_coin_id'].'_'.$insideParam['exchange_coin_id']);
         if($tradeTeam){
             $tradeTeamMessage['trade_switch']=$tradeTeam['switch'];
         }

         $tradeTeamMessage['user_auth'] = $this->userDao->getOneRecord(['user_id'=>$insideParam['user_id'],'is_usable'=>1])->toArray()['is_special_user'];
         return $tradeTeamMessage;
      }

      /* 获取所有的币种
      */
     public function getAllCoin()
     {
         $tradeTeamList = [];
         $coin = $this->coinTypeDao->getAllCoinType();
         foreach ($coin as $key =>$value){
             $coinList = $this->getList('TRADE_TEAM_'.$value['coin_name']);
             if($coinList){
                 foreach ($coinList as $item){
                     $item = unserialize($item);
                     $res = $this->redisHgetAll('INSIDE_TEAM_'.$value['coin_id'].'_'.$item['coin_id']);
                     if(!empty($res)){
                         $tradeTeamList[] = $res;
                     }
                 }
             }
         }

          foreach ($tradeTeamList as $key => $value){
              $tradeTeamList[$key]['coin_icon'] = CoinDes::where('coin_symbol', $value['exchange_coin_name'])->value('coin_icon');
          }

         return $tradeTeamList;
     }


//     public function getSymbolCoinPrice()
//     {
//         $coin = $this->coinTypeDao->getAllCoinType();
//         foreach ($coin as $key =>$value){
//             $coinList = $this->getList('TRADE_TEAM_'.$value['coin_name']);
//             if($coinList){
//                 foreach ($coinList as $item){
//                     $item = unserialize($item);
//                     $res = $this->redisHgetAll('INSIDE_TEAM_'.$value['coin_id'].'_'.$item['coin_id']);
//                     if(!empty($res)){
//                         $tradeTeamList[] = $res;
//                     }
//                 }
//             }
//         }
//
//
//
//
//
//     }

}
