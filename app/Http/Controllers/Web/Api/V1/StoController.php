<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/9
 * Time: 14:51
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Http\Controllers\Web\BaseController;
use App\Logic\StoLogic;
use App\Traits\Tools;
use Illuminate\Http\Request;

class StoController extends BaseController
{

    use Tools;

    protected $logic;


    public function __construct(StoLogic $stoLogic)
    {
        $this->logic = $stoLogic;

    }


    //获取我的推荐人
    public function getMyPUser(Request $request)
    {
        return $this->logic->getMyPUser($request->user_id);

    }


    //绑定推荐人
    public function bindMyPUser(Request $request)
    {

        if ($vr = $this->verifyField($request->all(),[
            'user_phone' => 'required'
        ])) return $vr;

        return $this->logic->bindMyPUser($request->user_phone);


    }


    //获取所有sto项目
    public function getAllSTOProject(Request $request)
    {

        if ($vr = $this->verifyField($request->all(),[
            'status' => 'required|integer|in:0,1,2'//0，代表预发行，1代表发行中，2代表阶段已完结',
        ])) return $vr;

        return $this->logic->getAllSTOProject($request->status);

    }

    //项目详情
    public function getStoDetail(Request $request)
    {

        if ($vr = $this->verifyField($request->all(),[
            'data_id' => 'required|integer'
        ])) return $vr;

        return $this->logic->getStoDetail($request->data_id);

    }

    //获取正在发行中的阶段详情
    public function getStoStageDetail(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'stage_id' => 'required|integer'
        ])) return $vr;

        return $this->logic->getStoStageDetail($request->stage_id);

    }

    //获取icic可用余额
    public function getICICUsableBalance()
    {
        return $this->logic->getICICUsableBalance();

    }

    //购买
    public function buy(Request $request)
    {

        if ($vr = $this->verifyField($request->all(),[
            'day_id' => 'required|integer',
            'buy_amount' => 'required|integer'//购买的icic数量1000的倍数
        ])) return $vr;


        if ($request->buy_amount <= 0) return api_response()->zidingyi('请输入有效数字');

        return $this->logic->buy($request->day_id,$request->buy_amount);
    }

    //获取会员购买订单
    public function getUserBuyOrders(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'day_id' => 'required|integer'
        ])) return $vr;

        return $this->logic->getUserBuyOrders($request->day_id);
    }


    //获取购买页面的交易记录
    public function getBuyRecord(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'data_id' => 'required|integer'
        ])) return $vr;

        return $this->logic->getBuyRecord($request->data_id);


    }

    //sto资产
    public function getStoWallets()
    {
        return $this->logic->getStoWallets();
    }

    //资产详情
    public function getWalletDetail(Request $request)
    {

        if ($vr = $this->verifyField($request->all(),[
            'wallet_id' => 'required|integer'
        ])) return $vr;

        return $this->logic->getWalletDetail($request->wallet_id);
    }

    //获取提取阶段
    public function getCoinFreeStage(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'coin_id' => 'required|integer'
        ])) return $vr;

        return $this->logic->getCoinFreeStage($request->coin_id);
    }

    //sto提取
    public function free(Request $request)
    {

        if ($vr = $this->verifyField($request->all(),[
            'coin_id' => 'required|integer',
            'free_stage_id' => 'required|integer'
        ])) return $vr;

        return $this->logic->free($request->coin_id,$request->free_stage_id);
    }



}