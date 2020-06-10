<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/4
 * Time: 17:08
 */

namespace App\Http\Controllers\Web\Api\V1;

use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use App\Model\CoinTradeOrder;
use App\Server\AdminCoinServer;
use App\Traits\Tools;
use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethServer;
use App\Server\CoinServers\GethTokenServer;
use App\Model\EthToken;
use App\Traits\RedisTool;

class AdminWalletController extends BaseController
{
    use Tools,RedisTool;

    function __construct()
    {
    }


    /*后台审核提币接口*/
    public function checkWithdraw(Request $request,CoinTradeOrder $coinTradeOrder,AdminCoinServer $adminCoinServer)
    {
        if ($this->verifyField($request->all(),[
            'order_id' => 'required|integer',
            'check_status' => 'required|integer'//1通过2拒绝
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
//        if ($this->redisExists('WITHDRAW_'.$request->input('order_id')))
//            return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
//        $this->stringSetex('WITHDRAW_'.$request->input('order_id'),10,$request->input('order_id'));

        $order = $coinTradeOrder->getRecordById($request->input('order_id'));

        switch ($order['coin_name']['coin_name']){
            case 'BTC':
                $result = $adminCoinServer->checkWithdrawCoin((new BitCoinServer()),$order,$request->input('check_status'));
                break;
            case 'ETH':
                $result = $adminCoinServer->checkWithdrawCoin((new gethServer()),$order,$request->input('check_status'));
                break;
            case 'BABC':
                $token = (new EthToken())->getRecordByCoinId($order['coin_id'])->toArray();$order['token'] = $token;
                $result = $adminCoinServer->checkWithdrawCoin((new GethTokenServer($token['token_contract_address'],$token['token_contract_abi'])),$order,$request->input('check_status'));
                break;
            case 'USDT':
                $token = (new EthToken())->getRecordByCoinId($order['coin_id'])->toArray();$order['token'] = $token;
                $result = $adminCoinServer->checkWithdrawCoin((new GethTokenServer($token['token_contract_address'],$token['token_contract_abi'])),$order,$request->input('check_status'));
                break;
            default:
        }
        return $this->checkWithdrawResponse($result);
    }

    /*提币定义返回数据*/
    public function checkWithdrawResponse(int $result)
    {
        switch ($result){
            case 0:
//                return response()->json(['status_code'=>self::STATUS_CODE_NOTSUFFICIENT_FUNDS,'message'=>'余额不足']);
                return response()->json(['status_code'=>self::STATUS_CODE_UNKNOWN_ERROR,'message'=>'未知的错误,请联系客服']);
                break;
            case 1:
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
                break;
            case 2:
                return response()->json(['status_code'=>self::STATUS_CODE_NOTSUFFICIENT_FUNDS,'message'=>'中央钱包余额不足']);
                break;
            case 3:
                return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
                break;
            default:

        }
    }


    /*手动标识提币成功*/
    public function checkSuccess(Request $request,AdminCoinServer $adminCoinServer)
    {
        if ($this->verifyField($request->all(),[
            'order_id' => 'required|integer',
            'check_status' => 'required|integer'//1通过2拒绝
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        $result = $adminCoinServer->updateOrderStatus($request->input('order_id'));

        switch ($result){
            case 1:
                $this->redisDelete('WITHDRAW_'.$request->input('order_id'));
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
                break;
            case 2:
                return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
                break;
            case 3:
                return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'请勿重复操作']);
                break;
        }


    }

}