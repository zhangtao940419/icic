<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/13
 * Time: 18:08
 */

namespace App\Model\Admin;


use App\Model\User;
use Illuminate\Database\Eloquent\Model;

class AdminUserBehavior extends Model
{

    protected $table = 'admin_user_behavior';

    protected $primaryKey = 'id';

    protected $guarded = [];


    protected $column = ['admin_user_id','behavior_des','type_des','user_id'];


    public function saveOneRecord(array $data)
    {
        foreach ($data as $key => $value){
            if (in_array($key,$this->column)){
                $this->$key = $value;
            }
        }
        return $this->save();
    }


    public function user()
    {
        return $this->hasOne('App\Model\Admin\adminUser','id','admin_user_id');
    }


    public function uuser()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }


    public function getTypes()
    {

        $re = $this->select(['type_des'])->groupBy('type_des')->get()->toArray();

        return $re;

    }


}