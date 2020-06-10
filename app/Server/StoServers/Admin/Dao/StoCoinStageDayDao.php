<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/17
 * Time: 14:48
 */

namespace App\Server\StoServers\Admin\Dao;

use App\Model\StoCoinStageDay;


class StoCoinStageDayDao extends StoCoinStageDay
{


     public function addStoCoinStageDay($data){

         return parent::addStoCoinStageDay($data);

     }



     public function getStoStageDay($where)
     {
         return parent::getRecordByCondition($where)->toArray(); // TODO: Change the autogenerated stub
     }


     public function updateStoStageDay($data,$id){
            $data['stage_issue_remain_number'] =$data['stage_issue_number'];
            return parent::updateData($data,$id);

     }

    public function deleStoStageDay($stage_id){

        return parent::deleteData(['stage_id'=>$stage_id]);

    }




}