<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ComplainImg extends Model
{
    /*表名称*/
    protected $table = 'complain_img';

    protected $primaryKey = 'complain_img_id';

    private $fields =['complain_id','img_url'];

    public function saveComplainImg($complain_id,$img_url){

        $this->complain_id = $complain_id;

        $this->img_url = $img_url;

        return $this->save();
    }

}
