<?php

namespace App\Console\Commands;

use App\Jobs\transfer_lock_auto_free;
use App\Model\CoinFees;
use App\Model\CoinTradeOrder;
use App\Model\EthToken;
use App\Model\TransferLockRecord;
use App\Server\AdminCoinServer;
use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethServer;
use App\Server\CoinServers\GethTokenServer;
use App\Traits\RedisTool;
use Illuminate\Console\Command;

class AutoCheckWithdrawOrder extends Command
{
    use
    RedisTool;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoCheckWithdrawOrder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动审核提币订单';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CoinFees $coinFees,CoinTradeOrder $coinTradeOrder)
    {
        //检测场内锁定订单
        $this->checkTransferLockOrder();

        //
        $coinIds = $coinFees->where(['withdraw_need_check'=>0])->pluck('coin_id')->toArray();

        if (!$coinIds) return;

        $orders = $coinTradeOrder->with('coinName','centerWallet','coinFees')->where(['order_check_status'=>0])->whereIn('coin_id',$coinIds)->get()->toArray();


        foreach ($orders as $order) {

//            $order = $coinTradeOrder->getRecordById($order_id);

            if ($this->redisExists('WITHDRAW_'.$order['order_id'])) return;
            $this->stringSet('WITHDRAW_'.$order['order_id'], $order['order_id']);

            switch ($order['coin_name']['coin_name']) {
                case 'BTC':
                    $result = (new AdminCoinServer())->checkWithdrawCoin((new BitCoinServer()), $order, 1);
                    break;
                case 'ETH':
                    $result = (new AdminCoinServer())->checkWithdrawCoin((new GethServer()), $order, 1);
                    break;

                case 'USDT':
                    $token = (new EthToken())->getRecordByCoinId($order['coin_id'])->toArray();
                    $order['token'] = $token;
                    $result = (new AdminCoinServer())->checkWithdrawCoin((new GethTokenServer($token['token_contract_address'], $token['token_contract_abi'])), $order, 1);
                    break;
                default:
//            case $this->coinSymbol:
                    $token = (new EthToken())->getRecordByCoinId($order['coin_id'])->toArray();
                    $order['token'] = $token;
                    if ($token && $token['token_contract_address'] !== '0x'){
                        $result = (new AdminCoinServer())->checkWithdrawCoin((new GethTokenServer($token['token_contract_address'], $token['token_contract_abi'])), $order, 1);
                    }else{
                        $result = (new AdminCoinServer())->checkWithdrawCoin((new GethServer()), $order, 1);
                    }

                    break;

            }
            $this->redisDelete('WITHDRAW_' . $order['order_id']);


        }


    }


    public function checkTransferLockOrder()
    {
        $t_orders = (new TransferLockRecord())->where('free_time','<',time()-10)->get();
        foreach ($t_orders as $t_order){
            dispatch(new transfer_lock_auto_free($t_order->id,1));
        }

        return true;

    }

}
