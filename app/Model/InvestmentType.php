<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class InvestmentType extends Model
{
    protected $table = 'investment_type_set';


    protected $primaryKey = 'invest_id';


    protected $insertField =['invest_type_name','coin_id'];

    //根据条件获取数据
    public function getRecordByCondition($where){

        return $this->where($where)->get();
    }

    public function insertInvest($data)
    {

        foreach ($this->insertField as $value){
            $this->$value=$data[$value];
        }
        return $this->save($data);

    }

    public function getInvestTypeWithCoinName($where){

       return  $this->with('getCoinNames')->where($where)->get();
    }


    public function getCoinNames(){

      return  $this->hasOne('App\Model\CoinType', 'coin_id','coin_id');

    }

}
