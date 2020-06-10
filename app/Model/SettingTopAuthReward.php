<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/5
 * Time: 17:15
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class SettingTopAuthReward extends Model
{
    protected $table = 'setting_top_auth_reward';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public function getOne()
    {
        return $this->where('switch',1)->first();
    }


    public function getAll()
    {
        return $this->where('switch',1)->get();
    }





}