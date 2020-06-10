<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Server\Investment\Dao\InvestmentTypeDao;
use App\Server\Investment\Dao\CoinTypeDao;


class InvestmentTypeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InvestmentTypeDao $investmentTypeDao)
    {

        $investmentType = $investmentTypeDao->getInvestType()->toArray();

        return view('admin.invest_type.index',compact('investmentType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CoinTypeDao $coin)
    {   $where =['is_usable'=>1,'is_invest'=>1];
        $coins = $coin->getRecordByCondition($where);
        return view('admin.invest_type.create',compact('coins'));
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
            'invest_type_name' => 'required|',
            'coin_id' => 'required|integer'
        ]);

        (new InvestmentTypeDao())->insertInvest($request->all());

        $investmentType = (new InvestmentTypeDao())->getInvestType()->toArray();

        return view('admin.invest_type.index',compact('investmentType'));
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
        //
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
        //
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
