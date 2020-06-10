<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class Permission extends model
{
    protected $fillable = ['name', 'display_name', 'route', 'description'];

    public function adminUsers()
    {
        return $this->belongsToMany('App\Model\Admin\adminUser', 'permission_user', 'permission_id', 'user_id');
    }

    public function getTree($data, $parents_id = 0, $leve = 0)
    {
        static $array = [];
        foreach ($data as $k => $v) {
            if($v->parents_id == $parents_id){
                $v->leve = $leve;
                $array[] = $v;
                $this->getTree($data, $v->id, $leve + 1);
            }
        }

        return $array;
    }
}
