<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserDatum extends Model
{
    //场外交易次数信息表
    protected $primaryKey = 'datum_id';
    /*
     * 与模型关联的数据表
     * @var string
     */
    protected $table = 'outside_user_trade_datum';

    protected $guarded = [];



    /* 增加交易总次数
     *
     *
     */
    public function addTradeTotalNum($user_id){

        return $this->where(['user_id'=>$user_id,'is_usable'=>1])->increment('trade_total_num');


    }


    /* 增加交易总次数
     *
     *
     */
    public function addTradeFavourableComment($user_id){

        return $this->where(['user_id'=>$user_id,'is_usable'=>1])->increment('trade_favourable_comment');


    }



    public function getTradeFavourableCommentAttribute($value)
    {
        if($this->trade_total_num){
            return ($value/$this->trade_total_num)*100;
        }else{
            return 0;
        }

    }

}

