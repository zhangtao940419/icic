<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/9/23
 * Time: 15:19
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class StoCoinFreeStage extends Model
{

    protected $table = 'sto_coin_free_stage';


    protected $primaryKey = 'id';



    protected $guarded = [];



    public $appends = ['status' , 'free_day'];



    public function getStatusAttribute()
    {//0未到期1到期可提
        $now = date('Ymd');
        if ($now >= $this->start_day) return 1;return 0;


    }

    public function getFreeDayAttribute()
    {
        return substr($this->start_day,0,4) . '.' . substr($this->start_day,4,2) . '.' . substr($this->start_day,6,2);
    }

    public function getBuyId($id)
    {
        return $this->find($id);
    }


    public function getCoinFreeStageRecord($dataid)
    {

        return $this->where(['data_id' => $dataid])->get();
    }


    public function user_tq_record()
    {
        return $this->hasOne(StoRewardFlow::class,'free_stage_id','id');
    }






}