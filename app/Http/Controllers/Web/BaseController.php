<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/6/27
 * Time: 13:35
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Response\ApiResponse;

class BaseController extends Controller{

    use ApiResponse;

    //create by zt
    const STATUS_CODE_TOKEN_NOTEXIST = 1001;//缺少token
    const STATUS_CODE_TOKEN_ERROR = 1002;//token不正确,用户未登录
    const STATUS_CODE_TOKEN_EXPIRE = 1003;//token已过期,请重新登录!

    const STATUS_CODE_PARAMETER_ERROR = 1004;//参数错误
    const STATUS_CODE_UNAUTHORIZED = 1005;//账号或密码不正确
    const STATUS_CODE_PHONE_NOTEXIST = 1006;//该手机号未注册,请重新输入
    const STATUS_CODE_PHONE_HASEXIST = 1007;//该手机号已被注册
    const STATUS_CODE_REPASSWORD_ERROR = 1008;//两次密码不一致
    const STATUS_CODE_NICKNAME_HASEXIST = 1009;//昵称已被占用

    const STATUS_CODE_CODE_ERROR = 1010;//验证码错误
    const STATUS_CODE_CODE_EXPIRE = 1011;//验证码过期
    const STATUS_CODE_CODE_REPEAT = 1012;//验证码重复发送

    const STATUS_CODE_PASSWORD_ERROR = 1013;//密码错误

    const STATUS_CODE_EMAIL_HASEXIST = 1014;//邮箱已被使用
    const STATUS_CODE_IDCARD_HASEXIST = 1015;//该身份证已被认证

    const STATUS_CODE_IMAGE_ERROR = 1016;//图片大小或格式不正确
    const STATUS_CODE_IMAGE_TOOLARGE = 1017;//图片过大

    const STATUS_CODE_NOTSUFFICIENT_FUNDS = 1020;//余额不足

    const STATUS_CODE_UNKNOWN_ERROR = 1030;//未知的错误

    const STATUS_CODE_AUTH_VERIFY_FAIL = 1031;//验证不通过
    const STATUS_CODE_PRIMARY_AUTH_FAIL = 1032;//请提交初级认证
    const STATUS_CODE_TOP_AUTH_FAIL = 1033;//请提交高级认证

    const STATUS_CODE_PAYPASSWORD_NOTEXIST = 1034;//请设置资金密码


    const STATUS_CODE_CARDNO_ERROR = 1040;//身份证验证失败
    const STATUS_CODE_BANK_VERIFY_EXIST = 1041;//已绑定银行卡
    const STATUS_CODE_PHONE_ERROR = 1042;//手机不正确
    const STATUS_CODE_NO_BANK_CARD = 1043;//请绑定银行卡
    const STATUS_CODE_BANK_NAME_ERROR = 1044;//银行名有误
    const STATUS_CODE_CARD_VERIFY_ERROR = 1045;//身份验证不通过
    const STATUS_CODE_VERIFY_NUM_LIMIT = 1046;//身份验证次数达上限
    const STATUS_CODE_IDCARD_NOT_LEGAL = 1047;//身份证不合法
    const STATUS_CODE_BANKCARD_NOT_LEGAL = 1048;//银行卡不合法

    const STATUS_CODE_NOT_BUSINESS = 1050;//您不是商家

    const STATUS_CODE_C2CTRADE_UNUSABLE = 1060;//订单不可用
    const STATUS_CODE_CANNOT_ACCEPT_SELF = 1061;//不能接自己的单
    const STATUS_CODE_C2CTRADE_LIMIT = 1062;//订单达到上限
    const STATUS_CODE_C2CTRADE_NUM_LIMIT = 1063;//订单金额超过限制
    const STATUS_CODE_C2CTRADE_TIME_LIMIT = 1064;//请稍后再接单
    const STATUS_CODE_C2CORDER_HANDLING = 1065;//有待处理的订单
    const STATUS_CODE_C2CORDER_LATER_HANDLE = 1066;//请稍后进行处理

    const STATUS_CODE_CANNOT_HANDLE = 1070;//暂时不可操作
    const STATUS_CODE_AMOUNT_ERROR = 1071;//请输入正确的金额
    const STATUS_CODE_CANNOT_TRANSFER_SELF = 1072;//不能给自己转张

    const STATUS_CODE_HANDLE_FAIL = 4001;//操作失败,处理失败,内部错误
    const STATUS_CODE_ACCOUNT_OTHER_POINT = 4002;//账号已在别处登录


    /////////////

    const STATUS_CODE_SUCCESS = 200;//代表操作成功或验证通过
    const STATUS_CODE_CREATED = 201;//	已创建。成功请求并创建了新的资源
    const STATUS_CODE_ACCEPTED = 202;//已接受。已经接受请求，但未处理完成
    const STATUS_CODE_NOCONTENT = 204;//	无内容。服务器成功处理，但未返回内容。在未更新网页的情况下，可确保浏览器继续显示当前文档

    const STATUS_CODE_DATA_HASEXIST = 2001;//数据已存在



    const STATUS_CODE_DATA_ERROR = 2006;//数据错误
    const STATUS_CODE_DATA_EMPTY = 2007;//根据条件查询的数据为空或者已经被删除
    const STATUS_CODE_BALANCE_UNENOUGH = 3008;//账户余额不足
    const STATUS_CODE_TRADE_ERROR = 3009;//挂单发起失败
    const STATUS_CHANGEPAY_STATU_ERROR = 3010;//更改支付状态失败
    const STATUS_PAY_CONFIRM_REPEAT = 3011;//重复确认支付状态
    const STATUS_INSIDE_ORDER_TRADE_SUCCESS = 3012;//订单撮合成功
    const STATUS_INSIDE_ORDER_WAIT_TRADE = 3013;//订单入库等待交易
    const STATUS_INSIDE_ORDER_TRADE_ERROR = 3014;//订单撮合失败或者出错
    const STATUS_INSIDE_ORDER_HAS_TRADE_SOME = 3015;//订单已交易一部分，其余部分等待交易
    const STATUS_INSIDE_CANNEL_FAIL = 3016;//撤单失败；
    const STATUS_OUTSIDE_ORDER_CANNEL_FINSH = 3017;//已撤单；
    const STATUS_OUTSIDE_ORDER_CANNOT_CANNEL = 3018;//不允许撤单；
    const STATUS_OUTSIDE_ORDER_REPEAT = 3019;//广告重复发起；
    const STATUS_TRADE_LOCK = 3020;//交易锁定；
    const STATUS_COMPLAIN_ERROR = 3021;//投诉处理失败；


    const STATUS_CODE_BADREQUEST = 400;//	客户端请求的语法错误，服务器无法理解


    const STATUS_CODE_FORBIDDEN = 403;//服务器理解请求客户端的请求，但是拒绝执行此请求
    const STATUS_CODE_NOTFOUND = 404;//服务器无法根据客户端的请求找到资源（网页）。
    const STATUS_CODE_METHODNOTALLOWD = 405;//客户端请求中的方法被禁止
    const STATUS_CODE_NOTACCEPTABLE = 406;//服务器无法根据客户端请求的内容特性完成请求
    const STATUS_CODE_REQUESTTIMEOUT = 408;//服务器等待客户端发送的请求时间过长，超时
    const STATUS_CODE_GONFILCT = 409;//服务器完成客户端的PUT请求是可能返回此代码，服务器处理请求时发生了冲突
    const STATUS_CODE_GONE = 410;//客户端请求的资源已经不存在。410不同于404，如果资源以前有现在被永久删除了可使用410代码，网站设计人员可通过301代码指定资源的新位置
    const STATUS_CODE_ENTITYTOOLARGE = 413;//由于请求的实体过大，服务器无法处理，因此拒绝请求。为防止客户端的连续请求，服务器可能会关闭连接。如果只是服务器暂时无法处理，则会包含一个Retry-After的响应信息
    const STATUS_CODE_SERVERERROR = 500;//服務器內部錯誤







    function __construct(){


    }


}
