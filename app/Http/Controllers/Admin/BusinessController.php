<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25
 * Time: 11:01
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\C2CTradeOrder;
use App\Model\User;
use Illuminate\Http\Request;

class BusinessController extends Controller
{

    public function index(User $user)
    {

        $users = $user->with('userIdentify')->where(['is_business'=>1])->latest()->paginate();
        return view('admin.business.index',compact('users'));

    }




    public function show(User $user,Request $request,C2CTradeOrder $c2CTradeOrder)
    {
        $user = $user->with('userIdentify')->find($request->route('business'));
        $data = $c2CTradeOrder->iODetails($user->user_id);
//        dd($user);
        return view('admin.business.show',compact('user','data'));
    }

}