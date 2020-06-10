<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description', 'parents_id'];

    //获取传入的分类的无限子类ids
    public static function getSubIds($category_id){
        $categorys = self::all();

        if(blank($categorys)){
            return [];
        }else{
            $categorys = $categorys->toArray();
        }

        $subIds = get_tree_child($categorys,$category_id);

        return $subIds;
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

    public function articles()
    {
        return $this->hasMany('App\Model\Admin\Article');
    }
}
