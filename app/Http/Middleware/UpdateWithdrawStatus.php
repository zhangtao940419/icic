<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/13
 * Time: 16:16
 */

namespace App\Http\Middleware;

use Closure;
use App\Model\CoinTradeOrder;
use App\Server\CoinServer;
use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethServer;
use function GuzzleHttp\Promise\is_fulfilled;

class UpdateWithdrawStatus
{
    private $coinTradeOrder;
    private $bitCoinServer;
    private $gethServer;

    //此中间件处理更新用户提币订单状态的逻辑
    public function __construct(CoinTradeOrder $coinTradeOrder,BitCoinServer $bitCoinServer,GethServer $gethServer)
    {
        $this->coinTradeOrder = $coinTradeOrder;
        $this->bitCoinServer = $bitCoinServer;
        $this->gethServer = $gethServer;
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

        $withdrawRecords = $this->coinTradeOrder->getRecords(['user_id'=>$request->input('user_id'),'coin_id'=>$request->input('coin_id'),'order_status'=>0]);//提币记录

        if ($withdrawRecords){
            foreach ($withdrawRecords->toArray() as $record){
                if ($record['order_trade_hash'] && ($record['order_status'] != 1)) {
                    switch ($record['coin_name']['coin_name']) {
                        case 'BTC':
                            (new CoinServer())->updateOrderStatus($this->bitCoinServer, $request->input('user_id'), $record['coin_id'], $record['order_id'], $record['order_trade_hash'], bcadd($record['order_trade_money'], $record['order_trade_fee'], 8), 'BTC');
                            break;
                        case 'ETH':
                            (new CoinServer())->updateOrderStatus($this->gethServer, $request->input('user_id'), $record['coin_id'], $record['order_id'], $record['order_trade_hash'], bcadd($record['order_trade_money'], $record['order_trade_fee'], 8), 'ETH');
                            break;
                        case 'BABC':
                            (new CoinServer())->updateOrderStatus($this->gethServer, $request->input('user_id'), $record['coin_id'], $record['order_id'], $record['order_trade_hash'], bcadd($record['order_trade_money'], $record['order_trade_fee'], 8), 'BABC');
                            break;
                        case 'USDT':
                            (new CoinServer())->updateOrderStatus($this->gethServer, $request->input('user_id'), $record['coin_id'], $record['order_id'], $record['order_trade_hash'], bcadd($record['order_trade_money'], $record['order_trade_fee'], 8), 'USDT');
                            break;
                        default:
                    }
                }
            }
        }


        return $next($request);
    }

}