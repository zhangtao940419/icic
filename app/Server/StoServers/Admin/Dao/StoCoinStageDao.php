<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/17
 * Time: 14:48
 */

namespace App\Server\StoServers\Admin\Dao;

use App\Model\StoCoinStage;


class StoCoinStageDao extends StoCoinStage
{


     public function addStoCoinStageData($data){

         return parent::insertData($data);

     }


     public function updateStoStage($data,$id){

     return parent::updateData($data,$id);

     }




}