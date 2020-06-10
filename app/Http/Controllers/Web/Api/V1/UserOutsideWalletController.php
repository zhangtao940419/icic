<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10
 * Time: 12:04
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Http\Controllers\Web\BaseController;
use App\Logic\UserOutsideWalletLogic;
use App\Traits\Tools;
use Illuminate\Http\Request;

class UserOutsideWalletController extends BaseController
{
use Tools;
    protected $logic;

    function __construct(UserOutsideWalletLogic $userOutsideWalletLogic)
    {

        $this->logic = $userOutsideWalletLogic;

    }

    //首页
    public function index(Request $request)
    {
        return $this->logic->index($request->user_id);
    }


    public function getBalance(Request $request)
    {

        if ($result = $this->verifyField($request->all(), [
            'coin_id' => 'required|integer',
        ])) return $result;

        return $this->logic->getBalance($request->all());

    }


    /*获取指定账户的余额详情*/
    public function getWalletDetail(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'wallet_id' => 'required|integer',
        ])) return $result;
        return $this->logic->getWalletDetail($request->user_id,$request->wallet_id);
    }


    /*充值信息接口*/
    public function getRechargeMsg(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'wallet_id' => 'required|integer',
        ])) return $result;
        return $this->logic->getRechargeMsg($request->user_id,$request->wallet_id);
    }

    /*提币信息接口*/
    public function getWithdrawMsg(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'wallet_id' => 'required|integer',
        ])) return $result;
        return $this->logic->getWithdrawMsg($request->user_id,$request->wallet_id);
    }


//提币
    public function withdraw(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'wallet_id' => 'required|integer',
            'to_address' => 'required|string',
            'amount' => 'required|numeric'
        ])) return $result;
        return $this->logic->withdraw($request->user_id,$request->wallet_id,$request->to_address,$request->amount);
    }




}