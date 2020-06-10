<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/10/16
 * Time: 20:51
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CoinTotalDayTongji extends Model
{

    protected $table = 'coin_total_day_tongji';


    protected $primaryKey = 'id';


    protected $guarded = [];


    public function insertOne($coinId,$un,$cn,$tn)
    {
        $this->day = date('Ymd');
        $this->coin_id = $coinId;
        $this->user_total_num = $un;
        $this->center_num = $cn;
        $this->total_num = $tn;
        return $this->save();


    }

    public function getLastTotalNum($coinId)
    {
        return $this->where(['coin_id' => $coinId,'day'=>date('Ymd',time()-24*3600)])->value('total_num');
    }





}