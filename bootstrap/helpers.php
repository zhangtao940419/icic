<?php

//找推荐人并计算邀请人的佣金
function findTopId($data, $pid)
{
    $rate = \DB::table('lv_rate')->first();

    static $arr = [];

    foreach ($data as $v) {
        if ($v->user_id == $pid) {
            $arr['上级']['上级id'] = $v->user_id;
            $arr['上级']['rate'] = $rate->lv1;
            foreach ($data as $i) {
                if ($v->pid == $i->user_id) {
                    $arr['上上级']['上上级id'] = $i->user_id;
                    $arr['上上级']['rate'] = $rate->lv2;
                    foreach ($data as $j) {
                        if ($i->pid == $j->user_id) {
                            $arr['上上上级']['上上上级id'] = $j->user_id;
                            $arr['上上上级']['rate'] = $rate->lv3;
                        }
                    }
                }
            }
        }
    }

    return $arr;
}

function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}



