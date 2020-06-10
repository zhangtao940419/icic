<?php

namespace App\Http\Controllers\Admin;

use App\Server\InsideTrade\InsideTradeServer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FaceController extends Controller
{
    public function index(InsideTradeServer $insideTradeServer,Request $request)
    {
//
//        $tradeTeamList = $insideTradeServer->getAllCoin();
//
//        dd($tradeTeamList);
//
//        $inParam = [
//            'base_coin_id' => 'required|integer',
//            'exchange_coin_id' => 'required|integer',
//            'pageSize' => 'required|integer',
//        ];
//
//        $pankou = $this->inSideTradeServer->adminGetTradeDisksurfaceServer($inParam);
//
//
//
//
//
//        return view('admin.face_disk.new_index',compact('tradeTeamList'));
        return view('admin.face_disk.index');
    }
}
