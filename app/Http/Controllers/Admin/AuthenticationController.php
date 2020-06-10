<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Events\UserTopAuthBehavior;
use App\Model\UserIdentify;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthenticationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        if ($request->status) {
            $data = ['status' => $request->status];
        }

        if ($request->area){
            $data['identify_area_id'] = $request->area;
        }

        $userIdentifys = UserIdentify::where($data)->where('status', '!=', 0)->orderBy('status')->latest()->paginate();

        return view('admin.auth.index', compact('userIdentifys', 'data'));
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
            'identify_name' => 'required',
            'identify_card' => 'required',
            'identify_card_z_img' => 'required',
            'identify_card_f_img' => 'required',
            'identify_card_h_img' => 'required',
        ]);

        $result = UserIdentify::create($request->all());

        if ($result) {
            $result->user_Invitation_code = $result->user_id . str_random(3);

            $result->save();
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(UserIdentify $authentication)
    {
        return view('admin.auth.edit', compact('authentication'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserIdentify $authentication)
    {
        //$authentication->update($request->all());
        DB::transaction(function () use ($authentication) {
            DB::table('users')->where('user_id', $authentication->user_id)->update(['user_auth_level' => 2]);
            DB::table('users_identify')->where('user_id', $authentication->user_id)->update(['status' => 2]);
        });
        event(new UserTopAuthBehavior($authentication->user_id));
        event(new AdminUserBehavior(auth('web')->user()->id,"通过用户认证:用户id{$authentication->user_id}",'用户认证审核',$authentication->user_id));
        return redirect()->route('authentication.index')->with('success', '操作成功');
    }

    public function destroy(UserIdentify $authentication,Request $request)
    {
        if ($request->refuse_reason != 1){
            $reason = $request->refuse_reason;
        }else{
            $reason = $request->zdy_reason;
        }

        $authentication->status = 3;
        $authentication->refuse_reason = $reason;
        $authentication->save();
        event(new AdminUserBehavior(auth('web')->user()->id,"拒绝用户认证:用户id{$authentication->user_id},理由:{$reason}",'用户认证审核',$authentication->user_id));
        return redirect()->route('authentication.index')->with('success', '操作成功');
    }
}
