<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 15:06
 */

namespace App\Http\Middleware\OutsideMiddleware;

use App\Server\CoinServers\OmnicoreServer;
use App\Server\OutsideTrade\Dao\OutsideWalletDao;
use App\Server\OutsideWalletServer;
use App\Traits\RedisTool;
use Closure;
use App\Model\User;
use App\Model\CoinType;
use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethServer;
use App\Model\EthToken;

class CheckCoinWallet
{
    use RedisTool;
//此中间件用于检测用户钱包账户地址是否存在,若不存在则创建之
    private $coinType;
    private $walletDetail;
    private $coinServer;
    private $ethToken;

    public function __construct(CoinType $coinType,OutsideWalletDao $walletDetail,OutsideWalletServer $coinServer,EthToken $ethToken)
    {
        $this->coinType = $coinType;
        $this->walletDetail = $walletDetail;
        $this->coinServer = $coinServer;
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
        $wallet = $this->walletDetail->getRecordById($request->input('wallet_id'),$request->input('user_id'));
        if (!$wallet) return response()->json(['status_code'=>1004,'message'=>'参数错误']);
        //dd($wallet);


        if ($wallet['wallet_address'] == ''){
            $key = 'created:wallet:'.$request->wallet_id;
            if (! $this->setKeyLock($key,10)) return $next($request);

            switch ($wallet['coin_name']['coin_name']){
                case 'BTC':
                    $this->coinServer->createBlockAccount((new BitCoinServer()),$wallet['wallet_id'],$wallet['coin_name']['coin_name'],$wallet['user_id']);
                    break;
                case 'ETH':
                    $this->coinServer->createBlockAccount((new GethServer()),$wallet['wallet_id'],$wallet['coin_name']['coin_name'],$wallet['user_id']);
                    break;
                case 'USDT':
                    $this->coinServer->createBlockAccount((new OmnicoreServer()),$wallet['wallet_id'],$wallet['coin_name']['coin_name'],$wallet['user_id']);
                    break;
                default:
                    if ($this->ethToken->getRecordByCoinId($wallet['coin_id']) && $wallet['parent_id']){
                        $parentWallet = $this->walletDetail->getRecordById($wallet['parent_id'],$request->input('user_id'));
                        if ($parentWallet['wallet_address'] == ''){
                            $this->coinServer->createBlockAccount((new GethServer()),$parentWallet['wallet_id'],$parentWallet['coin_name']['coin_name'],$wallet['user_id']);
                        }
                    }
                    break;
            }
            $this->redisDelete($key);
        }

        //查询用户账户的实际余额,与数据库的余额进行同步
//        $this->coinServer


        return $next($request);

    }



}