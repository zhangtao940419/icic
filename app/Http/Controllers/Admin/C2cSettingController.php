<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Model\C2CSetting;
use App\Model\CoinType;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class C2cSettingController extends Controller
{
    use RedisTool;
    public function index()
    {
        $c2csetting = C2CSetting::with('coin')->paginate();

        return view('admin.c2csetting.index', compact('c2csetting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(C2CSetting $c2csetting)
    {
        $coins = CoinType::all();

        return view('admin.c2csetting.create_or_edit', compact('c2csetting', 'coins'));
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
            'coin_id' => 'required|unique:c2c_setting',
            'buy_price' => 'required',
            'sell_price' => 'required'
        ]);

        $data = $request->all();

        foreach ($data as $k=>$v) {
            if (!empty($v)) {
                $newData[$k] =$v;
            }
        }

        C2CSetting::create($newData);

        return redirect()->route('c2csetting.index')->with('success', '操作成功');
    }


    public function show(C2CSetting $c2csetting)
    {
        return view('admin.c2csetting.show', compact('c2csetting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(C2CSetting $c2csetting)
    {
        $coins = CoinType::all();

        return view('admin.c2csetting.create_or_edit', compact('c2csetting', 'coins'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, C2CSetting $c2csetting)
    {
        $this->validate($request, [
            'coin_id' => 'required',
            'buy_price' => 'required',
            'sell_price' => 'required'
        ]);

        $c2csetting->update($request->all());
        event(new AdminUserBehavior(auth('web')->user()->id,"c2c设置修改",'c2c设置'));
        return redirect()->route('c2csetting.index')->with('success', '操作成功');
    }



    //卖出审核列表
    public function checkSwitch(C2CSetting $c2CSetting)
    {
        $switch = $this->stringGet('c2c_need_check_switch') ? 1 : 0;

        $num = $this->stringGet('c2c_need_check_num');
        $num = $num == false ? 500 : $num;

        $c2CSetting = $c2CSetting->getOneRecord();
        return view('admin.c2csetting.check_switch',compact('switch','num','c2CSetting'));


    }


    //卖出审核列表
    public function updateCheckSwitch(Request $request,C2CSetting $c2CSetting)
    {
        if ($request->type == 2){
            $this->stringSet('c2c_need_check_num',$request->low_number);
            $this->stringSet('c2c_need_check_switch',$request->switch);
            $des = $request->switch == 1 ? '开启' : '关闭';
            event(new AdminUserBehavior(auth('web')->user()->id,"c2c审核开关设置修改:卖单审核{$des}:num:{$request->low_number}",'c2c设置'));
            return redirect()->route('c2c_check_switch.index')->with('success', '操作成功');
        }else{
            $c2CSetting->first()->update(['buy_order_need_check_num'=>$request->low_number,'buy_order_check_switch'=>$request->switch]);
            $des = $request->switch == 1 ? '开启' : '关闭';
            event(new AdminUserBehavior(auth('web')->user()->id,"c2c审核开关设置修改:买单审核{$des}:num:{$request->low_number}",'c2c设置'));
            return redirect()->route('c2c_check_switch.index')->with('success', '操作成功');
        }


    }

}
