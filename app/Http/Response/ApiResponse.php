<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 14:19
 */
namespace App\Http\Response;


trait ApiResponse
{
    //参数错误
    public function parameterError($code = 1004,$message = '参数错误' , $data = null) {
        return $this->responseJson($code,$message,$data);
    }

    //处理成功
    public function success($code = 200,$message = '操作成功',$data = null) {
        return $this->responseJson($code,$message,$data);
    }

    public function successWithData($data = null) {
        return $this->responseJson($code = 200,$message = '操作成功',$data);
    }

    public function error($code = 4001,$message = '操作失败',$data = null) {
        return $this->responseJson($code,$message,$data);
    }

    public function responseByENCode($statusENCode,$message = null,$data = null)
    {

        if (! $message) $message = $statusENCode;

        return $this->responseJson($this->statusCodes()[$statusENCode],$message,$data);

    }

    public function zidingyi($message)
    {
        return $this->responseJson(8888,$message,[]);
    }

    public function responseJson($statusCode,$message,$data)
    {
        if (isset($GLOBALS['refreshToken']))
        return response()->json([ 'status_code' => $statusCode, 'message' => $message, 'data' => $data, 'refresh_token'=>$GLOBALS['refreshToken']]);
        return response()->json([ 'status_code' => $statusCode, 'message' => $message, 'data' => $data]);
    }




    public function statusCodes()
    {

        return
        [
            'STATUS_CODE_PARAMETER_ERROR' => 1004,//参数错误
            'STATUS_CODE_CODE_REPEAT' => 1012,//验证码重复发送
            'STATUS_CODE_HANDLE_FAIL' => 4001,//操作失败,处理失败,内部错误

            'STATUS_CODE_TOKEN_NOTEXIST' => 1001,//缺少token
             'STATUS_CODE_TOKEN_ERROR' => 1002,//token不正确,用户未登录
             'STATUS_CODE_TOKEN_EXPIRE' => 1003,//token已过期,请重新登录!

             'STATUS_CODE_UNAUTHORIZED' => 1005,//账号或密码不正确
             'STATUS_CODE_PHONE_NOTEXIST' => 1006,//该手机号未注册,请重新输入
             'STATUS_CODE_PHONE_HASEXIST' => 1007,//该手机号已被注册
             'STATUS_CODE_REPASSWORD_ERROR' => 1008,//两次密码不一致
             'STATUS_CODE_NICKNAME_HASEXIST' => 1009,//昵称已被占用

             'STATUS_CODE_CODE_ERROR' => 1010,//验证码错误
             'STATUS_CODE_CODE_EXPIRE' => 1011,//验证码过期

            'STATUS_CODE_INVITE_CODE_ERROR' => 1012,//邀请码错误


            'STATUS_CODE_PASSWORD_ERROR' => 1013,//密码错误

             'STATUS_CODE_EMAIL_HASEXIST' => 1014,//邮箱已被使用
             'STATUS_CODE_IDCARD_HASEXIST' => 1015,//该身份证已被认证

             'STATUS_CODE_IMAGE_ERROR' => 1016,//图片大小或格式不正确
             'STATUS_CODE_IMAGE_TOOLARGE' => 1017,//图片过大

             'STATUS_CODE_NOTSUFFICIENT_FUNDS' => 1020,//余额不足

             'STATUS_CODE_UNKNOWN_ERROR' => 1030,//未知的错误

             'STATUS_CODE_AUTH_VERIFY_FAIL' => 1031,//验证不通过
             'STATUS_CODE_PRIMARY_AUTH_FAIL' => 1032,//请提交初级认证
             'STATUS_CODE_TOP_AUTH_FAIL' => 1033,//请提交高级认证

             'STATUS_CODE_PAYPASSWORD_NOTEXIST' => 1034,//请设置资金密码

            'STATUS_CODE_TOP_AUTH_CHECKING' => 1035,//高级认证审核中



            'STATUS_CODE_CARDNO_ERROR' => 1040,//身份证验证失败
             'STATUS_CODE_BANK_VERIFY_EXIST' => 1041,//已绑定银行卡
             'STATUS_CODE_PHONE_ERROR' => 1042,//手机不正确
             'STATUS_CODE_NO_BANK_CARD' => 1043,//请绑定银行卡
             'STATUS_CODE_BANK_NAME_ERROR' => 1044,//银行名有误
             'STATUS_CODE_CARD_VERIFY_ERROR' => 1045,//身份验证不通过
             'STATUS_CODE_VERIFY_NUM_LIMIT' => 1046,//身份验证次数达上限
             'STATUS_CODE_IDCARD_NOT_LEGAL' => 1047,//身份证不合法
             'STATUS_CODE_BANKCARD_NOT_LEGAL' => 1048,//银行卡不合法

             'STATUS_CODE_NOT_BUSINESS' => 1050,//您不是商家

             'STATUS_CODE_C2CTRADE_UNUSABLE' => 1060,//订单不可用
             'STATUS_CODE_CANNOT_ACCEPT_SELF' => 1061,//不能接自己的单
             'STATUS_CODE_C2CTRADE_LIMIT' => 1062,//订单达到上限
             'STATUS_CODE_C2CTRADE_NUM_LIMIT' => 1063,//订单金额超过限制
             'STATUS_CODE_C2CTRADE_TIME_LIMIT' => 1064,//请稍后再接单
             'STATUS_CODE_C2CORDER_HANDLING' => 1065,//有待处理的订单
             'STATUS_CODE_C2CORDER_LATER_HANDLE' => 1066,//请稍后进行处理

             'STATUS_CODE_CANNOT_HANDLE' => 1070,//暂时不可操作
             'STATUS_CODE_AMOUNT_ERROR' => 1071,//请输入正确的金额
             'STATUS_CODE_CANNOT_TRANSFER_SELF' => 1072,//不能给自己转张

            'STATUS_CODE_ADDRESS_ERROR' => 1073,//地址不合法

             'STATUS_CODE_ACCOUNT_OTHER_POINT' => 4002,//账号已在别处登录


            /////////////

             'STATUS_CODE_SUCCESS' => 200,//代表操作成功或验证通过
             'STATUS_CODE_CREATED' => 201,//	已创建。成功请求并创建了新的资源
             'STATUS_CODE_ACCEPTED' => 202,//已接受。已经接受请求，但未处理完成
             'STATUS_CODE_NOCONTENT' => 204,//	无内容。服务器成功处理，但未返回内容。在未更新网页的情况下，可确保浏览器继续显示当前文档

             'STATUS_CODE_DATA_HASEXIST' => 2001,//数据已存在



             'STATUS_CODE_DATA_ERROR' => 2006,//数据错误
             'STATUS_CODE_DATA_EMPTY' => 2007,//根据条件查询的数据为空或者已经被删除
             'STATUS_CODE_BALANCE_UNENOUGH' => 3008,//账户余额不足
             'STATUS_CODE_TRADE_ERROR' => 3009,//挂单发起失败
             'STATUS_CHANGEPAY_STATU_ERROR' => 3010,//更改支付状态失败
             'STATUS_PAY_CONFIRM_REPEAT' => 3011,//重复确认支付状态
             'STATUS_INSIDE_ORDER_TRADE_SUCCESS' => 3012,//订单撮合成功
             'STATUS_INSIDE_ORDER_WAIT_TRADE' => 3013,//订单入库等待交易
             'STATUS_INSIDE_ORDER_TRADE_ERROR' => 3014,//订单撮合失败或者出错
             'STATUS_INSIDE_ORDER_HAS_TRADE_SOME' => 3015,//订单已交易一部分，其余部分等待交易
             'STATUS_INSIDE_CANNEL_FAIL' => 3016,//撤单失败；
             'STATUS_OUTSIDE_ORDER_CANNEL_FINSH' => 3017,//已撤单；
             'STATUS_OUTSIDE_ORDER_CANNOT_CANNEL' => 3018,//不允许撤单；
             'STATUS_OUTSIDE_ORDER_REPEAT' => 3019,//广告重复发起；
             'STATUS_TRADE_LOCK' => 3020,//交易锁定；
             'STATUS_COMPLAIN_ERROR' => 3021,//投诉处理失败；

            //3500开头为理财部分状态码
            'STATUS_INVEST_BUY_SUCCESS' => 200,//理财购买成功；
            'STATUS_INVEST_BUY_ERROR' => 3501,//理财购买失败；
            'STATUS_INVEST_ORDER_NULL' => 3502,//订单不存在；
            'STATUS_INVEST_ORDER_CANCEL_FAIL' => 3503,//订单撤单失败；
            'STATUS_INVEST_ORDER_STATUS_ERROR' => 3504,//订单不符合状态；

            //'STATUS_INVEST_ORDER_STATUS_ERROR' => 3600,//订单信息已过期,请刷新；



             'STATUS_CODE_BADREQUEST' => 400,//	客户端请求的语法错误，服务器无法理解


             'STATUS_CODE_FORBIDDEN' => 403,//服务器理解请求客户端的请求，但是拒绝执行此请求
             'STATUS_CODE_NOTFOUND' => 404,//服务器无法根据客户端的请求找到资源（网页）。
             'STATUS_CODE_METHODNOTALLOWD' => 405,//客户端请求中的方法被禁止
             'STATUS_CODE_NOTACCEPTABLE' => 406,//服务器无法根据客户端请求的内容特性完成请求
             'STATUS_CODE_REQUESTTIMEOUT' => 408,//服务器等待客户端发送的请求时间过长，超时
             'STATUS_CODE_GONFILCT' => 409,//服务器完成客户端的PUT请求是可能返回此代码，服务器处理请求时发生了冲突
             'STATUS_CODE_GONE' => 410,//客户端请求的资源已经不存在。410不同于404，如果资源以前有现在被永久删除了可使用410代码，网站设计人员可通过301代码指定资源的新位置
             'STATUS_CODE_ENTITYTOOLARGE' => 413,//由于请求的实体过大，服务器无法处理，因此拒绝请求。为防止客户端的连续请求，服务器可能会关闭连接。如果只是服务器暂时无法处理，则会包含一个Retry-After的响应信息
             'STATUS_CODE_SERVERERROR' => 500,//服務器內部錯誤

            'zidingyi_error' => 9999,//自定义错误
        ];

    }







}