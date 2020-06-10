<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 16:18
 */

namespace App\Http\Controllers\Web\Api\V1;

use App\Http\Controllers\Web\BaseController;
use App\Http\Response\ApiResponse;
use Illuminate\Http\Request;
use App\Model\C2CTrade;
use App\Model\C2CTradeOrder;
use App\Traits\Tools;
use App\Model\BankCardVerify;
use App\Model\C2CSetting;
use App\Traits\RedisTool;

class C2CTradeController extends BaseController
{
    use Tools,RedisTool,ApiResponse;

    private $c2cTrade;

    public $buyPrice = 1;

    public $sellPrice = 0.9;

    private $bankCardVerify;

    private $coinId;

    private $coinName;

    //单日卖出的额度
    private $sellDayMax;

    public $c2CSetting;

    function __construct(C2CTrade $c2CTrade,BankCardVerify $bankCardVerify,C2CSetting $c2CSetting)
    {
        $this->c2cTrade = $c2CTrade;
        $this->bankCardVerify = $bankCardVerify;
        $c2CSetting = $c2CSetting->getOneRecord();
        $this->c2CSetting = $c2CSetting;
        $this->buyPrice = $c2CSetting['buy_price'];
        $this->sellPrice = $c2CSetting['sell_price'];
        $this->sellDayMax = $c2CSetting['user_sell_day_max'];
        $this->coinId = $c2CSetting['coin_id'];
        $this->coinName = $c2CSetting['coin']['coin_name'];
    }

    public function getC2CMsg()
    {
        return $this->successWithData(['buy_price'=>$this->buyPrice,'sell_price'=>$this->sellPrice,'coin_name'=>$this->coinName]);
    }

    /*下单确认xinxi*/
    public function confirmTrade(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'trade_number'=>'required|integer',
            'trade_type' => 'required|integer'
        ])) return $this->parameterError();

        $price = $request->input('trade_type') == 1 ? $this->buyPrice:$this->sellPrice;//价格

        $amount = bcmul($price,$request->input('trade_number'),2);//总价

        $bankCard = $this->bankCardVerify->getOneBankCard($request->input('user_id'));

        return $this->successWithData(['price'=>$price,'number'=>$request->input('trade_number'),'amount'=>$amount]);
//        response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>,'bank_card'=>$bankCard->toArray()]);

    }



    /*用户挂单*/
    public function saveUserTrade(Request $request)
    {
//        return response()->json(['status_code'=>self::STATUS_CODE_CANNOT_HANDLE,'message'=>'暂未开放']);
        if ($this->verifyField($request->all(),[
            'trade_number'=>'required|integer',
            'trade_type' => 'required|integer'
        ])) return $this->parameterError();

        switch ($request->input('trade_type')){
            case 1:
                return $this->saveBuyTrade($request->input('user_id'),$request->input('trade_number'),$request->input('trade_type'));
                break;
            case 2:
                return $this->saveSellTrade($request->input('user_id'),$request->input('trade_number'),$request->input('trade_type'));
                break;
            default:
                return $this->parameterError();
                break;
        }

        //dd($this->c2cTrade->saveOneRecord(95,100,1));

    }

    /*买入订单*/
    private function saveBuyTrade($userId,$tradeNum,$tradeType)
    {
        $result = $this->c2cTrade->saveOneRecord($userId,$tradeNum,$tradeType,$this->buyPrice,$this->coinId);
        return $this->saveTradeResponse($result);
    }

    /*卖出订单*/
    private function saveSellTrade($userId,$tradeNum,$tradeType)
    {
//        // 当天的零点
//        $dayBegin = strtotime(date('Y-m-d', time()));
//        // 当天的24
//        $dayEnd = $dayBegin + 24 * 60 * 60;
//        if (!$this->redisExists('C2CSELL:MAX:'.$userId)){
            //$this->
//        }



        $num = $this->stringGet('c2c_need_check_num');
        $num = $num == false ? 500 : $num;
        $result = $this->c2cTrade->saveOneRecord($userId,$tradeNum,$tradeType,$this->sellPrice,$this->coinId,$this->stringGet('c2c_need_check_switch'),$num);
        return $this->saveTradeResponse($result);
    }

    /*自定义的返回值*/
    private function saveTradeResponse($code)
    {
        switch ($code){
            case 0:
                return $this->responseByENCode('STATUS_CODE_NOTSUFFICIENT_FUNDS','余额不足');
                break;
            case 1:
                return $this->success();
                break;
            case 2:
                return $this->error();
                break;
            default:
                break;
        }

    }



    /*交易记录*/
    public function orderList(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'handle_status' => 'required|integer'//1进行中2已完成
        ])) return $this->parameterError();

        $result = $this->c2cTrade->getUOrderList($request->input('user_id'),$request->input('handle_status'));

        return $this->successWithData(['order'=>$result]);

    }

    /*交易记录详情*/
    public function orderDetail(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'trade_id' => 'required|integer'
        ])) return $this->parameterError();

        $result = $this->c2cTrade->getUOrderDetail($request->input('user_id'),$request->input('trade_id'));

        return $this->successWithData($result);

    }



}
