<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/19
 * Time: 10:47
 */

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class UserBuyStoCoinRecordFilter extends ModelFilter
{



    //用户名筛选
    public function userphone($userphone)
    {
        $this->whereHas('user',function ($query) use($userphone) {
            $query->where('user_phone', $userphone);
        });
    }



    public function exchangecoinid($exchangecoinid)
    {
            $this->where(['exchange_coin_id'=>$exchangecoinid]);

    }





    public function stage($stage)
    {
        $this->whereHas('stage',function ($query) use($stage) {
            $query->where('stage_number', $stage);
        });
    }

    public function day($day)
    {
        $this->whereHas('day',function ($query) use($day) {
            $query->where('issue_day', $day);
        });
    }

    public function rate($rate)
    {
        $this->whereHas('stage',function ($query) use($rate) {
            $query->where('exchange_rate', $rate);
        });
    }

    //开始时间
    public function begintime($begintime)
    {
        $this->where('created_at', '>=', $begintime);
    }

    //结束时间
    public function endtime($endtime)
    {
        $this->where('created_at', '<=', $endtime);
    }





}