<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/18
 * Time: 9:48
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Http\Controllers\Web\BaseController;
use App\Http\Response\ApiResponse;
use App\Server\HuoBiServer\Server\HuobiServer;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HuoBiController extends BaseController
{

    use RedisTool,ApiResponse;
    private $huoBiServer;

    function __construct(HuobiServer $huobiServer)
    {
        $this->huoBiServer = $huobiServer;
    }

    //所有交易对 已缓存
    public function getAllSymbolMerged()
    {if (env('APP_V') == 'test') return $this->successWithData([]);
        $records = $this->huoBiServer->getAllRecords();
        return $this->successWithData(['tradeTeamList'=>$records]);
    }

    //互链接口,已缓存
    public function getHLAllSymbol()
    {if (env('APP_V') == 'test') return $this->successWithData([]);
        $records = $this->huoBiServer->getHLAllRecords();
        return $this->successWithData($records);
    }

    //k线图   //已缓存
    public function getKLine(Request $request)
    {if (env('APP_V') == 'test') return $this->successWithData([]);
        $records = $this->huoBiServer->getKLine($request->symbol,$request->period,$request->size);
        return $this->successWithData($records);
    }

    //单个交易对详情  已缓存
    public function getOneSymbolMerged(Request $request)
    {if (env('APP_V') == 'test') return $this->successWithData([]);
        $record = $this->huoBiServer->getOneMerged($request->symbol);
        return $this->successWithData($record);
    }

    //深度图   已缓存
    public function getSymbolDepth(Request $request)
    {if (env('APP_V') == 'test') return $this->successWithData([]);
        $records = $this->huoBiServer->getSymbolDepth($request->symbol,$request->type);
        return $this->successWithData($records);

    }

    //最近成交
    public function getHistoryTrade(Request $request)
    {if (env('APP_V') == 'test') return $this->successWithData([]);
        $records = $this->huoBiServer->getHistoryTrade($request->symbol,$request->size);
        return $this->successWithData($records);
    }

    //币种介绍
    public function getCoinDes(Request $request)
    {
        $record = DB::table('coin_des')->where(['coin_symbol'=>$request->coin_name])->first();
        return $this->successWithData($record);

    }



}