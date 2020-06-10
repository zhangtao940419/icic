<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/9/28
 * Time: 10:08
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class UserQuestion extends Model
{



    protected $table = 'user_question';



    protected $primaryKey = 'id';




    protected $guarded = [];


    public $appends = ['question_limit','answer_limit'];


    public function getQuestionLimitAttribute()
    {
        return str_limit($this->question,18,'...');
    }
    public function getAnswerLimitAttribute()
    {
        return str_limit($this->answer,26,'...');
    }

    public function getSameTypeWaitHandleNum($userId,$typeId)
    {
        return $this->where(['user_id'=>$userId,'type_id' => $typeId,'status'=>0])->count();
    }

    public function insertOne($userId,$question,$typeId,$email = '',$images = [])
    {
        $this->user_id = $userId;
        $this->question = $question;
        $this->email = $email;
        $this->type_id = $typeId;
        $t = 1;
        foreach ($images as $image){
            if ($t == 1) $this->image1 = $image;
            if ($t == 2) $this->image2 = $image;
            if ($t == 3) $this->image3 = $image;
            $t++;
        }


        return $this->save();


    }



    public function user()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }


    public function getStatus()
    {

        return ['待处理','已处理'][$this->status];


    }

    public function type()
    {
        return $this->belongsTo(UserQuestionType::class,'type_id','id');
    }

    public function getImage1Attribute($v)
    {
        if ($v)
        return env('QI_NIU_URL') . $v;
        return $v;
    }

    public function getImage2Attribute($v)
    {
        if ($v)
            return env('QI_NIU_URL') . $v;
        return $v;
    }

    public function getImage3Attribute($v)
    {
        if ($v)
            return env('QI_NIU_URL') . $v;
        return $v;
    }
    public function getAImage1Attribute($v)
    {
        if ($v)
            return env('QI_NIU_URL') . $v;
        return $v;
    }

    public function getAImage2Attribute($v)
    {
        if ($v)
            return env('QI_NIU_URL') . $v;
        return $v;
    }

    public function getAImage3Attribute($v)
    {
        if ($v)
            return env('QI_NIU_URL') . $v;
        return $v;
    }



}