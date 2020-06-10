<?php

namespace App\Http\Controllers\Admin;

use App\Model\StoCoinStage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Server\StoServers\Admin\StoServer;
use App\Model\CoinType;

class StoStageController extends Controller
{


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

               $stoStageList =  $this->stoServer->getStoStageList($request->data_id);
               $data_id = $request->data_id;
               $i=1;
               return view('admin.stostage.index',compact('stoStageList','i','data_id'));
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
            'issue_begin_time' => 'required|',
            'start_time' => 'required|',
            'end_time' => 'required|',
        ]);
        $this->stoServer->addStoStageData($request->all());
        $stoStageList =  $this->stoServer->getStoStageList($request->data_id);
        $data_id = $request->data_id;
        $i=1;
        return redirect('/admin/stoStage?data_id='.$request->data_id);
//        return view('admin.stostage.index',compact('stoStageList','i','data_id'));
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
       $stoStage =  $this->stoServer->getStoStageById($id)[0];

        $coin_data =  $this->stoServer->getDataById($stoStage['data_id'])[0]['get_coin_names'];
        $base_coin_data =  $this->stoServer->getDataById($stoStage['data_id'])[0]['get_base_coin_names'];
        $data_id = $stoStage['data_id'];

        return view('admin.stostage.edit',compact('stoStage','coin_data','data_id','base_coin_data'));
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
            'base_coin_id' => 'required|integer|min:1',
            'exchange_coin_id' => 'required|integer',
            'exchange_rate' => 'required|numeric|min:0.001',
            'stage_issue_number' => 'required|numeric|min:1',
            'issue_time' => 'required|',
            'issue_begin_time' => 'required|',
            'data_id' => 'required|integer',
            'start_time' => 'required|',
            'end_time' => 'required|',
        ]);
        $stoStage = $request->all();
        $stoCount =$this->stoServer->getDataById($stoStage['data_id'])[0]['total_coin_issuance'];
        $stoStageData = $this->stoServer->getStoStageById($id)[0];
        $stoStageCount =$this->stoServer->getStoStageIssueCount($id);
        if(($stoStageCount-$stoStageData['stage_issue_number']+$stoStage['stage_issue_number'])<=$stoCount){
              $this->stoServer->updateStoStage($stoStage,$id);

        }
        $stoStageList =  $this->stoServer->getStoStageList($request->data_id);
        $data_id = $request->data_id;
        $i=1;
        return view('admin.stostage.index',compact('stoStageList','i','data_id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,StoCoinStage $stoCoinStage)
    {
//       dd($id);
       $stage = $stoCoinStage->find($id);
       if ($stage->issue_begin_time < time()){
           exit('当前阶段不可删除!');
       }

       foreach ($stage->sto_coin_stage_day as $item){
           $item->delete();
       }
       $stage->delete();

       return back();
//       exit('删除成功,请刷新页面');

//        return api_response()->success();
    }
}
