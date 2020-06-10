<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/17
 * Time: 14:48
 */

namespace App\Server\Investment\Dao;
use App\Model\CoinType;


class CoinTypeDao extends CoinType
{


     public function getInvestCoinType($where){



         return parent::getRecordByCondition($where)->toArray();
     }

}