<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/21
 * Time: 15:38
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ContractUserBuyRecords extends Model
{

    protected $table = 'contract_user_buy_records';

    protected $primaryKey = 'id';

    protected $appends = ['reward_des','type_des'];

    protected $guarded = [];

    public function getRewardDesAttribute()
    {
        if ($this->reward > 0) return '+'.$this->reward;
        return $this->reward;
    }

    public function getTypeDesAttribute()
    {
        return [1=>'多',2=>'平',3=>'空'][$this->type];
    }


    public function insertOne($userId,$activityId,$type,$amount)
    {
        $this->user_id = $userId;
        $this->activity_id = $activityId;
        $this->type = $type;
        $this->amount = $amount;
        return $this->save();

    }



    public function getUserBuyRecords($userId)
    {
        return $this->with(['activity'=>function($q){
            $q->select(['id','activity_no','jg_status']);
        }])->where(['user_id' =>$userId])->latest()->get();


    }

    public function getActivityRecord($activityId)
    {
        return $this->where(['activity_id'=>$activityId])->get();
    }


    public function activity()
    {

        return $this->belongsTo(ContractActivity::class,'activity_id','id');

    }


    public function getUserBuyTypeNum($activityId,$type)
    {
        return $this->where(['activity_id'=>$activityId,'type'=>$type])->sum('amount');

    }


    //kaijiang
    public function open($activityId)
    {
        $s1n = $this->getUserBuyTypeNum($activityId,1);
        $s2n = $this->getUserBuyTypeNum($activityId,2);
        $s3n = $this->getUserBuyTypeNum($activityId,3);

        if ($s1n == $s2n && $s2n == $s3n){
            $rand = rand(1,10);
            if ($rand <= 4){
                return 1;
            }elseif ($rand <= 8 && $rand >= 5){
                return 3;
            }else{
                return 2;
            }
        }

        if ($s1n == min($s1n,$s2n,$s3n)) return 1;
        if ($s2n == min($s1n,$s2n,$s3n)){
            if ($s1n == min($s1n,$s3n)){
                if (rand(0,3) == 1) return 2;
                return 1;
            }
            if ($s3n == min($s1n,$s3n)){
                if (rand(0,3) == 1) return 2;
                return 3;
            }
        }
        if ($s3n == min($s1n,$s2n,$s3n)) return 3;

    }



}