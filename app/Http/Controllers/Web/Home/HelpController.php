<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/30
 * Time: 16:51
 */

namespace App\Http\Controllers\Web\Home;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;

class HelpController extends BaseController
{

    public function index()
    {

        return view('home.helpIndex');
    }




    public function webRegister(Request $request)
    {

        return view('register.index')->with('invite_code',$request->invite_code);

    }

}