<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserinvestmentCount extends Model
{
    protected $table = 'user_investment_count';


    protected $primaryKey = 'id';


    protected $insertField = ['coin_id','user_id','investment_total_money','estimated_revenue'];

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

      //更新用户的数据
       public function updateUserInvest($where,$data){

             return $this->where($where)->update($data);


       }

       //增加用户投资的钱财
     public function addUserInvest($where,$data){

         return $this->where($where)->increment('','','');
     }


}
