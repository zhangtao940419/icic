<?php

namespace App\Http\Controllers\Web\Api\V1;


use App\Http\Requests\OutsideSaveTradeRequest;
use App\Http\Response\ApiResponse;
use App\Logic\OutsideTradeLogic;
use  Illuminate\Http\Request;
use  App\Http\Controllers\Web\BaseController;
use  App\Traits\Tools;


class OutsideTradeController extends BaseController
{

    use Tools,ApiResponse;
    private $outsideTrade;
    private $outsideTradeLogic;

    public function __construct(OutsideTradeLogic $outsideTradeLogic)
    {
        $this->outsideTradeLogic = $outsideTradeLogic;
    }

    /* 场外市场挂单入库
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOutsideTrade(OutsideSaveTradeRequest $request)
    {//dd($request->all());
        if ($request->trade_is_visual ==0 && (!$request->trade_limit_time)) return $this->parameterError();
        if ($request->trade_price_type ==2 && (!$request->trade_ideality_price)) return $this->parameterError();
        return $this->outsideTradeLogic->saveOutsideTrade($request->all());
    }

    public function getCoinPrice(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'coin_id' => 'required|integer',
            'currency_id' => 'required|integer',
        ])) return response()->json($result);
        return $this->outsideTradeLogic->getCoinPrice($request->all());

    }


    /*  撤销广告
     *  @param Request $request
     *  param:
     *  trade_id:订单id
     *  user_id:用户id
     *  @return \Illuminate\Http\JsonResponse
     */

    public function cancelTrade(Request $request){

        if ($result = $this->verifyField($request->all(), [
            'trade_id' => 'required|integer',
        ])) return response()->json($result);
        return $this->outsideTradeLogic->cancelTrade($request->all());
    }

    //获取所有地区
    public function getAllArea()
    {
        return $this->outsideTradeLogic->getAllArea();
    }

    //获取所有币种
    public function getAllCoin()
    {
        return $this->outsideTradeLogic->getAllCoin();
    }
    public function getAllCurrency()
    {
        return $this->outsideTradeLogic->getAllCurrency();
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
    public function getAllTrade(Request $request){
        if ($result = $this->verifyField($request->all(), [
            'trade_type' => 'required|integer',//针对用户来说0买入1卖出
            'coin_id' => 'required|integer',
            'location_id' => 'integer',
            'page' => 'integer',//页码
            'page_size' => 'integer',//一页的数量默认15
        ])) return response()->json($result);
        if (!$request->location_id) $request->merge(['location_id'=>1]);
        return $this->outsideTradeLogic->getAllTrade($request->all());
    }

    public function getPersonalMsg(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'coin_id' => 'required|integer',
        ])) return response()->json($result);
        return $this->outsideTradeLogic->getPersonalMsg($request->all());
    }




    /* 获取单个订单的信息
     * @param Request $request
     *  param:
     *  trade_order:订单信息
     *  trade_id:订单id
     *  Carbon::parse(date("Y-M-d",time()))->dayOfWeek 获取当天是星期几0~6 星期日~星期六;
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOneTrade(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'trade_id' => 'required|integer',
        ])) return response()->json($result);
        return $this->outsideTradeLogic->getOneTradeOrder($request->trade_id);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 获取某个人的广告
     */
    public function getUserTrade(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'target_userid' => 'required|integer',
        ])) return response()->json($result);
        return $this->outsideTradeLogic->getUserTrade($request->target_userid);
    }


    //广告管理
    public function tradeManage(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'status' => 'required|integer',
        ])) return response()->json($result);

        //status 1进行2已完成3下架
        return $this->outsideTradeLogic->tradeManage($request->all());

    }







}