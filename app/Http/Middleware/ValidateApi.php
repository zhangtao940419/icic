<?php

namespace App\Http\Middleware;

use App\Model\Settings;
use Auth;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Traits\RedisTool;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use App\Model\User;

// 注意，我们要继承的是 jwt 的 BaseMiddleware
class ValidateApi extends BaseMiddleware
{
    use RedisTool;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {



//        dd($this->auth->parseToken()->authenticate()->toArray());
        try {
            // 检查此次请求中是否带有 token，如果没有则抛出异常。
            $this->checkForToken($request);
        } catch(\Exception $e) {
            return response()->json(['status_code'=>1002,'message'=>'缺少token']);
        }


//        dd($this->auth->getToken()->get());


        // 使用 try 包裹，以捕捉 token 过期所抛出的 TokenExpiredException  异常
        try {
            // 检测用户的登录状态，如果正常则通过

            $userMessage = $this->auth->parseToken()->authenticate();
            if (!$request->user_id) $request->merge(['user_id'=>$userMessage['user_id']]);
//            dd($userMessage);
            if (($request->input('user_id') != null) && $userMessage && ($request->user_id == $userMessage->toArray()['user_id'])) {

                if (!$userMessage['is_frozen']) return response()->json(['status_code'=>1002,'message'=>'账号被冻结']);


                if (
//                    User::find($request->input('user_id'))->is_business != 1
                    ($rand = $this->stringGet('SINGLE:POINT_TOKEN'.$request->input('user_id')))
                    && ($rand != $this->auth->payload()->get('rand'))
                    && env('APP_V') != 'test'
                ){
                    return response()->json(['status_code'=>4002,'message'=>'当前账号已在别处登录!']);//单点登录实现4002
                }

                return $next($request);
            }
//          throw new UnauthorizedHttpException('jwt-auth', '未登录');
            return response()->json(['status_code'=>1002,'message'=>'token不正确,用户未登录']);
        } catch (TokenExpiredException $exception) {
            // 此处捕获到了 token 过期所抛出的 TokenExpiredException 异常，我们在这里需要做的是刷新该用户的 token 并将它添加到响应头中
          try {
              // 刷新用户的 token
              $token = $this->auth->refresh();
             // 使用一次性登录以保证此次请求的成功
              Auth::guard('api')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
              if (!$request->user_id) $request->merge(['user_id'=>$this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']]);
              $GLOBALS['refreshToken'] = $token;

              // 在响应头中返回新的 token
              return $this->setAuthenticationHeader($next($request), $token);

//              echo($token);
          } catch (JWTException $exception) {
             // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
//              throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
              return response()->json(['status_code'=>1003,'message'=>'token已过期,请重新登录!']);
          }
//            return response()->json(['status_code'=>1003,'message'=>'token已过期,请重新登录!']);
        } catch (TokenBlacklistedException $exception){
            //黑名单检测
              return response()->json(['status_code'=>1003,'message'=>'token已过期,请重新登录!']);
        }catch (\Exception $exception){
            return api_response()->zidingyi('网络繁忙');
        }

//        return $next($request);
        return response()->json(['status_code'=>1002,'message'=>'token不正确,用户未登录']);

    }
}

