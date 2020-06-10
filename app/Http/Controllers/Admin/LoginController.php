<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Handlers\Distribution;
use App\Jobs\ComputeCommission;
use App\Model\Admin\adminUser;
use App\Model\User;
use App\Traits\RedisTool;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    use RedisTool;
    //登陆视图
    public function loginview()
    {
        return view('admin.login');
    }

    //极验证视图
    public function captcha()
    {
        $captcha = new \Laravist\GeeCaptcha\GeeCaptcha(env('CAPTCHA_ID'), env('PRIVATE_KEY'));

        return $captcha->GTServerIsNormal();
    }

    public function login(Request $request,adminUser $adminUser)
    {
        //验证极验证
//        $captcha = new \Laravist\GeeCaptcha\GeeCaptcha(env('CAPTCHA_ID'), env('PRIVATE_KEY'));
//        if ($captcha->isFromGTServer()) {
            if(1){
                $this->validate($request, [
                    'username' => 'required',
                    'password' => 'required',

                ]);
                $user = $adminUser->where('username', $request->username)->first();
                if (env('APP_V') == 'zs') {


                    if (!$user) return ['status_code' => 400, 'message' => '用户不存在'];

                    if (!$result = $this->checkCode('HTDL' . $user->phone, $request->code)) {
                        return back()->with('danger', '请重新发送验证码');//请重新发送验证码
                    } else if ($result != 1) {
                        return back()->with('danger', '验证码错误');//错误
                    }
                }

                if (Auth::guard('web')->attempt(['username' => $request->username, 'password' => $request->password])) {
                    $this->redisDelete('HTDL'.$user->phone);
                    event(new AdminUserBehavior(auth('web')->user()->id,"后台用户{$request->username}登录",'后台用户登录'));
                    return redirect()->intended('/admin');
                } else {
                    return back()->with('danger', '用户名或密码错误');
                }
            } else {
                return back()->with('danger', '验证码异常');
            }
//        }
    }

    //首页重定向到下载页面
    public function download()
    {
        return redirect()->route('download');
    }

    public function logout()
    {

        Auth::guard('web')->logout();

        return redirect('/admin/login')->with('success', '退出成功');

//        $name = '1809064764@qq.com';
//
//        $matchingUsers = User::search($name)->get();

//        $res = User::findOrFail(1065)->userWallet->where('coin_id', 9)->first();
//        dd($res);

//        dd($matchingUsers);
    }

    public function sendCodeSMS(Request $request,adminUser $adminUser)
    {
        $userName = $request->username;
        $user = $adminUser->where('username',$userName)->first();
        //$key = 'HTDL' . $user->phone;
        if (!$user) return ['status_code'=>400,'message'=>'用户不存在'];
        $key = 'HT' . $user->phone;

        $code = rand(100000,999999);
        $msg = '您的验证码为'.$code.'请在5分钟内输入。感谢您对TTS的支持，祝您生活愉快！';
        if ($request->czuser || $request->des){
            $msg = "尊敬的后台用户，您的短信验证码为{$code}，5分钟内有效，后台用户{$request->czuser}正在{$request->des}操作，请您确认。";
            $key = 'HT' . $user->phone;
        }
        if ($request->type == 'login') $key = 'HTDL' . $user->phone;

        if ($this->redisExists($key)) return ['status_code'=>400,'message'=>'请勿重复发送'];//重复发送
//dd($msg);
//        $msg = '亲爱的用户，您的短信验证码为1164，在10分钟内有效，若非本人操作请忽略。';
        //$re = app('sms')->setSignature('TTS')->sendVariableMsg($msg,'15574832499,tts,15855');
        $re = app('sms')->setSignature('TTS')->send($user->phone,$msg);

        if (
            //
            $re
            && $this->stringSetex($key,300,"{$code}")
        ) return ['status_code'=>200,'message'=>'发送成功'];//成功
        return ['status_code'=>400,'message'=>'发送失败'];//失败

    }

}
