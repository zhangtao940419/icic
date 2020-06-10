<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/20
 * Time: 9:32
 */

namespace App\Http\Controllers\Web\Api\V1;

use App\Http\Controllers\Web\BaseController;
use App\Http\Response\ApiResponse;
use App\Logic\OutsideTradeLogic;
use App\Logic\OutsideTransactionLogic;
use App\Model\OutsideTrade;
use App\Model\OutsideTradeOrder;
use App\Model\User;
use App\Model\WalletDetail;
use App\Model\UserTradeDatum;
use App\Server\OutsideTrade\Dao\OutsideTradeOrderDao;
use App\Traits\RedisTool;
use App\Traits\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutsideTransactionController extends BaseController
{
    use RedisTool,Tools,ApiResponse;


    private $outsideTransactionLogic;


    function __construct(OutsideTransactionLogic $outsideTransactionLogic)
    {

        $this->outsideTransactionLogic = $outsideTransactionLogic;
    }


    //接单
    public function acceptTtrade(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'trade_id' => 'required|integer',
            'order_coin_num' => 'required|numeric',
            'order_total_money' => 'required|numeric'
        ])) return $this->parameterError();

        return $this->outsideTransactionLogic->acceptTrade($request->all());
    }

    //买家取消订单
    public function buyCancelOrder(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'order_id' => 'required|integer',
        ])) return $this->parameterError();

        return $this->outsideTransactionLogic->buyCancelOrder($request->all());
    }


    //买家标记付款并上传凭证
    public function confirmBuyOrder(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'order_id' => 'required|integer',
            'image' => 'required|file'
        ])) return $this->parameterError();
        return $this->outsideTransactionLogic->confirmBuyOrder($request->all());

    }

    //卖家确认收款,交易完成
    public function sellerConfirmOrder(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'order_id' => 'required|integer',
        ])) return $this->parameterError();
        return $this->outsideTransactionLogic->sellerConfirmOrder($request->all());
    }




    /*信任与取消*/
    public function trust(Request $request,UserTradeDatum $userTradeDatum)
    {
        if ($this->verifyField($request->all(),[
            'trust_userid' => 'required|integer',
        ])) return $this->parameterError();
        $trustList = $this->sMembers('ouside:TRUST_LIST_'.$request->input('user_id'));
        if (!in_array($request->trust_userid,$trustList)){//信任
            $userTradeDatum->addTrust($request->input('trust_userid'));
                    $this->sAdd('ouside:TRUST_LIST_'.$request->input('user_id'),$request->input('trust_userid'));//信任的人的集合
                    $this->sAdd('ouside:TRUSTED_LIST_'.$request->input('trust_userid'),$request->input('user_id'));//被别人信任的集合

        }else{//取消
            $userTradeDatum->removeTrust($request->input('trust_userid'));
                $this->sRem('ouside:TRUST_LIST_'.$request->input('user_id'),$request->input('trust_userid'));
                $this->sRem('ouside:TRUSTED_LIST_'.$request->input('trust_userid'),$request->input('user_id'));
        }

        return $this->success();
    }


    /*屏蔽用户*/
    public function defriend(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'target_userid' => 'required|integer',
        ])) return $this->parameterError();
        $defriendList = $this->sMembers('ouside:DEFRIEND_LIST_'.$request->input('user_id'));
        if (!in_array($request->target_userid,$defriendList)){
            $this->sAdd('ouside:DEFRIEND_LIST_'.$request->input('user_id'),$request->input('target_userid'));
        }else{
            $this->sRem('ouside:DEFRIEND_LIST_'.$request->input('user_id'),$request->input('target_userid'));
        }

        return $this->success();
    }


    /*查询对某个用户的信任和屏蔽状态*/
    public function checkTrustStatus(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'check_userid' => 'required|integer',
        ])) return $this->parameterError();
        $trustStatus = $this->sIsMember('ouside:TRUST_LIST_'.$request->input('user_id'),$request->input('check_userid'));
        $defriendStatus = $this->sIsMember('ouside:DEFRIEND_LIST_'.$request->input('user_id'),$request->input('check_userid'));
        return $this->successWithData(['trust_status'=>$trustStatus,'defriend_status'=>$defriendStatus]);
    }



    /*信任管理--1信任我的人/2我信任的人/3屏蔽我的人*/
    public function getAboutUserMsg(Request $request,User $user,OutsideTradeOrderDao $outsideTradeOrderDao)
    {
        if ($this->verifyField($request->all(), [
            'type' => 'required|integer',
        ])) return $this->parameterError();

        switch ($request->input('type')){
            case 1:
                $result = $this->sMembers('ouside:TRUST_LIST_' . $request->input('user_id'));//我信任的人
            break;
            case 2:
                $result = $this->sMembers('ouside:TRUSTED_LIST_' . $request->input('user_id'));//信任我的人
                break;
            case 3:
                $result = $this->sMembers('ouside:DEFRIEND_LIST_' . $request->input('user_id'));//我屏蔽的人
            break;
        }

        if (! $result) return response()->json(['status_code' => self::STATUS_CODE_SUCCESS, 'message' => '查询成功', 'data' => []]);

        $result = $user->with(['datum'])->select(['user_id','user_name','user_phone','user_headimg','outside_grade'])->whereIn('user_id',$result)->get()->toArray();

        foreach ($result as &$value){
            $value['ousideOrderWithMe'] = $outsideTradeOrderDao->where('order_status',3)->where(['user_id'=>$request->user_id,'trade_user_id'=>$value['user_id']])->orWhere(['trade_user_id'=>$request->user_id,'user_id'=>$value['user_id']])->count();
        }
//        dd($result);
        return response()->json(['status_code' => self::STATUS_CODE_SUCCESS, 'message' => '查询成功', 'data' => $result]);
    }


    //wohetadejiaoyi
    public function getOrderWithOther(Request $request)
    {
        if ($this->verifyField($request->all(), [
            'target_userid' => 'required|integer',
        ])) return $this->parameterError();

        return $this->outsideTransactionLogic->getOrderWithOther($request->all());

    }
    //订单管理
    public function getMyOrder(Request $request)
    {
        if ($this->verifyField($request->all(), [
            'status' => 'required|integer',//1进行中2已完成3已取消
        ])) return $this->parameterError();
        return $this->outsideTransactionLogic->getMyOrder($request->all());

    }

//订单详情
    public function getOrderDetail(Request $request)
    {
        if ($this->verifyField($request->all(), [
            'order_id' => 'required|integer',//1进行中2已完成3已取消
        ])) return $this->parameterError();
        return $this->outsideTransactionLogic->getOrderDetail($request->all());
    }

//pingjia
    public function comment(Request $request)
    {
        if ($this->verifyField($request->all(), [
            'order_id' => 'required|integer',//1进行中2已完成3已取消
            'comment' => 'required|integer'//1好2中3差
        ])) return $this->parameterError();
        if (!in_array($request->comment,[1,2,3])) return $this->parameterError();
        return $this->outsideTransactionLogic->comment($request->all());

    }


    public function getTradeOrderList(Request $request)
    {

        if ($this->verifyField($request->all(), [
            'trade_id' => 'required|integer',//1进行中2已完成3已取消
        ])) return $this->parameterError();
        return $this->outsideTransactionLogic->getTradeOrderList($request->all());

    }


}