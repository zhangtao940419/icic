<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/10
 * Time: 14:27
 */

namespace App\Http\Middleware;

use Closure;
use App\Model\WalletDetail;
use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethServer;
use App\Server\CoinServer;
use App\Server\CoinServers\GethTokenServer;
use App\Model\EthToken;
use App\Server\CoinServers\OmnicoreServer;

class UpdateUserWallet
{
//此中间件处理更新用户钱包余额的逻辑
//智能处理更新单个币种的余额
//当用户查看某个钱包时调用该中间件

    private $coinServer;
    private $walletDetail;

    public function __construct(CoinServer $coinServer,WalletDetail $walletDetail)
    {
        $this->coinServer = $coinServer;
        $this->walletDetail = $walletDetail;
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
            if ($request->input('wallet_id') == null) return response()->json(['status_code'=>4001,'message'=>'处理失败']);

            $wallet = $this->walletDetail->getRecordById($request->input('wallet_id'),$request->input('user_id'));

            if (! $wallet) return $next($request);

            if ($wallet['wallet_address'] == '') return $next($request);

            switch ($wallet['coin_name']['coin_name']){
                case 'BTC':
                    if (env('APP_V') == 'test') return $next($request);
                    $this->coinServer->updateUserWallet((new BitCoinServer()),$wallet);
                    break;
                case 'ETH':
                    $this->coinServer->updateUserWallet((new GethServer()),$wallet);
                    break;
                case 'USDT':
                    if (env('APP_V') == 'test') return $next($request);
                    (new CoinServer())->updateUserWallet((new OmnicoreServer()),$wallet);
                    break;
                default:
                    $token = (new EthToken())->getRecordByCoinId($wallet['coin_id']);//dd($token->toArray());
                    if ($token){
                        $this->coinServer->updateUserWallet((new GethTokenServer($token->token_contract_address,$token->token_contract_abi)),$wallet,$token->toArray());
                    }
            }
        }catch (\Exception $exception){
            return $next($request);
        }


        return $next($request);
    }


}