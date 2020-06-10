<?php

namespace App\Http\Controllers\Web\Api\V1;

use Illuminate\Http\Request;
use App\Model\worldArea;
use App\Model\coinType as coin;
use App\Http\Controllers\Web\BaseController;
use App\Traits\Tools;

class CoinTypeController extends BaseController
 {

     use Tools;

     private $coinType;
     private $worldArea;

     public function __construct(worldArea $worldArea,coin $coinType)
     {
          $this->worldArea =$worldArea;

          $this->coinType =$coinType;

     }

     /* 获取场外交易的币种和地区
      * @param Request $request
      *  param:void
      * @return \Illuminate\Http\JsonResponse
      */
     public function getCoinAndWorldArae(Request $request){

         $data['WorldArea']  =  $this->worldArea->getWorldArea();

         $data['CoinType'] =  $this->coinType->getAllCoinType();

         return response()->json(['message' => '获取数据成功!', 'data'=>$data,'status_code' => self::STATUS_CODE_SUCCESS]);

     }


}
