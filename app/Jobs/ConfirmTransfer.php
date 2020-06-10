<?php

namespace App\Jobs;

use App\Model\CoinTradeOrder;
use App\Server\CoinServers\GethServer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConfirmTransfer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    public $coinServer;
    public $gasLimit;
    public $gasPrice;
    public $hash;

    public $coinTradeOrder;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($hash,$order,$gasLimit,$gasPrice)
    {
        //
        $this->order = $order;
        $this->gasLimit = $gasLimit;
        $this->gasPrice = $gasPrice;
        $this->hash = $hash;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $this->coinServer = new GethServer();

        $this->coinTradeOrder = new CoinTradeOrder();
        $status = $this->coinServer->getTransactionReceipt($this->hash);

        switch ($status){
            case 1:
                $this->coinTradeOrder->updateOneRecord($this->order['order_id'],['order_status'=>1]);
                break;
            case -1:
                TransferToken::dispatch($this->order,1,$this->gasLimit+50000,$this->gasPrice+5)->onQueue('transfer_token');
                break;
            default:
                $this->dispatch($this->hash,$this->order,$this->gasLimit,$this->gasPrice)->delay(now()->addMinutes(5))->onQueue('confirm_transfer');
                break;

        }


    }
}
