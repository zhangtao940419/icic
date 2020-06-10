<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/19
 * Time: 14:53
 */

namespace App\ModelFilters;


use EloquentFilter\ModelFilter;

class StoRewardFlowFilter extends ModelFilter
{



    //用户名筛选
    public function userphone($userphone)
    {
        $this->whereHas('user',function ($query) use($userphone) {
            $query->where('user_phone', $userphone);
        });
    }

    //用户名筛选
    public function suserphone($suserphone)
    {
        $this->whereHas('s_user',function ($query) use($suserphone) {
            $query->where('user_phone', $suserphone);
        });
    }





}