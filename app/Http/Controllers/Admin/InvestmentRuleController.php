<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Server\Investment\Dao\InvestmentRuleDao;
use App\Server\Investment\Dao\InvestmentTypeDao;
use App\Server\Investment\Dao\CoinTypeDao;

class InvestmentRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $where =['is_usable'=>1];

        $investmentRule = (new InvestmentRuleDao)->getAllRecords($where);

        return view('admin.invest_rule.index',compact('investmentRule'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $investmentType = (new InvestmentTypeDao)->getInvestType()->toArray();
        $where =['is_usable'=>1,'is_invest'=>1];
        $coins = (new CoinTypeDao)->getRecordByCondition($where);
        return view('admin.invest_rule.create',compact('investmentType','coins'));
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
            'invest_id' => 'required|integer',
            'coin_id' => 'required|integer',
            'invest_time' => 'required|integer',
            'rate_of_return_set' => 'required|integer|min:0|max:100'
        ]);

        (new InvestmentRuleDao)->insertInvestRule($request->all());

        $where =['is_usable'=>1];

        $investmentRule = (new InvestmentRuleDao)->getAllRecords($where);

        return view('admin.invest_rule.index',compact('investmentRule'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $investmentType = (new InvestmentTypeDao)->getInvestType()->toArray();
        $where =['is_usable'=>1,'is_invest'=>1];
        $coins = (new CoinTypeDao)->getRecordByCondition($where);
        $record = (new  InvestmentRuleDao())->getOneInvestRule($id)->toArray();
        $record['invest_time'] = $record['invest_time']/86400;
        return view('admin.invest_rule.edit',compact('investmentType','coins','record'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {       $this->validate($request, [
        'invest_id' => 'required|integer',
        'coin_id' => 'required|integer',
        'invest_time' => 'required|integer',
        'rate_of_return_set' => 'required|integer|min:0|max:100'
    ]);
        $where = ['type_id'=>$id];
        $data = $request->all();
        $data['invest_time'] = $data['invest_time']*86400;
        (new  InvestmentRuleDao())->updateInvestRule($where,$data);

        $where =['is_usable'=>1];

        $investmentRule = (new InvestmentRuleDao)->getAllRecords($where);

        return view('admin.invest_rule.index',compact('investmentRule'));


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($id>0){
            $where = ['type_id'=>$id];
            (new InvestmentRuleDao)->deleInvestRule($where);
        }
    }
}
