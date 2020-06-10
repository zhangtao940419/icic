<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/16
 * Time: 10:10
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Response\ApiResponse;
use App\Traits\Tools;
use Illuminate\Http\Request;

class MessagePushController extends Controller
{

    use Tools,ApiResponse;



    public function getUserAccount(Request $request)
    {
        if ($this->verifyField($request->all(),
            [
                'get_user_id' => 'required|integer',
            ]
        )) return $this->parameterError();

        $userName = 'tts_jgim_u' . $request->get_user_id;
        $password = 'tts_jgim_p' . $request->get_user_id;
        app('messagePush')->register($userName,$password);
        return $this->successWithData(['user_name'=>$userName,'password'=>$password]);


    }

}