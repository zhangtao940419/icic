<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/5
 * Time: 14:19
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Handlers\ExchangeHelper;
use App\Http\Controllers\Web\BaseController;
use App\Model\CoinType;
use App\Model\EthToken;
use App\Model\InsideCountBuy;
use App\Model\InsideCountSell;
use App\Traits\RedisTool;
use Illuminate\Http\ResponseTrait;
use Illuminate\Support\Facades\DB;

class OpenApiController extends BaseController
{

    use RedisTool,ResponseTrait;


    protected  $coinList;

    protected $exchangeHelper;
    public function __construct(CoinType $coinType,ExchangeHelper $exchangeHelper)
    {
        $this->coinList = $coinType;
        $this->exchangeHelper = $exchangeHelper;
    }


    //第三方api服务

    //获取icic汇率
    public function getCnyExchangeRate($coinName = 'ICIC')
    {

        $coin = $this->coinList->getRecordByCoinName($coinName);

        $exchangeToCNYRate = $this->changeTo_Other_Coin($coin['coin_id']);


        $cnyToCoinExchangeRate = bcdiv(1,$exchangeToCNYRate,4);

        return $this->successWithData(['rate' => $cnyToCoinExchangeRate]);

//        dd($cnyToCoinExchangeRate);

    }




    //获取币种实时价格usdt
    public function getCoinPrice($coinName)
    {
        $coin = $this->coinList->getRecordByCoinName($coinName);
        if (!$coin){
            $price = 0;
        }else{
            $price = $this->exchangeHelper->getCoinPrice(strtoupper($coinName));
        }

        return $this->successWithData(['price' => $price]);
    }


    //获取币种价格
    public function getCoinCnyPrice($coinName)
    {
        $coinName = strtoupper($coinName);
        $country_code='CNY';$num = 1;
        try{
            $base_coin_id = CoinType::where('coin_name', 'QC')->pluck('coin_id')->first();
            $exchangeCoin = CoinType::where('coin_name', $coinName)->first();
            if (!$exchangeCoin) {
                $price = 0;
            }else{
                //交易对redis的键
                $key = strtoupper('INSIDE_TEAM_' . $base_coin_id . '_' . $exchangeCoin->coin_id);

                $res = $this->redisHgetAll($key);
                $cny_id = \DB::table('world_currency')->where('currency_code', 'CNY')->pluck('currency_id')->first();
                $qc_cuy_rate = \DB::table('coin_exchange_rate')->where(['virtual_coin_id' => $base_coin_id, 'real_coin_id' => $cny_id])->pluck('rate')->first();
                if(!empty($res)) {
                    //dd($res['current_price'] * $num * $qc_cuy_rate);
                    $price = round($res['current_price'] * $num * $qc_cuy_rate, 6);

                } else {

                    $price = round(\DB::table('coin_exchange_rate')->where(['virtual_coin_id' => $exchangeCoin->coin_id, 'real_coin_id' => $cny_id])->pluck('rate')->first()
                        * $num, 6);
                }
            }


        }catch (\Exception $exception){
            $price = 0;
        }
        return $this->successWithData(['price' => $price]);



    }


    //获取深度图
    public function getDepth($coinName,InsideCountBuy $insideCountBuy,InsideCountSell $insideCountSell)
    {
        $bids = [];
        $sids = [];
        $base_coin = CoinType::where('coin_name', 'QC')->first();
        if (!$base_coin) return api_response()->successWithData(['bids' => $bids,'sids' => $sids]);
        $exchangeCoin = CoinType::where('coin_name', $coinName)->first();

        $bids = $insideCountBuy->where(['base_coin_id'=>$base_coin->coin_id,'exchange_coin_id'=>$exchangeCoin->coin_id])->orderBy('unit_price','asc')->get(['unit_price','trade_total_num']);
        $sids = $insideCountSell->where(['base_coin_id'=>$base_coin->coin_id,'exchange_coin_id'=>$exchangeCoin->coin_id])->orderBy('unit_price','desc')->get(['unit_price','trade_total_num']);
        return api_response()->successWithData(['bids' => $bids,'sids' => $sids]);


    }

    //币价日期折线图
    public function getPriceLine($coinName,$days)
    {

        $exchangeCoin = CoinType::where('coin_name', $coinName)->first();

        $dates = [];
        $dates1 = [];
        $dates3 = [];
        while ($days > 0){
            $dates[] = date("Y-m-d",strtotime("-{$days} day"));
            $dates1[] = date("m-d",strtotime("-{$days} day"));
            $dates3[] = date("Y/m/d",strtotime("-{$days} day")) . '-23:59:00';
            $days--;
        }
//        dd($dates);
        $prices = [];
        foreach ($dates as $date){
            $minT = strtotime($date . ' 23:58:00');
            $maxT = strtotime($date . ' 23:59:00');
            $table = 'time_sharing_1_qc_' . strtolower($exchangeCoin->coin_name);
            $price = DB::table($table)->whereBetween('deal_time',[$minT,$maxT])->first(['current_price']);
            $price = $price ? $price->current_price : 0;
//            $prices[] = rtrim(rtrim($price,'0'),'.');
            $prices[] = floatval($price);

        }
        return api_response()->successWithData(['dates' =>$dates1,'dates1'=>$dates3,'price' => $prices]);

    }



}