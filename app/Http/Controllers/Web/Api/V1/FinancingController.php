<?php

namespace App\Http\Controllers\Web\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Tools;
use App\Logic\InvestmentLogic;
use App\Http\Response\ApiResponse;

class FinancingController extends Controller
{
     use Tools,ApiResponse;

     private $investmentLogic=null;

     public function __construct(InvestmentLogic $investmentLogic)
     {
          $this->investmentLogic  = $investmentLogic;
     }

    //获取用户理财统计数据
      public function getFinancCount(Request $request){
          if ($result = $this->verifyField($request->all(), [
              'user_id' => 'required|integer',
              'coin_id' => 'required|integer',
              'invest_id' => 'required|integer',
          ])) return response()->json($result);

          return $this->responseByENCode('STATUS_CODE_SUCCESS','用户获取数据成功',$this->investmentLogic->getUserInvestCount($request->all()['user_id'],$request->all()['coin_id'],$request->all()['invest_id']));
         }

      /*用户理财下单
        @param
           user_id : 用户id;
           invest_id:理财的类型id,定期或者活期；
           invest_type_id: 理财的套餐id
       * */
       public function setFinancOrder(Request $request){
           if ($result = $this->verifyField($request->all(), [
               'user_id' => 'required|integer',
               'invest_id' => 'required|integer',
               'invest_type_id' => 'required|integer',
               'invest_money' => 'required|numeric',
           ])) return response()->json($result);
           return $this->responseByENCode('STATUS_CODE_SERVERERROR','服务器出点小问题了');
           switch ( $this->investmentLogic->saveUserFinancOrder($request->all())){
               case 1:
                   return $this->responseByENCode('STATUS_INVEST_BUY_SUCCESS','用户购买成功');
                   break;
               case -1:
                   return $this->responseByENCode('STATUS_INVEST_BUY_ERROR','用户购买失败');
                   break;
               case -2:
                   return $this->responseByENCode('STATUS_CODE_BALANCE_UNENOUGH','账户余额不足');
                   break;
               default:
                   return $this->responseByENCode('STATUS_CODE_SERVERERROR','服务器出点小问题了');
                   break;
           }

          }

      /* 用户未到期提取理财订单
       *
       *
       */
       public function cancelFinancOrder(Request $request){
           if ($result = $this->verifyField($request->all(), [
               'user_id' => 'required|integer',
               'invest_order' => 'required|string',
           ])) return response()->json($result);
           return $this->responseByENCode('STATUS_CODE_SERVERERROR','服务器出点小问题了');
           switch ($this->investmentLogic->cancelInvestOrder($request->all()['user_id'],$request->all()['invest_order'])){
               case 1:
                   return $this->responseByENCode('STATUS_INVEST_BUY_SUCCESS','用户提取成功');
                   break;
               case -1:
                   return $this->responseByENCode('STATUS_INVEST_ORDER_NULL','不存在相关订单');
                   break;
               case -2:
                   return $this->responseByENCode('STATUS_INVEST_ORDER_STATUS_ERROR','订单状态不符合撤销要求');
                   break;
               case -3:
                   return $this->responseByENCode('STATUS_INVEST_ORDER_CANCEL_FAIL','订单撤销失败');
                   break;
               default:
                   return $this->responseByENCode('STATUS_CODE_SERVERERROR','服务器出点小问题了');
                   break;
           }


       }

      //用户获取下单记录
       public function getHistoryOrder(Request $request){

       if ($result = $this->verifyField($request->all(), [
               'user_id' => 'required|integer',
           ])) return response()->json($result);

       return $this->responseByENCode('STATUS_CODE_SUCCESS','用户获取数据成功',  $this->investmentLogic->getHistoryOrder($request->all()['user_id']));

       }


       /*  获取投资理财的类型
        *
        *
        */
       public function getInvestType(Request $request){

           if ($result = $this->verifyField($request->all(), [
               'coin_id' => 'required|integer',
           ])) return response()->json($result);

           return $this->responseByENCode('STATUS_CODE_SUCCESS','用户获取数据成功', $this->investmentLogic->getInvestType($request->all()['coin_id']));

       }

       /* 获取可以用来投资理财的虚拟币
        *
        *
        */
       public function getInvestCoin(){


           return $this->responseByENCode('STATUS_CODE_SUCCESS','用户获取数据成功',$this->investmentLogic->getInvestCoin());

       }


       /* 用户到期后提取本金和利息
        *
        *
        */
       public function getFinancMoney(Request $request){

           if ($result = $this->verifyField($request->all(), [
               'user_id' => 'required|integer',
               'invest_order' => 'required|string',
           ])) return response()->json($result);
           return $this->responseByENCode('STATUS_CODE_SERVERERROR','服务器出点小问题了');
           switch ($this->investmentLogic->getUserInvestMoney($request->all()['user_id'],$request->all()['invest_order'])){
               case 1:
                   return $this->responseByENCode('STATUS_INVEST_BUY_SUCCESS','用户提取成功');
                   break;
               case -1:
                   return $this->responseByENCode('STATUS_INVEST_ORDER_NULL','不存在相关订单');
                   break;
               case -2:
                   return $this->responseByENCode('STATUS_INVEST_ORDER_STATUS_ERROR','订单状态不符合撤销要求');
                   break;
               case -3:
                   return $this->responseByENCode('STATUS_INVEST_ORDER_CANCEL_FAIL','订单撤销失败');
                   break;
               default:
                   return $this->responseByENCode('STATUS_CODE_SERVERERROR','服务器出点小问题了');
                   break;
           }
       }


     /*  获取用户理财的余额
      *  @param
      *    $user_id
      *
      */
    public function getInvestCoinBalance(Request $request){

    if ($result = $this->verifyField($request->all(), [
            'user_id' => 'required|integer',
            'coin_id' => 'required|integer',
        ])) return response()->json($result);
        $balance['balance']=$this->investmentLogic->getInvestCoinBalance($request->all()['coin_id'],$request->all()['user_id']);
        if(!$balance['balance'])
            $balance['balance']=0.0;
        return $this->responseByENCode('STATUS_CODE_SUCCESS','用户获取数据成功',$balance);

    }

    /* 获取虚拟货币理财的类型
     *
     *
     */
    public function getInvestSetMeal(Request $request){

        if ($result = $this->verifyField($request->all(), [
            'coin_id' => 'required|integer',
            'invest_id' => 'required|integer',
        ])) return response()->json($result);

        return $this->responseByENCode('STATUS_CODE_SUCCESS','用户获取数据成功',$this->investmentLogic->getInvestSetMeal($request->all()['coin_id'],$request->all()['invest_id']));


    }




}
