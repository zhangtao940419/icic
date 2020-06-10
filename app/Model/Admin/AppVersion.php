<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/26
 * Time: 14:16
 */

namespace App\Model\Admin;


use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{

    protected $table = 'app_version';

    protected $primaryKey = 'id';

    protected $guarded = [];


    public function getOneRecord($type = 'android')
    {
        return $this->where(['phone_type'=>strtolower($type)])->first();
    }

}