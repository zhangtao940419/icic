<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13
 * Time: 15:03
 */

namespace App\Http\Middleware\AuthMiddleware;

use Closure;
use App\Server\UserServers\Dao\BankCardVerifyDao;

class BankCardAuth
{
    public $bankCardVerify;
    public function __construct(BankCardVerifyDao $bankCardVerify)
    {
        $this->bankCardVerify = $bankCardVerify;

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try{

        $bankCard = $this->bankCardVerify->getRecordByUserId($request->input('user_id'));

        if (!$bankCard) return response()->json(['status_code'=>1043,'message'=>'请绑定银行卡']);

        }catch (\Exception $exception){
            return api_response()->zidingyi('网络繁忙');
        }

        return $next($request);
    }

}