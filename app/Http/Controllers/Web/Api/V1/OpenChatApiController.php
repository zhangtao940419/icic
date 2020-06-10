<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/28
 * Time: 14:57
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Model\CoinTradeOrder;
use App\Model\CoinType;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use App\Traits\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpenChatApiController extends Controller
{
    use Tools;

    protected $coinType;

    protected $walletDetail;

    protected $auth = [
        'chat' => 'lt_tts_api_147258369'


    ];


    public function __construct(CoinType $coinType,WalletDetail $walletDetail,Request $request)
    {
        $this->coinType = $coinType;
        $this->walletDetail = $walletDetail;

        //权限验证
        $u = $request->u;
        $p = $request->p;

        if (!isset($this->auth[$u]) || $this->auth[$u] !== $p){
            throw new ApiException('perror','9999');
        }
    }


    //查询地址是否是tts地址
    public function checkIsAddress($address)
    {
        try{
            $re = $this->walletDetail->isAddress($address);

            if ($re){
                return api_response()->success();
            }

            return api_response()->error();
        }catch (\Exception $exception){
            return api_response()->error();
        }


    }


    //chat转账
    public function chatRecharge(Request $request,CoinTradeOrder $coinTradeOrder)
    {

        if ($vr = $this->verifyField($request->all(),[
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'amount' => 'required|numeric',
            'coin_name' => 'required|string'
        ])) return $vr;

        try{
            DB::beginTransaction();

            $coin = $this->coinType->getRecordByCoinName($request->coin_name);
            $userId = $this->walletDetail->getUserIdByAddress($request->to_address);

            $wallet = $this->walletDetail->getOneRecord($userId,$coin->coin_id);

            $wh = $wallet->addUsableBalance($coin->coin_id,$userId,$request->amount);

            $order = $coinTradeOrder->saveOneRecord($userId,$coin->coin_id,'',$request->from_address,$request->to_address,$request->amount,2,0,5);

            $flow = (new WalletFlow())->insertOne($userId,$wallet->wallet_id,$wallet->coin_id,$request->amount,2,1,'chat转入',1);

            if ($wh && $order){
                DB::commit();
                return api_response()->success();
            }


        }catch (\Exception $exception){

            DB::rollBack();
            return api_response()->error();
        }



    }

    //plc转账
    public function plcRecharge(Request $request,CoinTradeOrder $coinTradeOrder)
    {

        if ($vr = $this->verifyField($request->all(),[
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'amount' => 'required|numeric',
            'coin_name' => 'required|string'
        ])) return $vr;

        try{
            DB::beginTransaction();

            $coin = $this->coinType->getRecordByCoinName($request->coin_name);
            $userId = $this->walletDetail->getUserIdByAddress($request->to_address);

            $wallet = $this->walletDetail->getOneRecord($userId,$coin->coin_id);

            $wh = $wallet->addUsableBalance($coin->coin_id,$userId,$request->amount);

            $order = $coinTradeOrder->saveOneRecord($userId,$coin->coin_id,'',$request->from_address,$request->to_address,$request->amount,2,0,6);

            $flow = (new WalletFlow())->insertOne($userId,$wallet->wallet_id,$wallet->coin_id,$request->amount,20,1,'plc转入',1);

            if ($wh && $order){
                DB::commit();
                return api_response()->success();
            }


        }catch (\Exception $exception){

            DB::rollBack();
            return api_response()->error();
        }



    }









}