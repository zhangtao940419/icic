<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 17:14
 */

namespace App\Http\Controllers\Web\Api\V1;
use App\Http\Controllers\Web\BaseController;
use App\Logic\CommonLogic;
use App\Logic\UserLogic;
use App\Model\Admin\AppVersion;
use App\Model\BankCardVerify;
use App\Model\CoinType;
use App\Model\Invitation;
use App\Model\Notice;
use App\Model\Settings;
use App\Model\WalletDetail;
use App\Model\WorldCurrency;
use App\Traits\RedisTool;
use App\Traits\Tools;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\UserTradeDatum;
use App\Model\WorldArea;
use App\Model\BankList;
use DB;
use App\Http\Response\ApiResponse;


class CommonController extends BaseController
{
    use Tools,RedisTool,ApiResponse;

    private $userLogic;

    private $commonLogic;


    function __construct(UserLogic $userLogic,CommonLogic $commonLogic)
    {
        $this->userLogic = $userLogic;
        $this->commonLogic = $commonLogic;
    }

    /**
     * 发送验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCodeSMS(Request $request)
    {
        if ($result = $this->verifyField($request->all(),[
            'phone' => 'required|max:11|min:11',
        ])) return $this->parameterError();

        switch ($this->userLogic->sendCodeSMS($request->phone)){
            case 0:
                return $this->responseByENCode('STATUS_CODE_CODE_REPEAT','请勿重新发送');
                break;
            case 1:
                return $this->success(200,'发送成功');
                break;
            case 2:
                return $this->responseByENCode('STATUS_CODE_HANDLE_FAIL','发送失败');
                break;
        }

    }


    /**
     * 检查验证码是否正确
     * @param Request $request
     * @param user $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCodeResponse(Request $request, user $user)
    {
        if($result = $this->verifyField($request->all(),[
            'phone' => 'required',
            'code' => 'required|integer',
        ])) return $this->parameterError();
//        if ($user->getUserByPhone($request->input('phone'))) return response()->json(['status_code'=>self::STATUS_CODE_DATA_HASEXIST,'message'=>'该手机号已被注册']);
        if (! $result = $this->checkCode($request->input('phone'),$request->input('code'))){
            return response()->json(['status_code'=>self::STATUS_CODE_CODE_EXPIRE,'message'=>'请重新发送验证码']);
        }elseif ($result == 1){
            return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'验证通过']);
        }else{
            return response()->json(['status_code'=>self::STATUS_CODE_CODE_ERROR,'message'=>'验证码不正确']);
        }

    }


    /*验证用户登录密码*/
    public function checkLoginPassword(Request $request, user $user)
    {
        if($result = $this->verifyField($request->all(),[
            'user_id' => 'required',
            'password' => 'required|min:6|max:16'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
        if ($user->getUserById($request->input('user_id'))->user_password !== md5($request->input('password')))
            return response()->json(['status_code'=>self::STATUS_CODE_PASSWORD_ERROR,'message'=>'密码错误']);
            return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'验证通过']);
    }

    /*验证用户资金密码*/
    public function checkPayPassword(Request $request, user $user)
    {
        if($result = $this->verifyField($request->all(),[
            'user_id' => 'required',
            'pay_password' => 'required|min:6|max:16'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
        if ($user->getUserById($request->input('user_id'))->user_pay_password !== md5($request->input('pay_password')))
            return response()->json(['status_code'=>self::STATUS_CODE_PASSWORD_ERROR,'message'=>'密码错误']);
        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'验证通过']);
    }

    /*查询汇率*/
    public function queryCurrencyExchange(Request $request)
    {
        if($result = $this->verifyField($request->all(),[
            'tcur' => 'required|string',
            'scur' => 'string'
        ])) return $this->parameterError();
        if (($result = $this->getCurrencyExchange($request->input('tcur'))) != 0)
            return $this->successWithData(['exchange'=>$result]);
        return $this->error();
    }

    /*获取用户的额外信息  交易数  好评率  信任数*/
    public function getExtraUserMsg(Request $request,UserTradeDatum $userTradeDatum)
    {
        $data = $userTradeDatum->getOneRecord($request->input('user_id'));
        $trade_favourable_comment = $data['trade_total_num'] == 0? 0:(int)ceil(($data['trade_favourable_comment']/$data['trade_total_num']) * 100);
        return $this->successWithData(['trade_total_num'=>$data['trade_total_num'],'favorite_comment_rate'=>$trade_favourable_comment,'trade_trust_num'=>$data['trade_trust_num']]);
    }

    //获取所有国家信息
    public function getCountryInfo()
    {
        $data['country_info'] = WorldArea::select(['country_cn_abbreviate', 'country_cn_full_name', 'country_en_name', 'country_id', 'country_id'])->get();

        return $this->successWithData($data);
    }

    //获取所有国家币种
    public function getAllCountryCoin()
    {
        $data['country_coin'] = WorldCurrency::select(['currency_code', 'currency_cn_full_name', 'currency_en_name', 'currency_id'])->get();

        return $this->successWithData($data);
    }

    //获取能用的场外的虚拟货币
    public function getVirtualCoin()
    {
        $data['virtual_coin'] = CoinType::where(['is_outside' => 1,'is_usable' => 1])->select(['coin_id', 'coin_name'])->get();

        return $this->successWithData($data);
    }


    //获取用户余额
    public function getUserBalance($coin_id, $user_id, WalletDetail $walletDetail, Request $request)
    {
        $this->verifyField($request->all(), [
            'user_id' => 'required',
            'coin_id' => 'required',
        ]);

        $data['user_balance'] = $walletDetail->getCoinUsableBalance($coin_id, $user_id);

        return $this->successWithData($data);
    }

    //判断该用户余额,最终交易费率
    public function getUserCanByMoney($coin_id, $user_id, $number, WalletDetail $walletDetail)
    {
        //用户余额
        $data['user_balance'] = $walletDetail->getCoinUsableBalance($coin_id, $user_id);

        $rate = \DB::table('change_rate')->pluck('charge')->toArray()[0];

        //用户挂单的总费率
        $change_rate = $rate * $number;
        //最后交易费用
        $data['total_balance'] = $data['user_balance'] + $change_rate;

        return $this->successWithData($data);
    }


    //获取某某虚拟货币兑换成CNY的价格或者虚拟货币
    public function changeTo_else_Coin(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'currency_type' => 'required',
            'num' => 'required|numeric|min:0.000000000001',
            'coin_id' => 'required|integer'
        ])) return response()->json($result);

        if (strtoupper($request->currency_type == 'CNY')) {
           $res = $this->changeTo_Virtual_currency($request->num, $request->coin_id);
           $data['type'] = 'virtual_coin';
        } else {
            $country_code = $request->country_code ?: 'CNY';

            //转化为人民币价格
            $res = $this->changeTo_Other_Coin($request->coin_id, $country_code, $request->num);
            $data['type'] = 'CNY_coin';
        }

        $data['num'] = $res;

        return $this->successWithData($data);
    }


    //增加差评
    public function addPraise(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'num' => 'required|integer',
            'user_id' => 'required|integer',
            'order_number' => 'required|string'
        ])) return response()->json($result);

        $trade_statu = \DB::table('outside_trade_order')->where('order_number', $request->order_number)->pluck('trade_statu')->first();

        if ($trade_statu == null) {
            return response()->json(['status_code' => self::STATUS_CODE_CARDNO_ERROR, 'message' => '未能查到该交易']);
        }

        if ($trade_statu == 4) {
            return response()->json(['status_code' => self::STATUS_CODE_CARDNO_ERROR, 'message' => '该交易已评论']);
        }

        if ($trade_statu == 3) {
            DB::table('outside_trade_order')->where('order_number', $request->order_number)->update(['trade_statu' => 4]);
            if ($request->num == 1) {
                DB::table('user_trade_datum')->where('user_id', $request->user_id)->increment('trade_favourable_comment');
            }
        }

        return $this->success();
    }

    /*查询银行列表*/
    public function getBankList(BankList $bankList)
    {
        return $this->successWithData(['bank_list'=>$bankList->getRecords()]);
    }

    /*获取用户姓名手机等信息*/
    public function getUserAuthMsg(Request $request,User $user)
    {
        return $this->successWithData(['user_msg'=>$user->getUserAuthMsg($request->input('user_id'))]);

    }

    /*获取用户绑定的银行卡列表*/
    public function getBankCardList(Request $request,BankCardVerify $bankCardVerify)
    {
        return $this->successWithData(['bank_card_list'=>$bankCardVerify->getAllCardsByUserId($request->input('user_id'))]);
    }

    /*查询是否是商家*/
    public function checkBusinessAuth(Request $request,User $user)
    {
        $user = $user->getUserById($request->input('user_id'),['user_id','is_business']);

        if ($user && $user->is_business){
            return $this->successWithData(['result'=>1]);
        }
        return $this->successWithData(['result'=>0]);

    }

    //获取最新的app公告
    public function getNotice(Notice $notice)
    {
        return $this->successWithData(['new_notice' => $notice->getNewNotice()]);
    }


    //获取Android最新版本
    public function getAndroid(Request $request,AppVersion $appVersion)
    {
        if ($result = $this->verifyField($request->all(), [
            'version' => 'required|integer',
        ])) return response()->json($result);
        $version = $appVersion->getOneRecord();

        if ($request->version != $version->version) {
//            return $this->successWithData(['android_url' => route('getfile')]);
            return $this->successWithData(['android_url' => $version->update_url]);
        } else {
            return $this->error();
        }

    }


    //获取Ios最新版本
    public function getIos(AppVersion $appVersion)
    {
        $version = $appVersion->getOneRecord('ios')->version;
        $url = $appVersion->getOneRecord('ios')->update_url;

        return $this->successWithData(['ios_url' => $url, 'version' => $version]);
    }


    //邀请接口
    public function getUserInvitation(Request $request)
    {
        if ($result = $this->verifyField($request->all(), [
            'user_id' => 'required|integer',
        ])) return response()->json($result);

        $data = [];
        $res = CoinType::with(['invitation' => function($q) use($request) {
            $q->where('user_id', $request->user_id);
        }])->get();


        foreach ($res as $k => $v) {
            $data['info'][$k]['coin_name'] = $v->coin_name;
            $data['info'][$k]['coin_num'] = empty($v->invitation) ? 0 : $v->invitation->coin_num;
        }
        $data['user_num'] = User::where('pid', $request->user_id)->count();
        $data['code'] = User::where('user_id', $request->user_id)->value('user_Invitation_code');

        return response()->json(['status_code' => self::STATUS_CODE_SUCCESS, 'message' => '获取成功', 'data' => $data]);

    }

    //理财说明
    public function getFinancingExplain()
    {

        $setting = DB::table('settings')->where(['setting_key'=>'financing_explain'])->first();
        return $this->successWithData($setting);
//        dd($setting);

    }

    //关于我们
    public function getAboutUs()
    {
        $data = DB::table('settings')->where(['setting_key'=>'about_us'])->first();
        return $this->successWithData($data);

    }

    //异步邮件发送
    public function sendEmailT(Request $request)
    {
        return $this->sendEmailMessage($request->email,$request->code);
    }


    //邀请码参数
    public function getInvitationQRCode(Request $request)
    {
        return $this->commonLogic->getInvitationCode($request->all());
    }

    //公告
    public function getNewNotice()
    {
        return $this->commonLogic->getNewNotice();
    }


    //实名认证地区
    public function getUserIdentifyArea()
    {
        $re = DB::table('user_identify_area')->where('is_usable',1)->get();
        return $this->successWithData(['area'=>$re]);
    }


    //隐私政策
    public function getPrivacyPolicy(Settings $settings)
    {
        $re = $settings->getSetting('privacy_policy');

        return api_response()->successWithData(['record' => $re]);
    }

}