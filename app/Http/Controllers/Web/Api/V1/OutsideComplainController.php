<?php

namespace App\Http\Controllers\Web\Api\V1;


use  Illuminate\Http\Request;
use  App\Http\Controllers\Web\BaseController;
use  App\Server\OutsideComplainServer\OutsideComplain;
use  App\Traits\Tools;

class OutsideComplainController extends BaseController
{
    use Tools;
    private $outsideComplain;

    public function __construct(OutsideComplain $outsideComplain)
    {

        $this->outsideComplain = $outsideComplain;
    }


    /* 会员投诉表入库
     *  @param Request $request
     *  param:
     *  Request $request
     *  @return \Illuminate\Http\JsonResponse
     */
    public function saveComplain(Request $request){

        if ($result = $this->verifyField($request->all(),[
            'order_number' => 'required|string',
            'complain_title' => 'required|string',
            'user_id' => 'required|integer',
            'complain_content' => 'required|string',
            'complain_img' => 'required|array',
            'complain_img.*' => 'required|image',
        ])) return response()->json($result);
        //return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
        $complain = $request->all();
      //  dd($complain);
        $result=  $this->outsideComplain->saveComplain($complain);
        if($result===-1)
            return response()->json(['message' => '投诉失败，请联系管理员或者客服!', 'status_code' => self::STATUS_COMPLAIN_ERROR]);
        return response()->json(['message' => '投诉成功，请耐心等待处理结果！', 'status_code' => self::STATUS_CODE_SUCCESS]);

    }








}