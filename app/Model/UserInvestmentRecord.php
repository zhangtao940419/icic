<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserInvestmentRecord extends Model
{
    protected $table = 'user_investment_record';


    protected $primaryKey = 'id';


    protected $insertField =['invest_id','coin_id','invest_order','user_id','invest_pay_time','invest_time','rate_of_return_set',
        'invest_money'];

    //根据history
    public function getHistoryTrade($where){

        return $this->with('getCoinName','getInvestTypeName')->where($where)->get();

    }

    //
    public function getAdminTradeFlow($where){

        return $this->with('getCoinName','getInvestTypeName')->where($where)->paginate(15);
    }

    //根据条件获取数据
    public function getRecordByCondition($where){

        return $this->where($where)->get();

    }



    //将用户理财记录插入数据库
    public function saveUserInvest($data){

        foreach ($this->insertField as $value){
            $this->$value=$data[$value];
        }
        return $this->save($data);
    }

    //根据条件变更数据
    public function updateUserInvest($where,$data){
    return $this->where($where)->update($data);
    }

    /*关联coin表*/
    public function getCoinName()
    {
        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')->select('coin_id','coin_name');
    }

    /*获取用户理财的类型*/
    public function getInvestTypeName(){
        return $this->hasOne('App\Model\InvestmentType','invest_id','invest_id')->select('invest_id','invest_type_name');
    }


}
