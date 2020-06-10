<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InvestmentRule extends Model
{
    protected $table = 'investment_rule_set';


    protected $primaryKey = 'type_id';

    protected $insertField = ['invest_id','invest_time','coin_id','rate_of_return_set'];

    protected $updateFiled = ['invest_id','invest_time','coin_id','rate_of_return_set'];

    public function insertData($data){
        foreach ($this->insertField as $value){
            $this->$value=$data[$value];
        }
      return $this->save($data);
                                   }
    //根据条件获取数据
    public function getRecordByCondition($where){

        return $this->where($where)->get();

    }

    public function getOneRecord($id){

    return $this->with('getCoinNames','getInvestNames')->find($id);

    }

    public function updateInvestStatue($where,$data)
    {
        return $this->where($where)->update($data);
    }

    public function getAllRecords($where){

    return $this->with('getCoinNames','getInvestNames')->where($where)->get();

    }

    //更新用户的数据
    public function updateInvestRule($where,$data){

        foreach ($this->insertField as $value){
            $updateData[$value]=$data[$value];
        }
        return $this->where($where)->update($updateData);
    }

    //关联获取虚拟货币的名称
    public function getCoinNames(){

     return $this->hasOne('App\Model\CoinType','coin_id','coin_id');

    }

    //关联获取虚拟货币的名称
    public function getInvestNames(){

        return $this->hasOne('App\Model\InvestmentType','invest_id','invest_id');

    }
}
