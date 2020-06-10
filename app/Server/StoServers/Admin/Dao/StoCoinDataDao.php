<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/17
 * Time: 14:48
 */

namespace App\Server\StoServers\Admin\Dao;

use App\Model\StoCoinData;


class StoCoinDataDao extends StoCoinData
{


     public function getStoCoinData($where){
         return parent::getAllRecords($where)->toArray();
     }

     public function updateStoList($data,$id){

         return parent::updateData($data,$id);
     }

}