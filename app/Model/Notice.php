<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    protected $table = 'notice';

    protected $fillable = ['notice_img', 'notice_content', 'switch'];

    protected $dates = ['deleted_at'];

    public function getNewNotice()
    {
        return $this->where('is_usable', 1)->select(['notice_img', 'notice_content', 'switch'])->latest()->first();
    }

    public function getNoticeImgAttribute($value)
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . $value;
    }

}
