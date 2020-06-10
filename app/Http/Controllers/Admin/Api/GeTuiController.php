<?php

namespace App\Http\Controllers\Admin\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Earnp\Getui\Getui;

class GeTuiController extends Controller
{
    //推送文字数据
    public function postMsg()
    {
        $template = "IGtTransmissionTemplate";
        $data = "a";
        $config = ["type" => "HIGH", "title" => "你有一条新消息", "body" => "你有一个3000元的订单需要申请", "logo" => "", "logourl" => ""];
        $CID = "e5048414727d92c15e0fcdc279d82a84";
        $msg = Getui::pushMessageToSingle($template, $config, $data, $CID);

        return $msg;
    }



}
