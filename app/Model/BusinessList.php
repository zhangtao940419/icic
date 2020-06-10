<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/8
 * Time: 10:26
 */

namespace App\Model;



use Illuminate\Database\Eloquent\Model;

class BusinessList extends Model
{

    protected $table = 'business';

    protected $primaryKey = 'business_id';


    public function getRecordByUserId(int $userId)
    {
        return $this->where(['user_id'=>$userId,'is_usable'=>1])->first();
    }

}