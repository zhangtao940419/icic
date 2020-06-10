<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/5
 * Time: 17:48
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class SettingTJReward extends Model
{

    protected $table = 'setting_tj_reward';

    protected $primaryKey = 'id';

    protected $appends = ['s_t','e_t'];
    protected $guarded = [];


    public function getSTAttribute()
    {
        return substr($this->start_time,0,10);
    }

    public function getETAttribute()
    {
        return substr($this->end_time,0,10);
    }
    public function getOne($s_nums)
    {

        return $this->where(['s_number'=>$s_nums])->where('start_time','<',datetime())->where('end_time','>',datetime())->first();


    }


    public function getAll($s_nums)
    {
        return $this->where(['s_number'=>$s_nums])->where('start_time','<',datetime())->where('end_time','>',datetime())->get();
    }


}