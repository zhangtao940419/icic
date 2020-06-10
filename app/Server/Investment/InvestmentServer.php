<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/15
 * Time: 10:50
 */

namespace App\Server\Investment;

use App\Server\Investment\Dao\InvestmentRuleDao;
use App\Server\Investment\Dao\InvestmentTypeDao;
use App\Server\Investment\Dao\UserWalletDao;
use App\Server\Investment\Dao\UserInvestmentDao;
use App\Server\Investment\Dao\UserinvestmentCountDao;
use Illuminate\Support\Facades\DB;
use Psy\Exception\ErrorException;
use  App\Server\Investment\Dao\CoinTypeDao;


class InvestmentServer
{
    //理财套餐实例
    private $investmentRuleDao=null;

    //理财类型实例
    private $investmentTypeDao=null;

    //用户钱包实例
    private $userWalletDao=null;

    //用户理财下单记录
    private $userInvest=null;

    //用户理财统计实例
    private $userCount = null;

    //用户投资的返利比率
    private $compute_rate =null;

    //货币类型
    private $coinTypeDao =null;

    public function __construct(
        InvestmentRuleDao $investmentRuleDao ,
        InvestmentTypeDao $investmentTypeDao,
        UserWalletDao $userWalletDao,
        UserInvestmentDao $userInvest,
        UserinvestmentCountDao $userCount,
        CoinTypeDao $coinTypeDao
    )
    {
        $this->investmentRuleDao = $investmentRuleDao;

        $this->investmentTypeDao = $investmentTypeDao;

        $this->userWalletDao = $userWalletDao;

        $this->userInvest = $userInvest;

        $this->userCount = $userCount;

        $this->coinTypeDao = $coinTypeDao;

    }


    /*保存用户下的理财单
     *   return
     *         -2 : 用户账户余额不足；
     *         -1:  用户购买理财产品失败；
     *         1 ：用户购买理财产品成功；
     * */
    public function saveUserFinancOrder($userParam){
        //1,先查询用户是否有足够的余额
        if(!$investType = $this->investmentTypeDao->getRecordByCondition(['invest_id'=>$userParam['invest_id']])->toArray())
            throw new ErrorException('没有查到相关的数据');

        if(!$userBalance = $this->userWalletDao->getUserCoinBalance($investType[0]['coin_id'],$userParam['user_id']))
            throw new ErrorException('没有查到相关的账户余额');
        if($userBalance['wallet_withdraw_balance']<$userParam['invest_money'])
            return -2;
        $investTypeRule=$this->investmentRuleDao->getRecordByCondition(['type_id'=>$userParam['invest_type_id'],'is_usable'=>1])->toArray();
        $userinsertData['invest_id'] =$investTypeRule[0]['invest_id'];
        $userinsertData['user_id'] =$userParam['user_id'];
        $userinsertData['coin_id'] =$investType[0]['coin_id'];
        $userinsertData['invest_order'] =date('YmdHis'.$userParam['user_id'],time());
        $userinsertData['invest_pay_time'] =time();
        $userinsertData['invest_time'] =$investTypeRule[0]['invest_time'];
        $userinsertData['rate_of_return_set'] =$investTypeRule[0]['rate_of_return_set'];
        $userinsertData['invest_money'] =$userParam['invest_money'];
        //2，数据入库
        DB::beginTransaction();
        $userCountData['coin_id'] = $investType[0]['coin_id'];
        $userCountData['user_id'] = $userParam['user_id'];
        $userCountData['investment_total_money'] = $userParam['invest_money'];
        $userCountData['estimated_revenue'] =$this->computeEarnings($userParam['invest_money'],$investTypeRule[0]['rate_of_return_set']);
        if(!$this->userCount->getRecordByCondition(['coin_id'=>$investType[0]['coin_id'],'user_id'=>$userParam['user_id']])->count())
        {
            $result =  $this->userCount->saveUserInvest($userCountData);
        }else{
            $userOldCount = $this->userCount->getRecordByCondition(['coin_id'=>$investType[0]['coin_id'],'user_id'=>$userParam['user_id']])->toArray();
            $userAddData['investment_total_money'] = $userOldCount[0]['investment_total_money'] + $userParam['invest_money'];
            $userAddData['estimated_revenue'] = $userOldCount[0]['estimated_revenue']+$this->computeEarnings($userParam['invest_money'],$investTypeRule[0]['rate_of_return_set']);
            $result =  $this->userCount->updateUserInvest(['coin_id'=>$investType[0]['coin_id'],'user_id'=>$userParam['user_id']],$userAddData);
        }
        if($this->userWalletDao->reduceUserWithdrawBalance($investType[0]['coin_id'],$userParam['user_id'],$userParam['invest_money'])
            &&    $this->userInvest->saveUserInvest($userinsertData)
            &&    $result
        ){
            DB::commit();
            return 1;
        }else{
            DB::rollBack();
            return -1;
        }
    }

     /*  获取用户理财
      *   $user_id
      *   $coin_id
      *   $invest_id
      */
      public function getUserInvestCount($user_id,$coin_id,$invest_id){
          $userCount['count']=[
                'investment_total_money' => 0,
                'accumulated_income_money' =>0,
                'estimated_revenue' => 0
          ];
            $countWhere =['user_id'=>$user_id,'coin_id'=>$coin_id];
            $orderWhere =['user_id'=>$user_id,'coin_id'=>$coin_id,'invest_id'=>$invest_id];
            $userInvestCount =$this->userCount->getRecordByCondition($countWhere)->toArray();
            $userCount['order'] = $this->userInvest->getUserCountRecords($orderWhere);
           if($userInvestCount)
               $userCount['count'] = $userInvestCount[0];
            return $userCount;
      }

      /* 获取用户历史
       *  $user_id
       *
       */
     public function getUserHistoryOrder($user_id){

      return $this->userInvest->getUserHistoryOrder($user_id);

     }

     /* 用户未到期撤回本金
      * @param : $user_id : 用户id
      *          $invest_order :用户订单
      *  return -3 : 事务失败；
      *         -2 ： 订单不符合状态；
      *         -1 ： 不存在相关订单；
      */
     public function cancelUserInvestOrder($user_id,$invest_order){

          //查出订单
          $where = ['user_id'=>$user_id,'invest_order'=>$invest_order];
          $userRecord = $this->userInvest->getRecordByCondition($where)->toArray();
          if(!$userRecord) return -1;
          if($userRecord[0]['invest_status'] != 1) return -2;
//         if($userRecord[0]['invest_id'] !=2 && $userRecord[0]['invest_status'] != 1) return -2;
          $userRecordCountWhere =['user_id'=>$user_id,'coin_id'=>$userRecord[0]['coin_id']];
          $userCount = $this->userCount->getRecordByCondition($userRecordCountWhere)->toArray();
          $newUserCount['investment_total_money'] = $userCount[0]['investment_total_money']-$userRecord[0]['invest_money'];
          $newUserCount['estimated_revenue'] = $userCount[0]['estimated_revenue']-$this->computeEarnings($userRecord[0]['invest_money'],$userRecord[0]['rate_of_return_set']);
          DB::beginTransaction();
          //变更订单状态
         $userRecordWhere = ['invest_order'=>$invest_order,'user_id'=>$user_id];
         $updateData = ['invest_status'=>4];

         if($this->userInvest->updateUserInvest($userRecordWhere,$updateData)
             && $this->userWalletDao->addUsableBalance($userRecord[0]['coin_id'],$user_id,$userRecord[0]['invest_money'])
             && $this->userCount->updateUserInvest($userRecordCountWhere,$newUserCount)
         ){
             DB::commit();
             return 1;
         }else{
             DB::rollBack();
             return -3;
         }

     }

     /* 获取投资类型
      *
      *
      */
     public function getInvestType($coin_id){
         $where= ['is_usable'=>1,'coin_id'=>$coin_id];

         return  $this->investmentTypeDao->getRecordByCondition($where)->toArray();

     }

     /* 获取可以用作理财的虚拟币
      *
      *
      */
      public function getInvestCoin(){

                     $where =['is_invest'=>1,'is_usable'=>1];

              return $this->coinTypeDao->getRecordByCondition($where)->toArray();

      }

      /* 获取用户到期后的本金和利息
      *  return -3 : 事务失败；
      *         -2 ： 订单不符合状态；
      *         -1 ： 不存在相关订单；
       */
      public function getUserInvestMoney($user_id,$invest_order){
          //查出订单
          $where = ['user_id'=>$user_id,'invest_order'=>$invest_order];
          $userRecord = $this->userInvest->getRecordByCondition($where)->toArray();
          if(!$userRecord) return -1;
          if($userRecord[0]['invest_status'] != 2) return -2;
          $userRecordCountWhere =['user_id'=>$user_id,'coin_id'=>$userRecord[0]['coin_id']];
          $userCount = $this->userCount->getRecordByCondition($userRecordCountWhere)->toArray();
          $userIncome =$this->computeEarnings($userRecord[0]['invest_money'],$userRecord[0]['rate_of_return_set']);
          //给统计数据增减
          $newUserCount['investment_total_money'] = $userCount[0]['investment_total_money']-$userRecord[0]['invest_money'];
          $newUserCount['accumulated_income_money'] = $userCount[0]['accumulated_income_money']+$userIncome;
          $newUserCount['estimated_revenue'] = $userCount[0]['estimated_revenue']-$userIncome;
           //收益

          DB::beginTransaction();
          //变更订单状态
          $userRecordWhere = ['invest_order'=>$invest_order,'user_id'=>$user_id];
          $updateData = ['invest_status'=>3];
          if(     $this->userInvest->updateUserInvest($userRecordWhere,$updateData)
              &&  $this->userWalletDao->addUsableBalance($userRecord[0]['coin_id'],$user_id,$userRecord[0]['invest_money']+$userIncome)
              &&  $this->userCount->updateUserInvest($userRecordCountWhere,$newUserCount)
          ){
              DB::commit();
              return 1;
          }else{
              DB::rollBack();
              return -3;
          }
      }




    /* 计算用户投资预计收益
     *  @param
     *    $invest_money: 投资的金额
     *    $compute_rate :利率
     * */
    private function computeEarnings($invest_money,$compute_rate){

        if(!$this->compute_rate)
              $this->getComputeRate($compute_rate);

          return   $invest_money*$this->compute_rate;
    }
    //获取利率
    private function getComputeRate($compute_rate){

       if(!$this->compute_rate){
           $this->compute_rate = $compute_rate/100;
       }
    }

    /* 获取用户余额
     *
     */
    public function getInvestCoinBalance($coin_id,$user_id){

       return $this->userWalletDao->getUserCoinBalance($coin_id,$user_id);


    }


    /*  获取投资理财套餐
     *
     *
     */
    public function getInvestSetMeal($coin_id,$invest_id){

              $where =['coin_id'=>$coin_id,'is_usable'=>1,'invest_id'=>$invest_id];

              return $this->investmentRuleDao->getInvestSetMeal($where);

    }

}