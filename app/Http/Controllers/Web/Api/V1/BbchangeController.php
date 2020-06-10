<?php

namespace App\Http\Controllers\Web\Api\V1;

use App\Http\Controllers\Web\BaseController;
use App\Model\CoinType;
use Illuminate\Http\Request;
use App\Traits\Tools;

class BbchangeController extends BaseController
{
    use Tools;
    public function getChanges(Request $request, $base_coin_name, $exchange_coin_name, $time)
    {
        $prefix = $time / 60;

        $table = strtolower("time_sharing_" . $prefix . "_" . $base_coin_name . "_" . $exchange_coin_name);

        $data = \DB::table($table)->orderBy('deal_time', 'desc')->paginate(100)->toArray();

        return response()->json(['status_code' => self::STATUS_CODE_SUCCESS, 'message' => '查询成功', 'data' => $data]);
    }
}
