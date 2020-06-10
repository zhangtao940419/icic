<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/9
 * Time: 11:08
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class StoCoinStageDay extends Model
{




    protected $table = 'sto_coin_stage_day';

    protected $primaryKey = 'day_id';


    protected $insertField = ['coin_id','stage_issue_number','stage_issue_remain_number','issue_day'];

    protected $updateFiled = ['coin_id','stage_issue_number','stage_issue_remain_number','issue_status','is_usable'];

    protected $guarded = [];

    public function insertData($data){
        foreach ($this->insertField as $value){
            $this->$value=$data[$value];
        }
        return $this->save($data);
    }

     //添加
      public function addStoCoinStageDay($data){

           return $this->insert($data);

    }

    //根据条件获取数据
    public function getRecordByCondition($where){

        return $this->with('getCoinNames')->where($where)->get();
    }

    //更新
    public function updateData($data,$id){

        $sto =  $this->find($id);

        foreach ($data as $key=> $value){
            if(in_array($key,$this->updateFiled))
                $sto->$key=$data[$key];
        }
        return $sto->save($data);
    }

    public function deleteData($where){

        return $this->where($where)->delete();
    }


    public function sto_coin_stage()
    {

        return $this->belongsTo(StoCoinStage::class,'stage_id','stage_id');

    }

    public function sto_coin_data()
    {
        return $this->belongsTo(StoCoinData::class,'data_id','data_id');
    }



    //减库存
    public function dec_remain_num($num)
    {
        return $this->where('day_id',$this->day_id)->where('stage_issue_remain_number','>=',$num)->decrement('stage_issue_remain_number',$num);
    }


    //关联获取虚拟货币的名称
    public function getCoinNames(){

        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')->select('coin_id','coin_name');

    }


}