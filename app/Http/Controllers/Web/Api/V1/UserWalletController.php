<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/7
 * Time: 14:30
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Http\Response\ApiResponse;
use App\Logic\UserWalletLogic;
use App\Model\CoinType;
use App\Model\TransferLockRecord;
use App\Model\WalletDetail;
use App\Traits\Tools;
use Illuminate\Http\Request;

class UserWalletController
{
    use Tools,ApiResponse;

    private $userWalletLogic;
    public function __construct(UserWalletLogic $userWalletLogic)
    {
        $this->userWalletLogic = $userWalletLogic;
    }

    //钱包首页
    public function index(Request $request)
    {
        return $this->userWalletLogic->index($request->user_id);
    }



    /*获取指定账户的余额详情*/
    public function getWalletDetail(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer'
        ])) return $this->parameterError();

        return $this->userWalletLogic->getWalletDetail($request->user_id,$request->wallet_id);
    }


    /*充值信息接口*/
    public function getRechargeMsg(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer'
        ])) return $this->parameterError();
        return $this->userWalletLogic->getRechargeMsg($request->user_id,$request->wallet_id);
    }

    /*提币信息接口*/
    public function getWithdrawMsg(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
        ])) return $this->parameterError();

        return $this->userWalletLogic->getWithdrawMsg($request->user_id,$request->wallet_id);

    }

    /*
     * 用户发起提币
     * */
    public function withdrawCoin(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
            'to_address' => 'required',
            'amount' => 'required|numeric'
        ])) return $this->parameterError();

        return $this->userWalletLogic->withdrawCoin($request->all());
    }


    /*转账 可提转交易*/
    public function transferAccounts(Request $request){
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
            'amount' => 'required|numeric'
        ])) return $this->parameterError();
        return $this->userWalletLogic->transferAccounts($request->user_id,$request->wallet_id,$request->amount);
    }

    //获取划转锁定的订单列表
    public function getTransferLockOrder(Request $request,TransferLockRecord $transferLockRecord)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
        ])) return $this->parameterError();
        $user = current_user();

        $re = $transferLockRecord->getUserLockRecords($user->user_id,$request->wallet_id);

        return api_response()->successWithData(['records' => $re]);


    }

    /*查询usdt余额*/
    public function getUSDTBalance(Request $request)
    {
        return $this->userWalletLogic->getUSDTBalance($request->user_id);
    }

    public function getCoinFlow(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'coin_id' => 'required|integer',
        ])) return $this->parameterError();
        return $this->userWalletLogic->getCoinFlow($request->user_id,$request->coin_id);
    }


    //内部用户划转usdt
    public function transferUSDT(Request $request)
    {


        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
            'amount' => 'required|numeric'
        ])) return $this->parameterError();
        return $this->userWalletLogic->transferUSDT($request->user_id,$request->wallet_id,$request->amount);




    }


    //获取矿池资产
    public function getOrePoolWallet()
    {
        return $this->userWalletLogic->getOrePoolWallet();
    }

    //获取矿池资产详情
    public function getOrePoolWalletDetail(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer'
        ])) return $this->parameterError();
        return $this->userWalletLogic->getOrePoolWalletDetail($request->wallet_id);
    }

    //获取矿池划转注意事项
    public function getOrePoolTransferMsg(Request $request,WalletDetail $walletDetail)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
        ])) return $this->parameterError();
        $wallet = $walletDetail->with([
            'coin' => function ($q){
                $q->select(['coin_id','coin_name','ore_pool_transfer_message','coin_recharge_extra_message']);
            }
        ])->select(['wallet_id','coin_id','wallet_usable_balance','wallet_withdraw_balance'])->where(['wallet_id'=>$request->wallet_id])->first();


        return api_response()->successWithData($wallet);

    }

    //矿池icic划转(场内->矿池或者可提->矿池)
    public function orePoolTransfer(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
            'amount' => 'required|numeric',
            'type' => 'required|integer|in:1,2'//1场内2
        ])) return $this->parameterError();

        return $this->userWalletLogic->orePoolTransfer($request->wallet_id,$request->amount,$request->type);

    }


    //资金划转信息
    public function getTransferMsg(Request $request,CoinType $coinType)
    {
        if ($this->verifyField($request->all(),[
            'coin_id' => 'required|integer',
        ])) return $this->parameterError();

        $coin = $coinType->select(['coin_id','coin_name','coin_transfer_message'])->where(['coin_id'=>$request->coin_id])->first();

        return api_response()->successWithData($coin);

    }


    //获取特定货币的余额
    public function getUserCoinBalance(Request $request,WalletDetail $walletDetail)
    {
        if ($this->verifyField($request->all(),[
            'coin_id' => 'required|integer',
        ])) return $this->parameterError();

        $wallet = $walletDetail->getOneRecord(current_user()->user_id,$request->coin_id,['wallet_usable_balance','wallet_withdraw_balance']);
        if (!$wallet){
            $wallet = ['wallet_usable_balance'=>0,'wallet_withdraw_balance'=>0];
        }
        return api_response()->successWithData($wallet);
    }

}