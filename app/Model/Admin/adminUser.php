<?php

namespace App\Model\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;

class adminUser extends Authenticatable
{
    protected $fillable = ['username', 'password', 'email', 'phone', 'description', 'avatar','phone'];

    public function articles()
    {
        return $this->hasMany('App\Model\Admin\Article', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Model\Admin\Permission', 'permission_user', 'user_id', 'permission_id');
    }


    protected $guarded = ['geetest_challenge', 'geetest_validate', 'geetest_seccode'];
}
