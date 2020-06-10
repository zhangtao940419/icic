<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/7
 * Time: 16:05
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\CoinType;
use App\Model\Settings;
use App\Model\SettingTJReward;
use App\Model\SettingTopAuthReward;
use App\Traits\Tools;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    use Tools;


    //杂项设置
    public function index(Settings $settings)
    {

        $about_us = $settings->getSetting('about_us');

        $privacy_policy = $settings->getSetting('privacy_policy');

        return view('admin.setting.index',compact('about_us','privacy_policy'));





    }

    public function update(Request $request,Settings $settings)
    {

        $data = $request->except('_token');

        foreach ($data as $k=>$datum){
            $settings->updateOne($k,$datum);
        }

        return back()->with('success','操作成功');




    }


    public function reward_setting(CoinType $coinType,SettingTopAuthReward $settingTopAuthReward,SettingTJReward $settingTJReward)
    {
        $coins = $coinType->all();

        $topRs = $settingTopAuthReward->all();

        $tjRs = $settingTJReward->get()->groupBy('s_number');//dd($tjRs);

        return view('admin.setting.reward_index',compact('coins','topRs','tjRs'));

    }


    public function reward_setting_update(Request $request,SettingTopAuthReward $settingTopAuthReward,SettingTJReward $settingTJReward)
    {

        if ($request->type == 1){
            if ($request->id){
                if ($this->verifyField($request->all(),[
                    'number' => 'numeric'
                ])) return back()->with('error','请输入数字');
                $topR = $settingTopAuthReward->find($request->id);//dd($request->except('_token','type'));
                $topR->update($request->except('_token','type','id'));
                return back()->with('success','操作成功');
            }

            $settingTopAuthReward->insert($request->except('_token','type'));
            return back()->with('success','操作成功');
        }else{
            if ($request->id){
                if ($this->verifyField($request->all(),[
                    'reward_number' => 'numeric',
                    'start_time' => 'required',
                    'end_time' => 'required'
                ])) return back()->with('error','请输入数字');

                $updateData = $request->except('_token','type','id');
                $updateData['start_time'] .= ' 01:00:00';
                $updateData['end_time'] .= ' 23:59:00';
//                dd($updateData);
                $tjR = $settingTJReward->find($request->id);//dd($request->except('_token','type'));
                $tjR->update($updateData);
                return back()->with('success','操作成功');
            }


            $data = $request->except('_token','type');
            $data['start_time'] .= ' 01:00:00';
            $data['end_time'] .= ' 23:59:00';

            $settingTJReward->create($data);
            return back()->with('success','操作成功');





        }











    }






}