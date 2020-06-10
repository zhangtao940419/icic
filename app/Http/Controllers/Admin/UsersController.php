<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Handlers\ExcelHtml;
use App\Model\Admin\adminUser;
use App\Model\User;
use App\Model\BankCardVerify;
use App\Model\UserQuestion;
use App\Model\UserQuestionType;
use App\Notifications\AdminNotification;
use App\Notifications\AdminWarningNotification;
use App\Traits\QiNiuFileTool;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\ModelFilters\UserFilter;

class UsersController extends Controller
{
    use RedisTool,QiNiuFileTool;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,adminUser $adminUser)
    {

        $builder = User::filter($request->all(), UserFilter::class);
        if ($request->excel) return $this->outExcel($builder->with('userIdentify')->get());

        $search = array_merge($request->all(), ['count' => $builder->count()]);

        $users = $builder->with(['userIdentify','p_user'])->latest()->paginate();

        $adminPhone = $adminUser->where('username','admin')->first()->phone;
        $adminPhone = substr($adminPhone,0,3) . '****' . substr($adminPhone,7,4);
        $excel = $request->fullUrl().'&excel=1';
        if (strpos($request->fullUrl(),'?') === false) $excel = $request->fullUrl().'?excel=1';

        return view('admin.users.index', compact('users', 'search','adminPhone','excel'));
    }

    public function s_user($userId)
    {
        $sUsers = User::query()->with(['userIdentify'])->where(['pid'=>$userId])->paginate();


        return view('admin.users.s_user', compact('sUsers'));
    }

    public function outExcel($users)
    {
            $header = ['用户id','会员电话','真实姓名','身份证','认证等级','是否特殊用户','是否商家','状态','注册时间'];
            $list = [];
            foreach($users as $user){
                $name = $user->userIdentify ? $user->userIdentify->identify_name : '-';
                $card = $user->userIdentify ? $user->userIdentify->identify_card : '-';
                $auth = ['未认证','初级认证','高级认证'][$user->user_auth_level];
                $type1 = ['普通','特殊用户'][$user->is_special_user];
                $type2 = ['普通','商家'][$user->is_business];
                $status = ['冻结','正常'][$user->is_frozen];
                $list[] = [$user->user_id,$user->user_phone,$name,"".$card,$auth,$type1,$type2,$status,$user->created_at];
            }
        event(new AdminUserBehavior(auth('web')->user()->id,"导出excel:tts_用户信息表",'导出excel'));
            return (new ExcelHtml())->ExcelPull('tts_用户信息表',$header,$list);

    }


    //标记特殊用户
    public function changeUser(User $user,Request $request,adminUser $adminUser)
    {

        $this->validate($request, [
            'code' => 'required|min:1'
        ]);
        $aUser = $adminUser->where('username','admin')->first();

        if (! $result = $this->checkCode('HT'.$aUser->phone,$request->code)){
            return back()->with('danger', '请重新发送验证码');//请重新发送验证码
        }else if ($result != 1){
            return back()->with('danger', '验证码错误');//错误
        }

        if ($user->is_special_user) {
            $user->update(['is_special_user' => 0]);
        } else {
            $user->update(['is_special_user' => 1]);
        }

        $user->save();
        $this->redisDelete('HT'.$aUser->phone);
        event(new AdminUserBehavior(auth('web')->user()->id,"标记特殊用户",'标记特殊用户',$user->user_id));

        return back()->with('success', '操作成功');
    }
    //标记内部用户
    public function changeInsideUser(User $user,Request $request,adminUser $adminUser)
    {

        $this->validate($request, [
            'code' => 'required|min:1'
        ]);
        $aUser = $adminUser->where('username','admin')->first();

        if (! $result = $this->checkCode('HT'.$aUser->phone,$request->code)){
            return back()->with('danger', '请重新发送验证码');//请重新发送验证码
        }else if ($result != 1){
            return back()->with('danger', '验证码错误');//错误
        }

        if ($user->is_inside_user) {
            $user->update(['is_inside_user' => 0]);
            $behavior = '标记取消内部用户';
        } else {
            $user->update(['is_inside_user' => 1]);
            $behavior = '标记内部用户';
        }

        $user->save();
        $this->redisDelete('HT'.$aUser->phone);
        event(new AdminUserBehavior(auth('web')->user()->id,$behavior,'标记内部用户',$user->user_id));

        return back()->with('success', '操作成功');
    }

    //标记取消长时未入金用户
    public function removeLongtimeStatus(User $user,Request $request)
    {

            $user->update(['c2c_long_time_not_buy_status' => 0]);
            $behavior = '标记取消长时未入金用户';

        event(new AdminUserBehavior(auth('web')->user()->id,$behavior,'标记取消长时未入金用户',$user->user_id));

        return back()->with('success', '操作成功');
    }

    public function changeAllInsideUser(User $user)
    {
        //dd((new User())->where('is_usable',1)->update(['is_inside_user' => 1]));
        if ($user->first()->is_inside_user == 1){
            (new User())->where('is_usable',1)->update(['is_inside_user' => 0]);
            $behavior = '一键取消内部用户';
        }else{
            (new User())->where('is_usable',1)->update(['is_inside_user' => 1]);
            $behavior = '一键标记内部用户';
        }

        event(new AdminUserBehavior(auth('web')->user()->id,$behavior,'标记内部用户',0));

        return back()->with('success', '操作成功');


    }

    //标记sto特殊用户
    public function changeSTOSpecialUser(User $user,Request $request,adminUser $adminUser)
    {

        $this->validate($request, [
            'code' => 'required|min:1'
        ]);
        $aUser = $adminUser->where('username','admin')->first();

        if (! $result = $this->checkCode('HT'.$aUser->phone,$request->code)){
            return back()->with('danger', '请重新发送验证码');//请重新发送验证码
        }else if ($result != 1){
            return back()->with('danger', '验证码错误');//错误
        }

        if ($user->is_sto_special_user) {
            $user->update(['is_sto_special_user' => 0]);
            $behavior = '标记取消sto特殊用户';
        } else {
            $user->update(['is_sto_special_user' => 1]);
            $behavior = '标记sto特殊用户';
        }

        $user->save();
        $this->redisDelete('HT'.$aUser->phone);
        event(new AdminUserBehavior(auth('web')->user()->id,$behavior,'标记sto特殊用户,user_id:'.$user->user_id,$user->user_id));

        return back()->with('success', '操作成功');



    }


    //标记为商家
    public function changeBusiness(User $user,Request $request,adminUser $adminUser)
    {
        $this->validate($request, [
            'code' => 'required|min:1'
        ]);
        $aUser = $adminUser->where('username','admin')->first();

        if (! $result = $this->checkCode('HT'.$aUser->phone,$request->code)){
            return back()->with('danger', '请重新发送验证码');//请重新发送验证码
        }else if ($result != 1){
            return back()->with('danger', '验证码错误');//错误
        }

        if(User::where('user_id', $user->user_id)->value('user_auth_level') !==2)
                         return back()->with('message', "该用户还未进行高级验证，不能标记为商家");
        if(!BankCardVerify::where('user_id', $user->user_id)->count('user_id'))
            return back()->with('success', "该用户还未进行银行卡绑定，不能标记为商家");
        if ($user->is_business) {
            DB::transaction(function () use($user) {
                $user->is_business = 0;
                \DB::table('business')->where('user_id', $user->user_id)->delete();
            });
        } else {
            DB::transaction(function () use($user) {
                $user->is_business = 1;
                \DB::table('business')->insert(['user_id' => $user->user_id]);
            });
        }

        $user->save();
        $this->redisDelete('HT'.$aUser->phone);
        event(new AdminUserBehavior(auth('web')->user()->id,"标记商家",'标记商家',$user->user_id));

        return back()->with('success', '操作成功');
    }

    //冻结用户
    public function FrozenUser(User $user)
    {
        if ($user->is_frozen) {
            $des = "冻结用户,用户id:{$user->user_id},用户手机:{$user->user_phone}";
            $user->is_frozen = 0;
        } else {
            $des = "解冻结用户,用户id:{$user->user_id},用户手机:{$user->user_phone}";

            $user->is_frozen = 1;
        }

        $user->save();
        event(new AdminUserBehavior(auth('web')->user()->id,"{$des}",'冻结用户',$user->user_id));

        return back()->with('success', '操作成功');
    }

    public function show(User $user)
    {
//        dd($user->userWallet);
        return view('admin.users.show', compact('user'));
    }


    //用户提问管理
    public function userQuestion(Request $request,UserQuestion $userQuestion,UserQuestionType $userQuestionType)
    {

        $query = UserQuestion::query()->with(['user.userIdentify','type']);
        if ($request->username){
            $query->whereHas('user',function ($q) use($request){
                $q->where(['user_phone' => $request->username]);
            });
        }
        if ($request->type_id){
            $query->where('type_id',$request->type_id);
        }
        if ($request->status !== null){
            $query->where('status',$request->status);
        }


        $records = $query->orderBy('status')->latest('id')->paginate();

        $types = $userQuestionType->getAllTypes();

        return view('admin.users.user_question',compact('records','types'));

    }

    public function questionDetail($id,UserQuestion $userQuestion)
    {
        $record = $userQuestion->with(['user','type'])->find($id);


        return view('admin.users.show_question',compact('record'));

    }

    public function answer($id,Request $request,UserQuestion $userQuestion)
    {


        $answer = $request->answer;
        if ($answer == null || $answer === '') return back()->with('danger','请输入内容');

        $data = ['status'=>1,'answer'=>$answer];

        if ($request->a_image1){
            $path = $this->qiniuuploadSingleImg($request->a_image1,'user_question');
            if ($path !== false) $data['a_image1'] = $path;
        }
        if ($request->a_image2){
            $path = $this->qiniuuploadSingleImg($request->a_image2,'user_question');
            if ($path !== false) $data['a_image2'] = $path;
        }
        if ($request->a_image3){
            $path = $this->qiniuuploadSingleImg($request->a_image3,'user_question');
            if ($path !== false) $data['a_image3'] = $path;
        }

        $userQuestion->where(['id'=>$id])->update($data);

        return back()->with('success','回复成功');

    }


    /////////
    /// 问题类型管理
    public function question_type_index(UserQuestionType $userQuestionType)
    {
        $records = $userQuestionType->paginate();//dd($records);

        return view('admin.question.index',compact('records'));

    }

    public function question_type_detail($id,UserQuestionType $userQuestionType)
    {
        $record = $userQuestionType->find($id);

        return view('admin.question.detail',compact('record'));

    }
    public function question_type_update($id,UserQuestionType $userQuestionType,Request $request)
    {
        $this->validate($request, [
            'type' => 'required|string|min:1'
        ]);
        $record = $userQuestionType->find($id);

        $record->update(['type' => $request->type]);

        return redirect('admin/question_type_index')->with('success','操作成功');

    }

    public function question_type_add()
    {
        return view('admin.question.add');

    }
    public function question_type_add_store(UserQuestionType $userQuestionType,Request $request)
    {
        $this->validate($request, [
            'type' => 'required|string|min:1'
        ]);
        $userQuestionType->insertOne($request->type);

        return redirect('admin/question_type_index')->with('success','操作成功');

    }

    public function question_type_delete($id,UserQuestionType $userQuestionType)
    {
        $record = $userQuestionType->find($id)->delete();

        return redirect('admin/question_type_index')->with('success','操作成功');

    }

    //给用户发送消息通知
    public function sendNotification(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|integer',
            'type' => 'required|integer|in:2,3',
            'contents' => 'required|string',
        ]);

        $user = User::query()->find($request->user_id);
        $type = $request->type;
        $message = [
            'contents' => $request->contents,
        ];

        if($type == 3){
            $user->notify(new AdminWarningNotification($message));
        }else{
            $user->notify(new AdminNotification($message));
        }

        return back()->with('success', '操作成功');
    }
}
