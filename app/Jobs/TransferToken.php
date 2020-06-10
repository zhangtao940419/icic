<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Server\CoinServers\GethTokenServer;
use App\Model\EthToken;
use App\Model\CoinTradeOrder;
use App\Jobs\ConfirmTransfer;

class TransferToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type = 1;//1token   2eth


    public $order;

    public $coinServer;

    public $ethTokenModel;

    public $coinTradeOrder;

    public $gasLimit;
    public $gasPrice;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order,$type = 1,$gasLimit = 0,$gasPrice = 0)
    {
        //
        $this->order = $order;
        $this->type = $type;
        if ($gasLimit){
            $this->gasLimit = $gasLimit;
        }else{
            $this->gasLimit = $this->order['coin_fees']['eth_gaslimit'];
        }
        if ($gasPrice){
            $this->gasPrice = $gasPrice;
        }else{
            $this->gasPrice = $this->order['coin_fees']['eth_gasprice'];
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $this->coinTradeOrder = new CoinTradeOrder();
        $this->ethTokenModel = new EthToken();

        if ($this->type != 1) return;

        $token = $this->ethTokenModel->getRecordByCoinId($this->order['coin_id']);if (!$token) return;

        $this->coinServer = new GethTokenServer($token['token_contract_address'],$token['token_contract_abi']);

        $this->transferToken($this->order['order_trade_to'],$this->order['order_trade_money']);

    }


    public function transferToken($toAddress,$amount)
    {

        $result = $this->coinServer->sendTransaction($this->order['center_wallet']['center_wallet_address'],$this->order['center_wallet']['center_wallet_password'],$this->order['order_trade_to'],bcmul($this->order['order_trade_money'],bcpow(10,$this->order['token']['token_decimal'])),$this->gasLimit,$this->gasPrice);
        if (
            $result == -1
        ){
            $this->dispatch($this->order,$this->coinTradeOrder);//失败
        }elseif ($result == -2){
            $this->coinTradeOrder->updateOneRecord($this->order['order_id'],['transfer_status'=>2]);//成功没有hash
        }else {
            $this->coinTradeOrder->updateOneRecord($this->order['order_id'],['order_trade_hash'=>$result,'transfer_status'=>1]);//成功
            ConfirmTransfer::dispatch($result,$this->order,$this->gasLimit,$this->gasPrice)->delay(now()->addMinutes(5))->onQueue('confirm_transfer');
        }

    }

}
