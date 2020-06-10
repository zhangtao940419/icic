<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/10/8
 * Time: 15:18
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class UserQuestionType extends Model
{

    protected $table = 'user_question_type';


    protected $primaryKey = 'id';

    protected $guarded = [];


    public function getAllTypes()
    {
        return $this->get();
    }


    public function insertOne($type)
    {
        $this->type = $type;
        return $this->save();
    }




}