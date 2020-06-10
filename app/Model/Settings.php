<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/6
 * Time: 17:26
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{

    protected $table = 'settings';


    protected $primaryKey = 'id';


    protected $guarded = [];




    public function getSetting($setting_key)
    {
        return $this->where(['setting_key' => $setting_key])->first();
    }




    public function getAllSetting()
    {
        return $this->all();
    }




    //
    public function getUserQuestionMsg()
    {
        return $this->where(['setting_key'=>'user_question_msg'])->value('setting_value');
    }


    public function updateOne($key,$value)
    {
        return $this->where(['setting_key'=>$key])->update(['setting_value'=>$value]);

    }



}