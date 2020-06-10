<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\ExcelHtml;
use App\Model\Admin\adminUser;
use App\Model\Admin\AdminWalletFlow;
use App\Model\Admin\Article;
use App\Model\Admin\Permission;
use App\Model\CoinType;
use App\Model\User;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\AdminUserBehavior;


class AdminUserController extends Controller
{
    use RedisTool;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = adminUser::paginate(15);

        return view('admin.adminusers.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(adminUser $adminuser)
    {
        $data = [];
        $permissions = Permission::all();

        $adminPhone = $adminuser->where('username','admin')->first()->phone;
        $adminPhone = substr($adminPhone,0,3) . '****' . substr($adminPhone,7,4);

        return view('admin.adminusers.create_or_edit', compact('adminuser', 'permissions', 'data','adminPhone'));
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
            'username' => 'required|unique:admin_users',
            'password' => 'required|min:6|max:20',
            'permission_id' => 'required',
            'phone' => 'required|min:11|max:11',
            'code' => 'required'
        ]);

        $user = $adminUser->where('username','admin')->first();

        if (! $result = $this->checkCode('HT'.$user->phone,$request->code)){
            return back()->with('danger', '请重新发送验证码');//请重新发送验证码
        }else if ($result != 1){
            return back()->with('danger', '验证码错误');//错误
        }

        $data = [
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'description' => $request->description,
            'avatar' => $request->avatar
        ];

        if ($res = adminUser::create($data)) {
            $res->permissions()->attach($request->permission_id);
        }
        $this->redisDelete('HT'.$user->phone);
        event(new AdminUserBehavior(auth('web')->user()->id,"创建后台用户:{$data['username']}",'创建后台用户'));
        return redirect()->route('adminuser.index')->with('success', '创建成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(adminUser $adminuser)
    {

        $data = [];
        foreach ($adminuser->permissions as $v) {
            $data[] = $v->pivot->permission_id;
        }

        $permissions = Permission::all();

        $adminPhone = $adminuser->where('username','admin')->first()->phone;
        $adminPhone = substr($adminPhone,0,3) . '****' . substr($adminPhone,7,4);

        return view('admin.adminusers.create_or_edit', compact('adminuser', 'permissions', 'data','adminPhone'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, adminUser $adminuser)
    {
        $this->validate($request, [
            'username' => 'required|min:2',
            'password' => 'required|min:6',
            'phone' => 'required|min:11|max:11',
            'code' => 'required'
        ]);

        $user = $adminuser->where('username','admin')->first();

        if (! $result = $this->checkCode('HT'.$user->phone,$request->code)){
            return back()->with('danger', '请重新发送验证码');//请重新发送验证码
        }else if ($result != 1){
            return back()->with('danger', '验证码错误');//错误
        }

        $data = $request->except('_method', '_token');
        if (strlen($request->password) == 60) {
            $data['password'] = $request->password;
        } else {
            $data['password'] = bcrypt($request->password);
        }

        if ($res = $adminuser->update($data)) {
            $adminuser->permissions()->sync($request->permission_id);
        }
        $this->redisDelete('HT'.$user->phone);
        event(new AdminUserBehavior(auth('web')->user()->id,"编辑后台用户:{$request->username}",'编辑后台用户'));
        return redirect()->route('adminuser.index')->with('success', '编辑成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(adminUser $adminuser)
    {
        $res = Article::where('user_id', $adminuser->id)->get()->toArray();

        if (!empty($res)) {
            return ['该管理员下还有文章，不能删除'];
        }

        if ($adminuser->delete()) {
            $adminuser->permissions()->detach();
            return [];
        }
    }

    public function adminUserBehavior(Request $request,\App\Model\Admin\AdminUserBehavior $adminUserBehavior)
    {
        $types = $adminUserBehavior->getTypes();//dd($types);



        $userphone = $request->input('user_phone');//dd($value);
        $type = $request->type;

//        $data = []
        $query = $adminUserBehavior->with('user');
        if ($userphone){
            if ($user = User::where('user_phone',$userphone)->first()){
                $query = $query->where('user_id',$user->user_id);
            }
        }
        if ($type){
            $query->where(['type_des'=>$type]);
        }

        $records = $query->latest()->paginate()->appends($request->all());

        return view('admin.behavior.index',compact('records','types'));
    }


    public function adminRechargeRecords(Request $request,AdminWalletFlow $adminWalletFlow,CoinType $coinType)
    {
        $query = $adminWalletFlow->with('user.userIdentify','admin_user','coin');

        $value = $request->input('value');
        if ($value){
            if ($user = adminUser::where('username',$value)->first()){
                $query = $query->where('admin_user_id',$user->id);
            }elseif ($re = User::where('user_phone',$value)->first()){
                $query = $query->where('user_id',$re->user_id);
            }
        }
        if ($request->coin_id){
            $query->where(['coin_id'=>$request->coin_id]);
        }
        if ($request->type){
            $query->where(['type'=>$request->type]);
        }
        if ($request->wallet_type){
            $query->where(['wallet_type'=>$request->wallet_type]);
        }

        if ($request->excel) return $this->outExcel($query->latest()->get());
        $coins = $coinType->where(['is_usable'=>1])->get();

        $total = $query->sum('amount');

        $records = $query->latest()->paginate()->appends($request->all());

        $excel = $request->fullUrl().'&excel=1';
        if (strpos($request->fullUrl(),'?') === false) $excel = $request->fullUrl().'?excel=1';

        return view('admin.behavior.admin_recharge_records',compact('records','coins','excel','total'));

    }

    public function outExcel($records)
    {
        $header = ['id','后台用户','用户手机','币种','类型','余额类型','数量','时间'];
        $list = [];
        foreach($records as $record){
            $list[] = [$record->id,$record->admin_user->username,$record->user->user_phone,$record->coin->coin_name,$record->type()[$record->type],$record->wallet_type()[$record->wallet_type],$record->amount,$record->created_at];
        }
        event(new AdminUserBehavior(auth('web')->user()->id,"导出excel:tts_后台充值记录表",'导出excel'));
        return (new ExcelHtml())->ExcelPull('tts_后台充值记录表',$header,$list);

    }

}
