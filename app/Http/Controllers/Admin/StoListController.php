<?php

namespace App\Http\Controllers\Admin;

use App\Traits\QiNiuFileTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Server\StoServers\Admin\StoServer;
use App\Model\CoinType;
use App\Traits\FileTools;

class StoListController extends Controller
{
           use FileTools,QiNiuFileTool;

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

    public function index()
    {
              $stoList =  $this->stoServer->getAllStoList();

              return view('admin.stolist.index',compact('stoList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $coin =  (new CoinType)->getAllCoinType();

        return view('admin.stolist.create',compact('coin'));
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
            'coin_id' => 'required|integer',
            'base_coin_id' => 'required|integer',
            'total_coin_issuance' => 'required|numeric|min:1',
            'issue_coin_number' => 'required|numeric|min:1',
            'des' => 'required',
            'img' => 'required|image',
            'des_img' => 'required|image',
            'is_reward' => 'required',
            'white_paper' => 'required|file'
        ]);
        $sto = $request->all();
        $sto['img'] = '/app/sto/'.$this->putImage($sto['img'],'','Sto');
        $sto['des_img'] = $this->qiniuuploadSingleImg($sto['des_img'],'sto');
        $sto['white_paper'] = $this->qiniuupload($sto['white_paper'],'sto_wp_' . time() . '.pdf');

        if($request->coin_id != $request->base_coin_id ){
            $this->stoServer->addStoData($sto);
        }
        $stoList =  $this->stoServer->getAllStoList();
        //dd($stoList);
        return view('admin.stolist.index',compact('stoList'));
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
        $coin_data =  $this->stoServer->getDataById($id);

        return view('admin.stolist.edit',compact('coin_data'));

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
        'coin_id' => 'integer',
        'base_coin_id' => 'integer',
        'total_coin_issuance' => 'numeric|min:1',
        'issue_coin_number' => 'numeric|min:1',
        'des' => 'required',
        'img' => 'image',
        'des_img' => 'image',
         'white_paper' => 'file',
    ]);
      $sto =  $request->all();
      if($request->img){
          $sto['img'] = '/app/sto/'.$this->putImage($sto['img'],'','Sto');
      }
        if($request->des_img){
            $sto['des_img'] = $this->qiniuuploadSingleImg($sto['des_img'],'sto');
        }
        if($request->white_paper){
            $sto['white_paper'] = $this->qiniuupload($sto['white_paper'],'sto_wp_' . $id . time() . '.pdf');
        }
        $this->stoServer->updateStoList($sto,$id);
        $stoList =  $this->stoServer->getAllStoList();
        //dd($stoList);
        return back()->with('success','操作成功');
//        return redirect('admin/stolist')->with('stoList',$stoList);
//        return view('admin.stolist.index',compact('stoList'));
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
}
