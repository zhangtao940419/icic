<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Model\Admin\adminUser;
use App\Model\CoinFees;
use App\Model\CoinType;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CoinFeesController extends Controller
{
    use RedisTool;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rates = CoinFees::paginate();

        return view('admin.coinfees.index', compact('rates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CoinFees $coinfee,adminUser $adminUser)
    {
        $coins = CoinType::all();

        $adminPhone = $adminUser->where('username','admin')->first()->phone;
        $adminPhone = substr($adminPhone,0,3) . '****' . substr($adminPhone,7,4);

        return view('admin.coinfees.create_or_edit', compact('coinfee', 'coins','adminPhone'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,adminUser $adminUser)
    {
        $this->validate($request, [
            'fixed_fee' => 'required',
            'percent_fee' => 'required',
            'coin_id' => 'required|unique:coin_fees',
            'fee_type' => 'required',
            'withdraw_min' => 'required',
            'withdraw_max' => 'required',
            'recharge_min' => 'required',
            'code' => 'required'
        ]);
        $aUser = $adminUser->where('username','admin')->first();

        if (! $result = $this->checkCode('HT'.$aUser->phone,$request->code)){
            return back()->with('danger', '请重新发送验证码');//请重新发送验证码
        }else if ($result != 1){
            return back()->with('danger', '验证码错误');//错误
        }

        CoinFees::create($request->all());

        return redirect()->route('coinfees.index')->with('success', '操作成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CoinFees $coinfee,adminUser $adminUser)
    {
        $coins = CoinType::all();

        $adminPhone = $adminUser->where('username','admin')->first()->phone;
        $adminPhone = substr($adminPhone,0,3) . '****' . substr($adminPhone,7,4);

        return view('admin.coinfees.create_or_edit', compact('coinfee', 'coins','adminPhone'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CoinFees $coinfee,adminUser $adminUser)
    {
        $this->validate($request, [
            'fixed_fee' => 'required',
            'percent_fee' => 'required',
            'coin_id' => 'required',
            'fee_type' => 'required',
            'code' => 'required',
            'ore_pool_min' => 'required'
        ]);
        $aUser = $adminUser->where('username','admin')->first();

        if (! $result = $this->checkCode('HT'.$aUser->phone,$request->code)){
            return back()->with('danger', '请重新发送验证码');//请重新发送验证码
        }else if ($result != 1){
            return back()->with('danger', '验证码错误');//错误
        }
        $data = $request->except('code');

        $coinfee->update($data);
        $this->redisDelete('HT'.$aUser->phone);
        event(new AdminUserBehavior(auth('web')->user()->id,"修改货币费率",'货币费率'));

        return redirect()->route('coinfees.index')->with('success', '操作成功');
    }

    //提币开关
    public function takeSwitch($id)
    {
        $coinfees = CoinFees::findOrFail($id);

        if ($coinfees->withdraw_on_off_status) {
            event(new AdminUserBehavior(auth('web')->user()->id,"关闭提币开关:$coinfees->coin_id",'充提开关'));
            $coinfees->withdraw_on_off_status = 0;
        } else {
            event(new AdminUserBehavior(auth('web')->user()->id,"开启提币开关:$coinfees->coin_id",'充提开关'));
            $coinfees->withdraw_on_off_status = 1;
        }

        $coinfees->save();

        return back()->with('success', '操作成功');
    }

    public function putSwitch($id)
    {
        $coinfees = CoinFees::findOrFail($id);

        if ($coinfees->recharge_on_off_status) {
            event(new AdminUserBehavior(auth('web')->user()->id,"关闭充值开关:$coinfees->coin_id",'充提开关'));
            $coinfees->recharge_on_off_status = 0;
        } else {
            event(new AdminUserBehavior(auth('web')->user()->id,"开启充值开关:$coinfees->coin_id",'充提开关'));
            $coinfees->recharge_on_off_status = 1;
        }

        $coinfees->save();

        return back()->with('success', '操作成功');
    }


    //提币到chat开关
    public function toChatSwitch($id)
    {
        $coinfees = CoinFees::findOrFail($id);

        if ($coinfees->to_chat_switch) {
            event(new AdminUserBehavior(auth('web')->user()->id,"关闭提币到chat开关:$coinfees->coin_id",'充提开关'));
            $coinfees->to_chat_switch = 0;
        } else {
            event(new AdminUserBehavior(auth('web')->user()->id,"开启提币到chat开关:$coinfees->coin_id",'充提开关'));
            $coinfees->to_chat_switch = 1;
        }

        $coinfees->save();

        return back()->with('success', '操作成功');
    }





}
