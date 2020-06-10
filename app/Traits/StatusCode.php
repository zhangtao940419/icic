<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/9
 * Time: 10:11
 */
namespace App\Traits;


trait StatusCode
{

    public static $STATUS_CODE_SUCCESS = 200;//请求成功。一般用于GET与POST请求
    public static $STATUS_CODE_CREATED = 201;//	已创建。成功请求并创建了新的资源
    public static $STATUS_CODE_ACCEPTED = 202;//已接受。已经接受请求，但未处理完成
    public static $STATUS_CODE_NOCONTENT = 204;//	无内容。服务器成功处理，但未返回内容。在未更新网页的情况下，可确保浏览器继续显示当前文档

    public static $STATUS_CODE_DATA_HASEXIST = 2001;//数据已存在
    public static $STATUS_CODE_PARAMETER_ERROR = 2002;//参数错误
    public static $STATUS_CODE_CODE_REPEAT = 2003;//验证码重复发送
    public static $STATUS_CODE_CODE_ERROR = 2004;//验证码错误
    public static $STATUS_CODE_CODE_EXPIRE = 2005;//验证码过期



    public static $STATUS_CODE_BADREQUEST = 400;//	客户端请求的语法错误，服务器无法理解
    public static $STATUS_CODE_UNAUTHORIZED = 401;//	请求要求用户的身份认证
    public static $STATUS_CODE_FORBIDDEN = 403;//服务器理解请求客户端的请求，但是拒绝执行此请求
    public static $STATUS_CODE_NOTFOUND = 404;//服务器无法根据客户端的请求找到资源（网页）。
    public static $STATUS_CODE_METHODNOTALLOWD = 405;//客户端请求中的方法被禁止
    public static $STATUS_CODE_NOTACCEPTABLE = 406;//服务器无法根据客户端请求的内容特性完成请求
    public static $STATUS_CODE_REQUESTTIMEOUT = 408;//服务器等待客户端发送的请求时间过长，超时
    public static $STATUS_CODE_GONFILCT = 409;//服务器完成客户端的PUT请求是可能返回此代码，服务器处理请求时发生了冲突
    public static $STATUS_CODE_GONE = 410;//客户端请求的资源已经不存在。410不同于404，如果资源以前有现在被永久删除了可使用410代码，网站设计人员可通过301代码指定资源的新位置
    public static $STATUS_CODE_ENTITYTOOLARGE = 413;//由于请求的实体过大，服务器无法处理，因此拒绝请求。为防止客户端的连续请求，服务器可能会关闭连接。如果只是服务器暂时无法处理，则会包含一个Retry-After的响应信息
    public static $STATUS_CODE_SERVERERROR = 500;//服務器內部錯誤

    public static $STATUS_CODE_HANDLE_FAIL = 4001;//操作失败


    public static $STATUS_CODE_TOKEN_NOTEXIST = 501;//未带token
    public static $STATUS_CODE_TOKEN_ERROR = 502;//token错误
    public static $STATUS_CODE_TOKEN_EXPIRE = 503;//token过期



}