<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9
 * Time: 17:02
 */
namespace App\Http\Middleware\OutsideMiddleware;

use App\Model\CoinType;
use App\Model\EthToken;
use App\Model\OutsideWalletDetail;
use App\Server\OutsideWalletServer;
use App\Traits\RedisTool;
use Closure;

class CheckOutsideWallets
{
    use RedisTool;
    protected $coinType;
    protected $ethToken;
    protected $outsideWallet;
    protected $outsideWalletServer;
    public function __construct(CoinType $coinType,EthToken $ethToken,OutsideWalletDetail $outsideWalletDetail,OutsideWalletServer $outsideWalletServer)
    {
        $this->coinType = $coinType;
        $this->ethToken = $ethToken;
        $this->outsideWallet = $outsideWalletDetail;
        $this->outsideWalletServer = $outsideWalletServer;
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

        //if (! $this->setKeyLock('outside:check_wallets' . $request->user_id,2)) return $next($request);

        $coinList = $this->coinType->getOutsideCoin(['coin_name','coin_id'])->toArray();//dd(1);
        $wallets = $this->outsideWallet->getUserWallets($request->user_id,['coin_id'])->toArray();
        $hasCoinList = [];
        foreach ($wallets as $wallet){
            array_push($hasCoinList,$wallet['coin_id']);
        }
//        dd($hasCoinList);
        $needCoinList = [];
        foreach ($coinList as $value){
            if (in_array($value['coin_id'],$hasCoinList)) continue;
            $needCoinList[] = $value;
        }
        if ($needCoinList) $this->outsideWalletServer->createAccount($request->user_id,$needCoinList);

        return $next($request);

    }

}