<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\RedisTool;

class CoinBabDowloadController extends Controller
{
    use RedisTool;

    public function dowload(Request $request)
    {
        $invitationCode = $request->invitation_code ? $request->invitation_code : '';
        return view('admin.dowload.index',compact('invitationCode'));
    }

    //安卓上传apk包
    public function androidDownload(Request $request)
    {
        ini_set('post_max_size', 20);
        if ($request->isMethod('get')) {
            return view('admin.dowload.postfile');
        } elseif ($request->isMethod('post')) {
            if ($request->android) {
                $android = $request->file('android');
                //获取文件的原文件名 包括扩展名
                $ext = $android->getClientOriginalExtension();
                $filename = 'tts' . '.' . $ext;
                $folder_name = "android";
                $upload_path = public_path() . '/' . $folder_name;
                $android->move($upload_path, $filename);
            }

            if ($request->ios) {
                $ios = $request->file('ios');
                $folder_name = "ios";
                $ext = $ios->getClientOriginalExtension();
                $filename = 'CoinBAB(HK)' . '.' . $ext;
                $upload_path = public_path() . '/' . $folder_name;
                $ios->move($upload_path, $filename);
            }

            if ($request->android_version) {
                $key = 'android_version';
                $this->stringSet($key, $request->android_version);
            }

            if ($request->ios_version) {
                $key = 'ios_version';
                $this->stringSet($key, $request->ios_version);
            }
            event(new AdminUserBehavior(auth('web')->user()->id,"上传安装包",'上传安装包'));
            return back()->with('success', '操作成功');

        }
    }


    //安卓下载
    public function getfile()
    {
//        $name = env('APP_V') == 'test' ? 'tts_test.apk' : 'tts.apk';
        $path = public_path('android/tts.apk');
        return response()->download($path);
    }

    //IOS文件下载
    public function getIpa()
    {
        $path = public_path('ios/CoinBAB(HK).ipa');

        return response()->download($path);
    }

    //IOS文件下载
    public function getPlist()
    {
        $path = public_path('ios/CoinBAB.plist');

        return response()->download($path);
    }


}
