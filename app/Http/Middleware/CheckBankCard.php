<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/8
 * Time: 11:01
 */

namespace App\Http\Middleware;
use Closure;
use App\Model\BankCardVerify;

class CheckBankCard
{

    private $bankCard;
    public function __construct(BankCardVerify $bankCardVerify)
    {
        $this->bankCard = $bankCardVerify;

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

        if (! $this->bankCard->getRecordByUserId($request->input('user_id'))){
            return response()->json(['status_code'=>1043,'message'=>'请绑定银行卡']);
        }

        return $next($request);

    }

}