<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/29
 * Time: 17:34
 */

namespace App\Http\Controllers\Admin;


use App\Events\AdminUserBehavior;
use App\Http\Controllers\Controller;
use App\Traits\RedisTool;
use Illuminate\Http\Request;

class SmsProviderController extends Controller
{
    use RedisTool;

    private $key = 'sms_provider';

    public function index()
    {

        $provider = $this->stringGet($this->key) ? $this->stringGet($this->key):1;
        return view('admin.smsProvider.index',compact('provider'));

    }

    public function update(Request $request)
    {

        $this->stringSet($this->key,$request->sms_provider);
        event(new AdminUserBehavior(auth('web')->user()->id,"修改短信接口",'短信接口'));

        return back()->with('success','操作成功');
    }

}