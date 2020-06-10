<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/10
 * Time: 14:27
 */

namespace App\Http\Middleware\OutsideMiddleware;

use App\Server\CoinServers\OmnicoreServer;
use App\Server\OutsideTrade\Dao\OutsideWalletDao;
use App\Server\OutsideWalletServer;
use Closure;
use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethServer;
use App\Server\CoinServers\GethTokenServer;
use App\Model\EthToken;

class UpdateUserWallets
{
//此中间件处理更新用户所有钱包余额的逻辑
//智能处理更新所有币种钱包的余额
//当用户刷新钱包时调用该中间件

    private $coinServer;
    private $walletDetail;
    private $ethToken;

    public function __construct(OutsideWalletDao $walletDetail,EthToken $ethToken)
    {
        $this->walletDetail = $walletDetail;
        $this->ethToken = $ethToken;
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
        $wallets = $this->walletDetail->walletIndex($request->input('user_id'));

        foreach ($wallets as $wallet){
            if ($wallet['wallet_address'] != '') {
                switch ($wallet['coin_name']['coin_name']) {
                    case 'BTC':
                        (new OutsideWalletServer())->updateUserWallet((new BitCoinServer()), $wallet);
                        break;
                    case 'ETH':
                        (new OutsideWalletServer())->updateUserWallet((new GethServer()), $wallet);
                        break;
                    case 'USDT':
                        (new OutsideWalletServer())->updateUserWallet((new OmnicoreServer()),$wallet);
                        break;
                    default:
                        $token = $this->ethToken->getRecordByCoinId($wallet['coin_id']);
                        if ($token) {
                            (new OutsideWalletServer())->updateUserWallet((new GethTokenServer($token->token_contract_address, $token->token_contract_abi)), $wallet, $token->toArray());
                        }
                }
            }
        }


        return $next($request);
    }


}