<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 15:06
 */

namespace App\Http\Middleware;

use App\Traits\RedisTool;
use Closure;
use App\Model\User;
use App\Model\CoinType;
use App\Model\WalletDetail;
use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethServer;
use App\Server\CoinServer;

class CheckUserWallet
{
//此中间件用于检测用户钱包账户地址是否存在,若不存在则创建之
use RedisTool;
    private $coinType;
    private $walletDetail;
    private $coinServer;
    public function __construct(CoinType $coinType,WalletDetail $walletDetail)
    {
        $this->coinType = $coinType;
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

        $coinType = $this->coinType->getAllCoinType();

//        foreach ($coinType as $coin){
//            switch ($coin['coin_name']){
//                case 'ETH':
//                    if (! $this->walletDetail->getOneRecord($request->input('user_id'),$coin['coin_id'])){
//                        (new CoinServer())->createNewAccount($request->input('user_id'),$coin['coin_id'],'','ETH');
//                    }
//                    break;
//            }
//        }
//        dd($coinType);

        if (!$this->setKeyLock('create_w'.$request->user_id,2)){
            return $next($request);
        }

        foreach ($coinType as $coin){
            if (! $this->walletDetail->getOneRecord($request->input('user_id'),$coin['coin_id'])){
                switch ($coin['coin_name']){
                    case 'BTC':
                        (new CoinServer())->createNewAccount($request->input('user_id'),$coin['coin_id'],'','BTC');
                        break;
                    case 'ETH':
                        (new CoinServer())->createNewAccount($request->input('user_id'),$coin['coin_id'],'','ETH');
                        break;
                    default:
                        (new CoinServer())->createNewAccount($request->input('user_id'),$coin['coin_id']);
                    break;
                        //(new CoinServer())->createNewAccount($request->input('user_id'),$coin['coin_id']);
                }
            }
        }

        //查询用户账户的实际余额,与数据库的余额进行同步
//        $this->coinServer

        return $next($request);

    }



}