<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 13:51
 */

namespace App\Handlers;




use App\Model\CoinDes;
use App\Model\CoinType;
use App\Server\HuoBiServer\LibServer\LibServer;
use App\Server\HuoBiServer\Server\HuobiServer;
use App\Traits\RedisTool;

class ExchangeHelper
{

    use RedisTool;

    private $ttlTime = 2000;//s

    public function __construct()
    {
    }



    /**
     * ExchangeHelper constructor.
     * 获取法币对美元的汇率
     */
    public function getUSDExchangeRate($exchangeCurrency = 'CNY')
    {
        switch ($exchangeCurrency){
            case 'CNY':
                return '6.5';
                break;
            case 'USD':
                return 1;
                break;
            default:
                return 0;
                break;
        }

    }

    /**
     * 获取币种对cny的价格
     * $ttl 时效分钟,0为拿取实时价格
     */
    public function getCoinPrice($coinName = 'ICIC',$ttl = 0)
    {
        switch ($coinName){
            case 'QC':
                return 1;
                break;
            case 'ICIC':
                if ($ttl == 0
                    || (!$this->redisExists('exchange:coin_to_usdt_' . $coinName . '_ttl_' . $ttl))
                    || (($this->ttlTime - $this->getTTL('exchange:coin_to_usdt_' . $coinName . '_ttl_' . $ttl)) >= ($ttl * 60))
                ) {
//                    $base_coin_id = CoinType::where('coin_name', 'QC')->pluck('coin_id')->first();
//                    $exchange_coin_id = CoinType::where('coin_name', $coinName)->pluck('coin_id')->first();
                    //交易对redis的键
//                    $key = strtoupper('INSIDE_TEAM_' . $qc_coin_id . '_' . $exchange_coin_id);
//                    $res = $this->redisHgetAll($key);
                    $price = $this->getCoinInsidePrice($coinName);
//                    if (!empty($res)) {
                        if ($ttl)
                        $this->stringSetex('exchange:coin_to_usdt_' . $coinName . '_ttl_' . $ttl,$this->ttlTime,$price);
                        return $price;
//                    } else {
//                        if ($ttl)
//                            $this->stringSetex('exchange:coin_to_usdt_' . $coinName . '_ttl_' . $ttl,$this->ttlTime,$res['current_price']);
//                        return '0.025';
//                    }
                }else{
                    return $this->stringGet('exchange:coin_to_usdt_' . $coinName . '_ttl_' . $ttl);
                }
                break;
            default:
                $coinName = strtolower($coinName);
                if ($ttl == 0
                    || (!$this->redisExists('exchange:coin_to_usdt_' . $coinName . '_ttl_' . $ttl))
                    || (($this->ttlTime - $this->getTTL('exchange:coin_to_usdt_' . $coinName . '_ttl_' . $ttl)) >= ($ttl * 60))
                ) {

                    $res = (new HuobiServer(new LibServer()))->getOneMerged(strtolower($coinName.'usdt'));//dd($res);
                    if ($ttl)
                        $this->stringSetex('exchange:coin_to_usdt_' . $coinName . '_ttl_' . $ttl,$this->ttlTime,$res['current_price'] * $this->getUSDExchangeRate());
                        return $res['current_price'] * $this->getUSDExchangeRate();

                }else{
                    return $this->stringGet('exchange:coin_to_usdt_' . $coinName . '_ttl_' . $ttl);
                }

                break;
        }

    }

    /* 获取所有的币种
      */
    public function getCoinInsidePrice($coinName)
    {
        $tradeTeamList = [];
        $coin = (new CoinType())->getAllCoinType();
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


        $price = 0;
        foreach ($tradeTeamList as $value){
            if ($value['exchange_coin_name'] == 'ICIC' && $value['base_coin_name'] == 'QC' && $value['switch'] == 1){
                $price = $value['current_price'];
            }elseif ($value['exchange_coin_name'] == 'ICIC' && $value['base_coin_name'] == 'USDT' && $value['switch'] == 1){
                $price = $value['current_price'] * $this->getUSDExchangeRate();
            }
        }

        return $price;
    }

    /**
     * 虚拟币对任意法币的汇率
     */
    public function getCoinToCurrency($coinName,$currency,$num = 1,$ttl = 0,$float = 0)
    {
        $currencyExchange = $this->getUSDExchangeRate($currency);

        $coinExchange = $this->getCoinPrice($coinName,$ttl);

        if ($float != 0) $coinExchange = bcmul($coinExchange,1+($float/100),8);
//dd($coinExchange);
        $price = round(bcmul($coinExchange,$currencyExchange,4),2);
        if ($num != 1) return round(bcmul($price,$num,4),2);
        return $price;

    }



}