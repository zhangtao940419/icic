<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/20
 * Time: 11:34
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Http\Controllers\Controller;
use App\Logic\ContractLogic;
use App\Traits\Tools;
use Illuminate\Http\Request;


class ContractController extends Controller
{

    use Tools;

    protected $logic;



    public function __construct(ContractLogic $contractLogic)
    {

        $this->logic = $contractLogic;

    }


    public function getContractMsg()
    {

        return $this->logic->getContractMsg();

    }


    //盘面
    public function getContractMarket(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'activity_id' => 'required|integer'
        ])) return $vr;


        return $this->logic->getContractMarket($request->activity_id);
    }


    //购买
    public function buy(Request $request)
    {

        if ($vr = $this->verifyField($request->all(),[
            'activity_id' => 'required|integer',
            'amount' => 'required|numeric',
            'type' => 'required|integer|in:1,2,3'//1开多2平仓3开空
        ])) return $vr;

        return $this->logic->buy($request->activity_id,$request->amount,$request->type);
    }


    //购买记录
    public function getBuyRecords()
    {


        return $this->logic->getBuyRecords();


    }




}