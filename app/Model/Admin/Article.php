<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $table = 'articles';

    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'cover', 'user_id'];

    public function category()
    {
        return $this->belongsTo('App\Model\Admin\Category');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\Admin\adminUser', 'user_id');
    }


    public function getCoverAttribute($value)
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . $value;
    }

}
