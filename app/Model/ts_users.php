<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/21
 * Time: 10:51
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ts_users extends Model
{


    protected $table = 'users';


    protected $primaryKey = 'user_id';




    public function getUserPhoneAttribute($value)
    {
        return substr_replace($value,'****',3,4);
    }

}