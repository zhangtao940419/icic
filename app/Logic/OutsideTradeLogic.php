<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 16:40
 */

namespace App\Logic;


use App\Exceptions\ApiException;
use App\Handlers\ExchangeHelper;
use App\Server\OutsideTrade\OutsideTrade;

class OutsideTradeLogic
{


    private $outsideTrade;
    private $outsideTradeServer;

    public function __construct(OutsideTrade $outsideTradeServer)
    {
        $this->outsideTradeServer = $outsideTradeServer;
    }

    /* 场外市场广告挂单入库
     * @param Request $request
     *    @return \Illuminate\Http\JsonResponse
     */
    public function saveOutsideTrade($data)
    {
        $data['trade_left_number'] = $data['trade_number'];
        if ($data['trade_is_visual'] == 1 && isset($data['trade_limit_time'])) unset($data['trade_limit_time']);

        if ($data['trade_is_visual'] == 0){
            if (is_array($data['trade_limit_time'])) $data['trade_limit_time'] = json_encode($data['trade_limit_time']);
        }
        if (!is_array($data['get_money_type'])){
            $data['get_money_type'] = json_decode($data['get_money_type'],true);
        }
        $data['get_money_type'] = implode(',',$data['get_money_type']);
        if ($data['trade_max_limit_price'] < $data['trade_min_limit_price'])
            throw new ApiException('最大限额不能小于最小限额',3019);

        $data['trade_with_confirm'] = 1;
        return $this->outsideTradeServer->saveOutsideTrade($data);
    }

    public function getCoinPrice($data)
    {
        return $this->outsideTradeServer->getCoinPrice($data);
    }


    /*  撤销广告
     *  @param Request $request
     *  param:
     *  trade_id:订单id
     *  user_id:用户id
     *  @return \Illuminate\Http\JsonResponse
     */

    public function cancelTrade($data){

        return $this->outsideTradeServer->cancelTrade($data);

    }

    //获取所有地区
    public function getAllArea()
    {
        return $this->outsideTradeServer->getAllArea();
    }

    //获取所有币种
    public function getAllCoin()
    {
        return $this->outsideTradeServer->getAllCoin();
    }

    public function getAllCurrency()
    {
        return $this->outsideTradeServer->getAllCurrency();
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
        return $this->outsideTradeServer->getAllTrade($data);
    }

    public function getPersonalMsg($data)
    {
        return $this->outsideTradeServer->getPersonalMsg($data);
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
        return $this->outsideTradeServer->getOneTradeOrder($tradeId);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 获取某个人的广告
     */
    public function getUserTrade($userId)
    {
        return $this->outsideTradeServer->getUserTrade($userId);
    }


    //广告管理
    public function tradeManage($data)
    {
        //status 1进行2已完成3下架
        return $this->outsideTradeServer->tradeManage($data['user_id'],$data['status']);

    }


}