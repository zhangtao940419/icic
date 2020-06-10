<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 20:38
 */

namespace App\Http\Controllers\Web\Api\V1;

use App\Http\Controllers\Web\BaseController;
use App\Http\Response\ApiResponse;
use App\Model\BankList;
use App\Model\C2CTrade;
use App\Model\C2CTradeOrder;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use App\Traits\Tools;
use App\Traits\FileTools;
use App\Model\C2CSetting;

class BusinessCenterController extends BaseController
{
    use Tools,FileTools,ApiResponse,RedisTool;

    private $c2cTrade;
    private $c2cTradeOrder;
    private $c2CSetting;

    function __construct(C2CTrade $c2CTrade,C2CTradeOrder $c2CTradeOrder,C2CSetting $c2CSetting)
    {
        $this->c2cTrade = $c2CTrade;
        $this->c2cTradeOrder = $c2CTradeOrder;
        $c2CSetting = $c2CSetting->getOneRecord();
        $this->c2CSetting = $c2CSetting;
    }


    /*商家中心--买单/卖单列表*/
    public function getTradeList(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'trade_type' => 'required|integer'
        ])) return $this->parameterError();

        return $this->successWithData(['trade_list'=>$this->c2cTrade->getTradeList($request->input('trade_type'))]);

    }

    /*接单*/
    public function receiptTrade(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'trade_id' => 'required|integer',
            'bank_id' => 'integer',
            'card_no' => 'string',
            'name' => 'string'
        ])) return $this->parameterError();

        if ($request->card_no && !$this->isBankCard($request->card_no))
            return $this->responseByENCode('STATUS_CODE_BANKCARD_NOT_LEGAL','银行卡不合法');
        if ($request->bank_id && !BankList::find($request->bank_id))
            return $this->responseByENCode('STATUS_CODE_BANKCARD_NOT_LEGAL','银行卡不合法');

        if (! $this->setKeyLock('C2C:TRADE_' . $request->trade_id,5))
            return $this->responseByENCode('STATUS_CODE_C2CTRADE_UNUSABLE','订单被锁定');

        $result = $this->c2cTrade->receiptTrade($request->input('trade_id'),$request->input('user_id'),$this->c2CSetting,$request->input('bank_id'),$request->input('card_no'),$request->input('name'));

        switch ($result){
            case 0:
                return $this->responseByENCode('STATUS_CODE_C2CTRADE_UNUSABLE','订单不可用');
                break;
            case 1:
                return $this->responseByENCode('STATUS_CODE_NOTSUFFICIENT_FUNDS','余额不足');
                break;
            case 2:
                return $this->error();
                break;
            case 3:
                return $this->responseByENCode('STATUS_CODE_CANNOT_ACCEPT_SELF','不能接自己的单');
                break;
            default:
                return $this->successWithData(['order'=>$result]);
                break;
        }

    }


    /*1.确认收款订单完成*/
    public function confirmBuyOrder(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'order_id' => 'required|integer'
        ])) return $this->parameterError();

        switch ($this->c2cTradeOrder->confirmBuyOrder($request->input('user_id'),$request->input('order_id'),$this->c2CSetting)){
            case 0:
                return $this->error();
                break;
            case 1:
                return $this->success();
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_C2CORDER_LATER_HANDLE','下单后'.$this->c2CSetting['business_buy_order_confirm_time'].'分钟才可确认');
                break;
        }


    }

    /*2.确认打款订单完成*/
    public function confirmSellOrder(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'order_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'bank_card_no' => 'required',
            'transfer_img' => 'required|string'
        ])) return $this->parameterError();
//dd($request->all());
        switch ($this->c2cTradeOrder->confirmSellOrder($request->all(),$this->c2CSetting)){
            case 0:
                return $this->error();
                break;
            case 1:
                return $this->success();
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_C2CORDER_LATER_HANDLE','下单后'.$this->c2CSetting['business_sell_order_confirm_time'].'分钟才可确认');
                break;
        }

    }

    /*商家交易记录*/
    public function orderList(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'handle_status' => 'required|integer',//处理状态1处理中2已完成
        ])) return $this->parameterError();

        $result = $this->c2cTradeOrder->getBOrderList($request->input('user_id'),$request->input('handle_status'));

        return $this->successWithData(['order'=>$result]);
    }

    /*交易详情*/
    public function orderDetail(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'order_id' => 'required|integer',
        ])) return $this->parameterError();

        $result = $this->c2cTradeOrder->getBOrder($request->input('user_id'),$request->input('order_id'));

        if (! $result) return $this->error();

        return $this->successWithData($result);
    }

    /*上传转账凭证接口*/
    public function uploadTransferImg(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'file' => 'required|mimes:jpeg,png',
        ])) return $this->parameterError();
        try{
            $filePath = $this->putImage($request->file('file'),date('Y-m',time()),'c2cTransfer');
            if ($filePath == 0) return $this->error();
            if ($filePath == 2) return $this->responseByENCode('STATUS_CODE_IMAGE_TOOLARGE','图片过大');
                return $this->successWithData(['path'=>'/app/c2c_transferimg/'.$filePath]);

        }catch (\Exception $e){
            return $this->error();
        }

    }

    /*后台审核接口*/
    public function checkTransferImg(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'order_id' => 'required|integer',//
            'check_status' => 'required|integer'//1代表通过审核,2代表拒绝
        ])) return $this->parameterError();

        if ($request->input('check_status') != 1) return $this->error();

        switch ($this->c2cTradeOrder->confirmTransfer($request->input('order_id'))){
            case 0:
                return $this->error();
                break;
            case 1:
                return $this->success();
                break;
            case 2:

                break;
        }

    }




}