<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14
 * Time: 17:28
 */

namespace App\Logic;

use App\Server\InsideTrade\InsideTradeBuyServer;
use App\Server\InsideTrade\InsideTradeSellServer;

class InsideTradeLogic
{

    private $insideTradeBuyServer = null;
    private $insideTradeSellServer = null;

    function  __construct(InsideTradeBuyServer $insideTradeBuyServer,InsideTradeSellServer  $insideTradeSellServer)
    {
        $this->insideTradeBuyServer =$insideTradeBuyServer;
        $this->insideTradeSellServer =$insideTradeSellServer;

    }

    //订单入库
    public function saveInsideOrder($inSideParam){

       switch ($inSideParam['trade_type']){
             case 0;
                 return $this->insideTradeBuyServer->saveInsideBuyOrder($inSideParam);
                 break;
             case 1:
                 return $this->insideTradeSellServer->saveInsideSellOrder($inSideParam);
                 break;
               default;
               break;
       }
    }

    //取消订单
    public function cancelInsideOrder($inSideParam){

        switch ($inSideParam['trade_type']){
            case 0;
                return $this->insideTradeBuyServer->cancelInsideBuyOrder($inSideParam);
                break;
            case 1:
                return $this->insideTradeSellServer->cancelInsideSellOrder($inSideParam);
                break;
            default;
                break;
        }
    }

    /* 获取场内交易历史委托
     * @param
     * Request $request
     *
     * return json
     */
    public function getInsideHistoryTrade($inSideParam)
    {
        switch ($inSideParam['trade_type']){
            case 0;
                return $this->insideTradeBuyServer->getInsideHistoryBuy($inSideParam);
                break;
            case 1:
                return $this->insideTradeSellServer->getInsideHistorySell($inSideParam);
                break;
            default;
                break;
        }
    }

    /* 获取场内订单匹配记录
     * @param
     * Request $request
     *
     * return json
     */
    public function getCarefulInsideHistoryTrade($inSideParam)
    {
        switch ($inSideParam['trade_type']){
            case 0;
                return $this->insideTradeBuyServer->getBuyMatchRecord($inSideParam);
                break;
            case 1:
                return $this->insideTradeSellServer->getSellMatchRecord($inSideParam);
                break;
            default;
                break;
        }
    }

    /* 获取场内最近委托
     * @param
     * Request $request
     *
     * return json
     */
    public function getManyTrade($inSideParam)
    {
        switch ($inSideParam['trade_type']){
            case 0;
                return $this->insideTradeBuyServer->getManyBuy($inSideParam);
                break;
            case 1:
                return $this->insideTradeSellServer->getManySell($inSideParam);
                break;
            default;
                break;
        }
    }

}
