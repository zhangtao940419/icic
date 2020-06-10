<?php

namespace App\Http\Controllers\Admin;

use App\Model\StoCoinStageDay;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Server\StoServers\Admin\StoServer;
use App\Model\CoinType;

class StoStageDayController extends Controller
{
    use RedisTool;


    protected $stoServer =null;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

      public function __construct(StoServer $stoServer)
      {
          $this->stoServer =$stoServer;
      }

    public function index(Request $request)
    {

               $stoStageDayList =  $this->stoServer->getStoStageDayList($request->stage_id);

               return view('admin.stostageday.index',compact('stoStageDayList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $this->validate($request, [
            'data_id' => 'required|integer',
        ]);
         $coin_data =  $this->stoServer->getDataById($request->data_id)[0]['get_coin_names'];
         $base_coin_data =  $this->stoServer->getDataById($request->data_id)[0]['get_base_coin_names'];
         $data_id = $request->data_id;
         $coin =  (new CoinType)->getAllCoinType();
         foreach($coin as $key =>$value){
             if($coin_data['coin_id'] == $value['coin_id'])
             {  unset($coin[$key]);break;}
         }

        return view('admin.stostage.create',compact('coin','coin_data','data_id','base_coin_data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'base_coin_id' => 'required|integer|min:1',
            'exchange_coin_id' => 'required|integer',
            'exchange_rate' => 'required|numeric|min:0.001',
            'stage_issue_number' => 'required|numeric|min:1',
            'issue_time' => 'required|',
            'data_id' => 'required|integer',
            'start_time' => 'required|',
            'end_time' => 'required|',
        ]);
        $this->stoServer->addStoStageData($request->all());
        $stoStageList =  $this->stoServer->getStoStageList($request->data_id);
        $data_id = $request->data_id;
        $i=1;
        return view('admin.stostage.index',compact('stoStageList','i','data_id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stoStageDay = $this->stoServer->getStoStageDayById($id)[0];
        $coin_data =  $this->stoServer->getDataById($stoStageDay['data_id'])[0]['get_coin_names'];
        $base_coin_data =  $this->stoServer->getDataById($stoStageDay['data_id'])[0]['get_base_coin_names'];


        return view('admin.stostageday.edit',compact('stoStageDay','coin_data','base_coin_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'stage_issue_number' => 'required|numeric|min:1',
        ]);
        $stoStageDay = $request->all();
       $stoStageCount =$this->stoServer->getStoStageIssueCount($request->stage_id);
       $stoStageDayCount =$this->stoServer->getStoStageDayIssueCount($request->stage_id);

        if(($stoStageDayCount+$stoStageDay['stage_issue_number'])<=$stoStageCount){
            $this->stoServer->updateStoStageDay($stoStageDay,$id);
        }
        $stoStageDayList =  $this->stoServer->getStoStageDayList($request->stage_id);

        return view('admin.stostageday.index',compact('stoStageDayList'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    //设置页面
    public function setting($id)
    {
        $jd_switch = $this->stringGet('jd_switch_'.$id);
        $xn_switch = $this->stringGet('xn_switch_'.$id);

        $order_num = $xn_switch == null ? 0 : $xn_switch;

        $jd_switch = $jd_switch == null ? 0 : 1;
        $xn_switch = $xn_switch == null ? 0 : 1;

        $day = StoCoinStageDay::find($id);
        $special_user  =  $day->is_special_user;


        return view('admin.stostageday.setting',compact('id','jd_switch','xn_switch','order_num','special_user'));



    }

    //更新设置
    public function update_setting($id,Request $request)
    {
        $this->validate($request,[
            'order_num' => 'integer|min:0'
        ]);


//        dd(\request()->all());

        $jd_switch = $this->stringGet('jd_switch_'.$id);
        $xn_switch = $this->stringGet('xn_switch_'.$id);//dd($xn_switch);

        $day = StoCoinStageDay::find($id);

        if ($request->type == 1){
            if ($request->jd_switch){
                if ($jd_switch) return back()->with('success','操作成功');
                $re = $this->stringSet('jd_switch_'.$id,'1');//dd($re);
                return back()->with('success','操作成功');

            }else{//dd($jd_switch);
                if (!$jd_switch) return back()->with('success','操作成功');
                $this->redisDelete('jd_switch_'.$id);
                return back()->with('success','操作成功');

            }

        }elseif ($request->type == 2){
            if ($request->xn_switch){
                if ($request->order_num <= 0) return back()->with('danger','请输入订单数');

                $this->stringSet('xn_switch_'.$id,(string)$request->order_num);
                return back()->with('success','操作成功');
            }else{

                if (!$xn_switch) return back()->with('success','操作成功');
                $this->redisDelete('xn_switch_'.$id);
                return back()->with('success','操作成功');
            }

        }elseif ($request->type == 3){//dd($request->special_user);
            if ($request->special_user){
                $day->update(['is_special_user'=>1]);
                return back()->with('success','操作成功');
            }else{

                $day->update(['is_special_user'=>0]);
                return back()->with('success','操作成功');
            }

        }



    }






}
