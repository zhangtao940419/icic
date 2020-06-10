<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/9/28
 * Time: 10:03
 */

namespace App\Http\Controllers\Web\Api\V1;


use App\Http\Controllers\Controller;
use App\Model\C2c_User_Last_Trade_Time;
use App\Model\Settings;
use App\Model\UserQuestion;
use App\Model\UserQuestionType;
use App\Traits\FileTools;
use App\Traits\QiNiuFileTool;
use App\Traits\RedisTool;
use App\Traits\Tools;
use App\User;
use Illuminate\Http\Request;

class UserQuestionController extends Controller
{

use Tools,RedisTool,FileTools,QiNiuFileTool;

    public $userQuestion;

    public function __construct(UserQuestion $userQuestion)
    {
        $this->userQuestion = $userQuestion;
    }

    //获取联系客服的说明文字
    public function getUserQuestionMsg(Settings $settings)
    {
        $value = $settings->getUserQuestionMsg();
        return api_response()->successWithData(['msg' => $value]);

    }

    //获取所有问题类型
    public function getAllQuestionType(UserQuestionType $userQuestionType)
    {
        $re = $userQuestionType->getAllTypes();
        return api_response()->successWithData(['types' => $re]);


    }

    //用户上传图片
    public function uploadImage(Request $request)
    {
        if ($vr = $this->verifyField($request->all(),[
            'image' => 'required|file|min:1|max:10000'
        ])) return $vr;

        $path = $this->qiniuuploadSingleImg($request->image);

        return api_response()->successWithData(['path' => $path]);

    }

    //提交问题
    public function submitQuestion(Request $request,UserQuestionType $userQuestionType)
    {
        if ($vr = $this->verifyField($request->all(),[
            'question' => 'required',
            'email' => 'email',
            'type_id' => 'required|integer',
            'images' => 'required|string'
        ])) return $vr;

        $user = current_user();
        $sameTypeNum = $this->userQuestion->getSameTypeWaitHandleNum($user->user_id,$request->type_id);
        if ($sameTypeNum >= 1) return api_response()->zidingyi('请等待问题回复后再提交');

        $images = json_decode($request->images,true);
        $type = $userQuestionType->find($request->type_id);

        if (!$type) return api_response()->zidingyi('不存在的类型');


        $re = $this->userQuestion->insertOne(current_user()->user_id,$request->question,$request->type_id,$request->email == null ? '' : $request->email,$images);


        if ($re) return api_response()->success();

        return api_response()->error();



    }

    //反馈列表
    public function getQuestionList()
    {
        $user = current_user();

        $records = $this->userQuestion->with(['type'])->where(['user_id'=>$user->user_id])->latest('id')->get();


        return api_response()->successWithData(['records' => $records]);

    }



    //获取用户弹框
    public function getUserNewsRemind(C2c_User_Last_Trade_Time $c2c_User_Last_Trade_Time)
    {
        $user = current_user();


        $news = [];

        $rkey = 'user_new_remind_' . $user->user_id;
        $rv = $this->stringGet($rkey);


        if ($user->c2c_long_time_not_buy_status == 0 && $user->is_business == 0 && $rv == null){

            $last_time = $c2c_User_Last_Trade_Time->getUserLastTime($user->user_id);

            $now = time();
            $cha_days = ($last_time - $now) / (24*3600);//dd($cha_days);
            if ($cha_days > 0 && $cha_days <= 3){
                $news['title'] = '超时未交易提醒';

                $news['content'] = '尊敬的TTS用户, 检测到您已长时间未进行C2C买入交易, 系统将于' . date('Y-m-d H',$last_time) . '时将您的ICIC钱包余额转入矿池, 请您及时进入C2C进行交易';

                $this->stringSetex($rkey,18,1);

            }



        }

        if ($user->c2c_long_time_not_buy_status == 1  && $rv == null){
            $news['title'] = '超时未交易提醒';

            $news['content'] = '尊敬的TTS用户, 因为长期未买入的原因, 您获得ICIC将会直接进入矿池, 请进入C2C进行交易解除限制';

            $this->stringSetex($rkey,18,1);
        }

        $news = [];

        return api_response()->successWithData(['news' => $news]);

    }








}