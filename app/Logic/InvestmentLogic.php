<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/15
 * Time: 10:50
 */

namespace App\Logic;


use App\Server\Investment\InvestmentServer;


class InvestmentLogic
{

    //理财服务组件
    private $investment = null;



     public function __construct(InvestmentServer $investment)
     {

         $this->investment = $investment;
     }


    /*保存用户下的理财单
     *   return
     *         -2 : 用户账户余额不足；
     *         -1:  用户购买理财产品失败；
     *         1 ：用户购买理财产品成功；
     * */
    public function saveUserFinancOrder($userParam){

        return $this->investment->saveUserFinancOrder($userParam);

    }

    /* 用户获取个人理财的统计数据
     *  @param
     *     $user_id
     *     $coin_id
     *     $invest_id
     */
    public function getUserInvestCount($user_id,$coin_id,$invest_id){

       return $this->investment->getUserInvestCount($user_id,$coin_id,$invest_id);

    }


    /*
     * 获取历史记录
     *
     *
     */
    public function getHistoryOrder($user_id){

     return  $this->investment->getUserHistoryOrder($user_id);

    }


    /*用户未到理财时间提取存款
     *
     *  $user_id
     *  $order
     */
    public function cancelInvestOrder($user_id,$order){

     return  $this->investment->cancelUserInvestOrder($user_id,$order);

    }


    /* 获取投资类型
     *  $param :
     *      $coin_id
     */
    public function getInvestType($coin_id){

         return   $this->investment->getInvestType($coin_id);


    }

    /* 获取可以用作投资的虚拟币
     *
     *
     */
    public function getInvestCoin(){


        return   $this->investment->getInvestCoin();

    }


    /* 用户获取到期后可以提取的本金和利息
     *  param
     *   $user_id
     *   $invest_order
     *
     */
    public  function getUserInvestMoney($user_id,$invest_order){


       return  $this->investment->getUserInvestMoney($user_id,$invest_order);


    }


    /* 获取用户的余额
     *   $user_id
     *   $coin_id
     */
    public function getInvestCoinBalance($coin_id,$user_id){

     $balance = $this->investment->getInvestCoinBalance($coin_id,$user_id);

     return  $balance['wallet_withdraw_balance'];

    }


    /* 获取投资套餐的余额
     *
     *
     */

    public function getInvestSetMeal($coin_id,$invest_id){


              return $this->investment->getInvestSetMeal($coin_id,$invest_id);


    }



}