<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/15
 * Time: 16:09
 */

namespace App\Server\Investment\Dao;

use App\Model\UserinvestmentCount;

class UserinvestmentCountDao extends  UserinvestmentCount
{

    //用户第一次购买某个币种的投资
    public function saveUserInvest($data)
    {
        return parent::saveUserInvest($data); // TODO: Change the autogenerated stub
    }


     //用户已存在购买某币种的投资
      public function updateUserInvest($where,$data){

            return parent::updateUserInvest($where,$data);

      }

      //用户查询自己理财情况
     public function getRecordByCondition($where)
     {
         return parent::getRecordByCondition($where); // TODO: Change the autogenerated stub
     }


}