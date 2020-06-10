<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/15
 * Time: 10:50
 */

namespace App\Server\StoServers\Admin;

use App\Server\StoServers\Admin\Dao\StoCoinDataDao;
use App\Server\StoServers\Admin\Dao\StoCoinStageDao;
use App\Server\StoServers\Admin\Dao\StoCoinStageDayDao;
use Psy\Exception\ErrorException;
use Illuminate\Support\Facades\DB;


class StoServer
{
    //Sto发行列表
    private $stoCoinDataDao=null;

    private $stoStageDataDao=null;

    private $stoCoinStageDayDao=null;

    public function __construct(
        StoCoinDataDao $stoCoinDataDao,
        StoCoinStageDao $stoStageDataDao,
        StoCoinStageDayDao $stoCoinStageDayDao
    )
    {
        $this->stoCoinDataDao = $stoCoinDataDao;

        $this->stoStageDataDao = $stoStageDataDao;

        $this->stoCoinStageDayDao = $stoCoinStageDayDao;
    }

     public function getAllStoList(){

      return $this->stoCoinDataDao->getStoCoinData(['is_usable'=>1]);


     }


     public function addStoData($data){
         if($data['total_coin_issuance']<$data['issue_coin_number'])
             return false;

          if($this->stoCoinDataDao->getRecordByCondition(['base_coin_id'=>$data['base_coin_id'],'coin_id'=>$data['coin_id'],'is_usable'=>1])->count())
              return false;

        return $this->stoCoinDataDao->insertData($data);
     }


       //添加sto阶段列表
      public function addStoStageData($data){
          unset($data['_token']);
          if(!$data['issue_begin_time']=strtotime($data['issue_begin_time']))
              return false;
          if(strtotime($data['end_time'])<strtotime($data['start_time']))
              return false;
          if($data['issue_begin_time']<time())
              return false;
          $stage_number =$this->stoStageDataDao->where(['data_id'=>$data['data_id'],'is_usable'=>1])->count();
          if($stage_number<=0)
          {   // $stage_number小于=0说明是第一阶段
              $data['stage_number'] = 1;

          }else{
              $stage_number =  $this->stoStageDataDao->where(['data_id'=>$data['data_id'],'is_usable'=>1])->max('stage_number');
              $data['stage_number'] = $stage_number+1;
          }
          $data['stage_issue_remain_number'] =$data['stage_issue_number'];
          $data['created_at'] =date('y-m-d h:i:S',time());
          $data['updated_at'] =date('y-m-d h:i:S',time());
           DB::beginTransaction();
            $stoStageId = $this->stoStageDataDao->addStoStage($data);
            $stoStageDay['stage_id'] =$stoStageId;
            $stoStageDay['data_id'] =$data['data_id'];
            $stoStageDay['coin_id'] =$data['exchange_coin_id'];
            $stoStageDay['created_at'] =date('y-m-d h:i:S',time());
            $stoStageDay['updated_at'] =date('y-m-d h:i:S',time());
            for($i=1;$i<=$data['issue_time'];$i++){
                $stoStageDay['issue_day']=$i;
               $stoCoinStageDay[$i] = $this->stoCoinStageDayDao->addStoCoinStageDay($stoStageDay);
            }
          if(!$stoStageId)
              DB::rollBack();
            foreach($stoCoinStageDay as $value){
                if(!$value)
                    DB::rollBack();
            }
           DB::commit();
            return true;


                                        }

        //显示stostage阶段列表

    public function getStoStageList($id){

        $stoStageList = $this->stoStageDataDao->getRecordByCondition(['data_id'=>$id,'is_usable'=>1])->sortBy('pid')->toArray();

        if(!empty($stoStageList)){
            foreach($stoStageList as $key => $vaue){
                   if($vaue['issue_status']==0){
                       $stoStageList[$key]['issue_status'] = "<font color='red'>预热发行<font>";
                   }
                if($vaue['issue_status']==1){
                    $stoStageList[$key]['issue_status'] = "<font color='green'>发行中<font>";
                }
                if($vaue['issue_status']==2){
                    $stoStageList[$key]['issue_status'] = "<font color='#ff4500'>已完结<font>";
                }

            }
        }
       return $stoStageList;
    }



    //获取阶段天数
    public function getStoStageDayList($stage_id){

       $stoStageDayLis =  $this->stoCoinStageDayDao->getStoStageDay(['stage_id'=>$stage_id,'is_usable'=>1]);
        if(!empty($stoStageDayLis)){
            foreach($stoStageDayLis as $key => $vaue){
                if($vaue['issue_status']==0){
                    $stoStageDayLis[$key]['issue_status'] = "<font color='red'>预热发行<font>";
                }
                if($vaue['issue_status']==1){
                    $stoStageDayLis[$key]['issue_status'] = "<font color='green'>发行中<font>";
                }
                if($vaue['issue_status']==2){
                    $stoStageDayLis[$key]['issue_status'] = "<font color='#ff4500'>已完结<font>";
                }

            }
            return  $stoStageDayLis;
        }

    }


    //更新sto列表
     public function updateStoList($data,$id){

          return $this->stoCoinDataDao->updateStoList($data,$id);

     }

     //获取发行阶段发行总数
    public function getStoStageIssueCount($stage_id){

       return  $this->stoStageDataDao->where(['stage_id'=>$stage_id,'is_usable'=>1])->value('stage_issue_number');
    }


    //获取发行天数货币总数
    public function getStoStageDayIssueCount($stage_id){

        return  $this->stoCoinStageDayDao->where(['stage_id'=>$stage_id,'is_usable'=>1])->sum('stage_issue_number');
    }




    //更新每日发行数量
    public function updateStoStageDay($data,$id){

         return $this->stoCoinStageDayDao->updateStoStageDay($data,$id);


    }


    //更新每阶段发行数量
    /*
     * "_token" => "9SFVYBbAdejzWQhjiw4vQc22C3g2PehEi9gmB03t"
  "base_coin_id" => "5"
  "data_id" => "12"
  "exchange_coin_id" => "6"
  "stage_issue_number" => "100000"
  "exchange_rate" => "0.3"
  "issue_begin_time" => "2019-08-17"
  "issue_time" => "2"
  "start_time" => "00:59"
  "end_time" => "23:00"*/

    public function updateStoStage($data,$id){
        unset($data['_method']);
        unset($data['_token']);
        if(!$data['issue_begin_time']=strtotime($data['issue_begin_time']))
            return false;
        if(strtotime($data['end_time'])<strtotime($data['start_time']))
            return false;
        if($data['issue_begin_time']<time())
            return false;
        $data['stage_issue_remain_number'] =$data['stage_issue_number'];
        DB::beginTransaction();
        $deleteStoStageDay = $this->stoCoinStageDayDao->deleStoStageDay($id);

        $updateStoStage = $this->stoStageDataDao->updateStoStage($data,$id);

        $stoStageDay['stage_id'] =$id;
        $stoStageDay['data_id'] =$data['data_id'];
        $stoStageDay['coin_id'] =$data['exchange_coin_id'];
        $stoStageDay['created_at'] =date('y-m-d h:i:S',time());
        $stoStageDay['updated_at'] =date('y-m-d h:i:S',time());
        for($i=1;$i<=$data['issue_time'];$i++){
            $stoStageDay['issue_day']=$i;
            $stoCoinStageDay[$i] = $this->stoCoinStageDayDao->addStoCoinStageDay($stoStageDay);
        }
        if(!$deleteStoStageDay || !$updateStoStage)
            DB::rollBack();
        foreach($stoCoinStageDay as $value){
            if(!$value)
                DB::rollBack();
        }
        DB::commit();
        return true;

    }


       //根据data获取数据
    public function getDataById($data_id){

       return $this->stoCoinDataDao->getRecordByCondition(['data_id'=>$data_id,'is_usable'=>1])->toArray();

    }


    //根据$stage_id获取数据
    public function getStoStageById($stage_i){

        return $this->stoStageDataDao->getRecordByCondition(['stage_id'=>$stage_i,'is_usable'=>1])->toArray();

    }


    //根据$day_id获取数据
    public function getStoStageDayById($day_id){

        return $this->stoCoinStageDayDao->getRecordByCondition(['day_id'=>$day_id,'is_usable'=>1])->toArray();

    }




}