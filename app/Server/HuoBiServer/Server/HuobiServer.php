<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/17
 * Time: 11:16
 */

namespace App\Server\HuoBiServer\Server;

use App\Model\CoinDes;
use App\Model\CoinType;
use App\Server\HuoBiServer\LibServer\LibServer;
use App\Server\InsideTrade\Dao\CoinTypeDao;
use App\Traits\RedisTool;
use Illuminate\Support\Facades\DB;

class HuobiServer
{
    use RedisTool;

    private $libServer;
    private $coinList;

    private $usdtToCny = 6.4;//汇率

    private $baseCoin = 'usdt';

    private $HLCoinList;

    private $allSymbolKey = 'ALL_SYMBOL';//所有交易对缓存键
    private $hLAllSymbolKey = 'HL_ALL_SYMBOL';

    private $ttl = 86640;//默认缓存秒



    public function __construct(LibServer $libServer)
    {
        $this->libServer = $libServer;
        // 定义参数
        if (!defined('ACCOUNT_ID'))define('ACCOUNT_ID', '59324642'); // your account ID
        if (!defined('ACCESS_KEY'))define('ACCESS_KEY',env('HUOBI_ACCESS_KEY')); // your ACCESS_KEY
        if (!defined('SECRET_KEY'))define('SECRET_KEY', env('HUOBI_SECRET_KEY')); // your SECRET_KEY

        $this->coinList = ['btc','eth','xrp','bch','ltc','etc','eos','trx','doge','dash'];
        $this->HLCoinList = ['btc','eth'];

        $this->usdtToCny = DB::table('coin_exchange_rate')->where('virtual_coin_id',$this->getCoinId('USDT'))->first()->rate;

    }

    //获取单个交易对的行情
    public function getDetailMerged($symbol)
    {if (env('APP_V') == 'test') return [];
        return json_decode(json_encode($this->libServer->get_detail_merged($symbol)),true);
    }

    public function getCoinId($coinName){
        $coin = CoinType::where('coin_name',$coinName)->select(['coin_id'])->first();
        if ($coin) return $coin->coin_id;return 0;
    }

    //获取需要的所有交易对详情1tts2互链
    public function getAllNeedMerged($option = 1)
    {if (env('APP_V') == 'test') return [];
        try {
            $baseCoinId = $this->getCoinId($this->baseCoin);
            $coinList = $option == 1 ? $this->coinList : $this->HLCoinList;
            $result = [];
            foreach ($coinList as $key => $value) {
                $coinDetail = $this->getDetailMerged($value . $this->baseCoin);
                if ($coinDetail['status'] != 'ok' || !$coinDetail) continue;
                $coinDetail['tick']['base_coin_name'] = strtoupper($this->baseCoin);
                $coinDetail['tick']['exchange_coin_name'] = strtoupper($value);
                $coinDetail['tick']['base_coin'] = strtoupper($this->baseCoin);
                $coinDetail['tick']['base_coin_id'] = $baseCoinId;
                $coinDetail['tick']['exchange_coin_id'] = $this->getCoinId($value);
                $coinDetail['tick']['coin_icon'] = CoinDes::where('coin_symbol',$value)->value('coin_icon');
                $coinDetail['tick']['symbol'] = $value . $this->baseCoin;
                $coinDetail['tick']['float_type'] = $coinDetail['tick']['open'] > $coinDetail['tick']['close'] ? '-' : '+';//1+2-
                $coinDetail['tick']['price_float'] = number_format((abs($coinDetail['tick']['open'] - $coinDetail['tick']['close']) / $coinDetail['tick']['open']) * 100, 2);
                $coinDetail['tick']['CNY_price'] = $this->usdtToCny * $coinDetail['tick']['close'];
                $coinDetail['tick']['max_price'] = $coinDetail['tick']['high'];
                $coinDetail['tick']['min_price'] = $coinDetail['tick']['low'];
                $coinDetail['tick']['begin_price'] = $coinDetail['tick']['open'];
                $coinDetail['tick']['current_price'] = $coinDetail['tick']['close'];

                $result[] = $coinDetail['tick'];
            }
            return $result;
        }catch (\Exception $exception){
            return [];
        }
    }

    public function getAllRecords()
    {if (env('APP_V') == 'test') return [];
        if ($this->ifTtl($this->allSymbolKey,4)){
            $result = $this->getAllNeedMerged();
            $this->stringSetex($this->allSymbolKey,$this->ttl,json_encode($result));
            return $result;
        }
            return $this->jsonDecode($this->allSymbolKey);
    }

    public function getHLAllRecords()
    {
        if ($this->ifTtl($this->hLAllSymbolKey,4)){
            $result = array_merge($this->getAllNeedMerged(2),$this->getICICSymbol());
            $this->stringSetex($this->hLAllSymbolKey,$this->ttl,json_encode($result));
            return $result;
        }
        return $this->jsonDecode($this->hLAllSymbolKey);
    }


    //k线
    public function getKLine($symbol,$period = '15min',$size = 30)
    {
        $key = 'KL' . $symbol . $period . $size;
        if ($this->ifTtl($key,4)){
            $result = $this->libServer->get_history_kline($symbol,$period,$size);
            $this->stringSetex($key,$this->ttl,json_encode($result));
            return $result;
        }
//        return json_decode(json_encode($this->libServer->get_history_kline($symbol,$period,$size)),true);
        return $this->jsonDecode($key);
    }

    //单个交易对
    public function getOneMerged($symbol)
    {
        $key = 'SYMBOL_' . $symbol;
        if ($this->ifTtl($key,4)){
            $result = $this->getMerged($symbol);
            $this->stringSetex($key,$this->ttl,json_encode($result));
            return $result;
        }
        return $this->jsonDecode($key);
    }


    private function getMerged($symbol)
    {
        $baseCoinId = $this->getCoinId($this->baseCoin);

        $coinDetail = $this->getDetailMerged($symbol);

        $coinDetail['tick']['base_coin_name'] = strtoupper($this->baseCoin);
        $coinDetail['tick']['base_coin'] = strtoupper($this->baseCoin);
        $coinDetail['tick']['base_coin_id'] = $baseCoinId;
        $coinDetail['tick']['float_type'] = $coinDetail['tick']['open'] > $coinDetail['tick']['close'] ? '-' : '+';//1+2-
        $coinDetail['tick']['price_float'] = number_format((abs($coinDetail['tick']['open'] - $coinDetail['tick']['close'])/$coinDetail['tick']['open'])*100,2);
        $coinDetail['tick']['CNY_price'] = $this->usdtToCny * $coinDetail['tick']['close'];
        $coinDetail['tick']['max_price'] = $coinDetail['tick']['high'];
        $coinDetail['tick']['min_price'] = $coinDetail['tick']['low'];
        $coinDetail['tick']['begin_price'] = $coinDetail['tick']['open'];
        $coinDetail['tick']['current_price'] = $coinDetail['tick']['close'];
        return $coinDetail['tick'];
    }

    //深度
    public function getSymbolDepth($symbol,$type)
    {
        $key = 'DEPTH_' . $symbol . $type;

        if ($this->ifTtl($key,4)){
            $result = $this->libServer->get_market_depth($symbol,$type)->tick;
            $this->stringSetex($key,$this->ttl,json_encode($result));
            return $result;
        }
        return $this->jsonDecode($key);
    }

    //最近成交
    public function getHistoryTrade($symbol,$size = 20)
    {
        if (!$size) $size = 20;

        $key = 'HISTRADE_' . $symbol . $size;

        if ($this->ifTtl($key,4)){
            $result = $this->libServer->get_history_trade($symbol,$size);
            $this->stringSetex($key,$this->ttl,json_encode($result));
            return $result;
        }
        return $this->jsonDecode($key);

    }


    /* 获取所有的币种
     */
    public function getICICSymbol()
    {
        $res = $this->redisHgetAll('INSIDE_TEAM_'.$this->getCoinId('USDT').'_'.$this->getCoinId('ICIC'));

        return [$res];
    }


    //json解码
    private function jsonDecode($key)
    {
        return json_decode($this->stringGet($key),true);
    }

    //判断过期
    private function ifTtl($key,$ttlSeconds)
    {
        $ttl = $this->getTTL($key);
        if (
            $ttl <= 0
            || ($this->ttl - $ttl > $ttlSeconds)
        ){
            if ($this->setKeyLock($key . ':lock',3))
            return 1;//过期,同时只能有一人更新
            return 0;
        }
        return 0;//未过期
    }







}