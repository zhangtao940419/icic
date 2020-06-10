<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/7
 * Time: 14:30
 */

namespace App\Server;

use App\Model\CenterWalletRecord;
use App\Model\WalletFlow;
use App\Model\XYModel\UserWalletLog;
use App\Server\ChatServer\ChatServer;
use App\Server\PlcServer\PlcServer;
use App\Server\CoinServers\GethServer;
use App\Server\Interfaces\CoinServerInterface;
use App\Model\WalletDetail;
use App\Model\EthToken;
use App\Model\CoinType;
use App\Model\CoinTradeOrder;
use Illuminate\Support\Facades\DB;
use App\Model\CenterWalletTransfer;
use App\Traits\RedisTool;
use App\Jobs\TransferToken;
use App\Model\kgModel\UserWallet;


class AdminCoinServer
{
    use RedisTool;
//    public $coinServer;
    private $walletDetail;
    private $ethToken;
    private $coinType;
    private $coinTradeOrder;
    private $coinServer;
    private $centerWalletTransfer;

    public function __construct()
    {
        $this->walletDetail = new WalletDetail;
        $this->ethToken = new EthToken;
        $this->coinType = new CoinType;
        $this->coinTradeOrder = new CoinTradeOrder();
        $this->centerWalletTransfer = new CenterWalletTransfer();
    }

    /*后台审核通过提币逻辑*/
    public function checkWithdrawCoin(CoinServerInterface $coinServer, $order, $status)
    {
        if ($order['order_check_status'] != 0) return 3;
        if ($status == 2) {
            return $this->refuseWithdraw($order);//refuse
        }

//        if ($order['transfer_type'] == 5){
//            return $this->transferChat($order);
//        }elseif ($order['transfer_type'] == 6){
//            return $this->transferPlc($order);
//        }

//        $hjkgWallet = UserWallet::where(['wallet_address' => $order['order_trade_to']])->first();
//        if ($hjkgWallet) return $this->transferKg($order);

//        $xyWallet = \App\Model\XYModel\UserWallet::where(['wallet_address' => $order['order_trade_to']])->first();
//        if ($xyWallet) return $this->transferXY($order);


//dd($order);
        $btpWallet = $this->walletDetail->getRecordByAddress($order['order_trade_to']);

        $this->coinServer = $coinServer;

        switch ($order['coin_name']['coin_name']) {
            case 'BTC':
                if ($btpWallet && ($btpWallet->coin_id == $order['coin_id'])){
                    return $this->transferByOurSelf($order,$btpWallet->user_id);
                }elseif ($btpWallet){
                    return 0;
                }
                return $this->BTCWithdraw($order);
                break;
            case 'ETH':
                if ($btpWallet && ($btpWallet->coin_id == $order['coin_id'])){
                    return $this->transferByOurSelf($order,$btpWallet->user_id);
                }elseif ($btpWallet){
                    return 0;
                }
                return $this->ETHWithdraw($order);
                break;
//            case 'BABC':
//                if ($btpWallet && $this->walletDetail->where(['user_id'=>$btpWallet->user_id,'coin_id'=>$order['coin_id'],'parent_id'=>$btpWallet->wallet_id])->first()){
//                    return $this->transferByOurSelf($order,$btpWallet->user_id);
//                }elseif ($btpWallet){
//                    return 0;
//                }
//                return $this->TokenWithdraw($order);
//                break;
            case 'USDT':
                return -1;
                break;
            default:
                if ($btpWallet && $this->walletDetail->where(['user_id'=>$btpWallet->user_id,'coin_id'=>$order['coin_id'],'parent_id'=>$btpWallet->wallet_id])->first()){
                    return $this->transferByOurSelf($order,$btpWallet->user_id);
                }elseif ($btpWallet){
                    return 0;
                }
                return $this->TokenWithdraw($order);
                break;
        }

    }

    //chat内部
    public function transferChat($order)
    {
        try {
            DB::beginTransaction();
            $fromWallet = $this->walletDetail->where(['user_id' => $order['user_id'], 'coin_id' => $order['coin_id'], 'is_usable' => 1])->first();
            $fromAddress = $fromWallet->wallet_address;
            if (!$fromAddress) $fromAddress = $this->walletDetail->where(['wallet_id' => $fromWallet->parent_id])->first()->wallet_address;
            if ($fromWallet->decrement('wallet_freeze_balance', bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_check_status' => 1, 'order_status' => 1, 'order_trade_from' => $fromAddress])
            ) {
                if((new ChatServer())->transferToChat($fromAddress,$order['order_trade_to'],$order['order_trade_money'],$order['coin_name']['coin_name'])){
                    (new CenterWalletRecord())->saveOneRecord($order['coin_id'], $order['order_trade_fee'], '提币手续费', $order['user_id']);
                    DB::commit();
                    return 1;
                }
                DB::rollBack();
                return 0;
            }
            DB::rollBack();
            return 0;
        }catch (\Exception $exception){
            DB::rollBack();
            return 0;
        }


    }

    public function transferPlc($order)
    {
        try {
            DB::beginTransaction();
            $fromWallet = $this->walletDetail->where(['user_id' => $order['user_id'], 'coin_id' => $order['coin_id'], 'is_usable' => 1])->first();
            $fromAddress = $fromWallet->wallet_address;
            if (!$fromAddress) $fromAddress = $this->walletDetail->where(['wallet_id' => $fromWallet->parent_id])->first()->wallet_address;
            if ($fromWallet->decrement('wallet_freeze_balance', bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_check_status' => 1, 'order_status' => 1, 'order_trade_from' => $fromAddress])
            ) {
                if((new PlcServer())->transferToChat($fromAddress,$order['order_trade_to'],$order['order_trade_money'],$order['coin_name']['coin_name'])){
                    (new CenterWalletRecord())->saveOneRecord($order['coin_id'], $order['order_trade_fee'], '提币手续费', $order['user_id']);
                    DB::commit();
                    return 1;
                }
                DB::rollBack();
                return 0;
            }
            DB::rollBack();
            return 0;
        }catch (\Exception $exception){
            DB::rollBack();
            return 0;
        }
    }


    /*矿工内部*/
    public function transferKg($order)
    {
        try {
            DB::beginTransaction();
            $fromWallet = $this->walletDetail->where(['user_id' => $order['user_id'], 'coin_id' => $order['coin_id'], 'is_usable' => 1])->lockForUpdate()->first();
            $fromAddress = $fromWallet->wallet_address;
            $hjkgWallet = UserWallet::where(['wallet_address' => $order['order_trade_to']])->lockForUpdate()->first();
            if (!$fromAddress) $fromAddress = $this->walletDetail->where(['wallet_id' => $fromWallet->parent_id])->first()->wallet_address;
            if (
                $hjkgWallet->increment('wallet', $order['order_trade_money'])
                && $hjkgWallet->increment('tts_into',$order['order_trade_money'])
                && $fromWallet->decrement('wallet_freeze_balance', bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_check_status' => 1, 'order_status' => 1, 'order_trade_from' => $fromAddress, 'transfer_type' => 3])
            ) {
                (new CenterWalletRecord())->saveOneRecord($order['coin_id'], $order['order_trade_fee'], '提币手续费', $order['user_id']);
                DB::commit();
                return 1;
            }
            DB::rollBack();
            return 0;
        }catch (\Exception $exception){
            DB::rollBack();
            return 0;
        }


    }

    /*星云内部*/
    public function transferXY($order)
    {
        try {
            DB::beginTransaction();
            $fromWallet = $this->walletDetail->where(['user_id' => $order['user_id'], 'coin_id' => $order['coin_id'], 'is_usable' => 1])->lockForUpdate()->first();
            $fromAddress = $fromWallet->wallet_address;
            $xyCoin = (new \App\Model\XYModel\CoinType())->getCoin('NEBA');
            $xyWallet = \App\Model\XYModel\UserWallet::where(['wallet_address' => $order['order_trade_to'],'coin_id'=>$xyCoin->coin_id])->first();
            if (
                $xyWallet->increment('wallet', $order['order_trade_money'])
                && $fromWallet->decrement('wallet_freeze_balance', bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_check_status' => 1, 'order_status' => 1, 'order_trade_from' => $fromAddress, 'transfer_type' => 4])
                && (new UserWalletLog())->saveOne(['user_id'=>$xyWallet->user_id,'coin_id'=>$xyCoin->coin_id,'amount'=>$order['order_trade_money'],'log_type'=>'recharge'])
            ) {
                (new CenterWalletRecord())->saveOneRecord($order['coin_id'], $order['order_trade_fee'], '提币手续费', $order['user_id']);
                DB::commit();
                return 1;
            }
            DB::rollBack();
            return 0;
        }catch (\Exception $exception){
            DB::rollBack();
            return 0;
        }


    }

    /*内部转账*/
    public function transferByOurSelf($order,$toUserId)
    {
        try {
            if ($order['order_check_status'] != 0) return 3;
            DB::beginTransaction();
            $orderL = DB::table('coin_trade_order')->where('order_id', $order['order_id'])->lockForUpdate()->first();
            if ($orderL->order_check_status != 0) {
                DB::rollBack();
                return 3;
            }
            $toWallet = $this->walletDetail->where(['user_id' => $toUserId, 'coin_id' => $order['coin_id'], 'is_usable' => 1])->lockForUpdate()->first();
            $fromWallet = $this->walletDetail->where(['user_id' => $order['user_id'], 'coin_id' => $order['coin_id'], 'is_usable' => 1])->lockForUpdate()->first();
            $fromAddress = $fromWallet->wallet_address;
            if (strlen($fromAddress) < 5 && $fromWallet->parent_id) $fromAddress = $this->walletDetail->where(['wallet_id' => $fromWallet->parent_id])->first()->wallet_address;
            if (strlen($fromAddress) < 5) $fromAddress = ' ';
            if (
                $toWallet->increment('wallet_usable_balance', $order['order_trade_money'])
                && $fromWallet->decrement('wallet_freeze_balance', bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_check_status' => 1, 'order_status' => 1, 'order_trade_from' => $fromAddress, 'transfer_type' => 2])
                && $this->coinTradeOrder->saveOneRecord($toUserId, $order['coin_id'], '', $fromAddress, $order['order_trade_to'], $order['order_trade_money'], 2, 0, 2)
            ) {
                (new CenterWalletRecord())->saveOneRecord($order['coin_id'], $order['order_trade_fee'], '提币手续费', $order['user_id']);
                //对方流水
                (new WalletFlow())->insertOne($toUserId,$toWallet->wallet_id,$order['coin_id'],$order['order_trade_money'],2,1,'转入',1);
                DB::commit();
                return 1;

            }
            DB::rollBack();return 0;

        }catch (\Exception $exception){
            DB::rollBack();
//            dd($exception->getMessage());
            return 0;

        }


    }

    /*提币拒绝逻辑*/
    public function refuseWithdraw($order)
    {
        try {
            if ($order['order_check_status'] != 0) return 3;

            DB::beginTransaction();
            $orderL = DB::table('coin_trade_order')->where('order_id', $order['order_id'])->lockForUpdate()->first();
            if ($orderL->order_check_status != 0) {
                DB::rollBack();
                return 3;
            }
            if (
                $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_check_status' => 2])
                && $this->walletDetail->incrementRecordC(['user_id' => $order['user_id'], 'coin_id' => $order['coin_id']], 'wallet_withdraw_balance', bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $this->walletDetail->decrementRecordC(['user_id' => $order['user_id'], 'coin_id' => $order['coin_id']], 'wallet_freeze_balance', bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
            ) {
                //流水
                $wallet = $this->walletDetail->getOneRecord($order['user_id'],$order['coin_id']);
                (new WalletFlow())->insertOne($order['user_id'],$wallet->wallet_id,$order['coin_id'],bcadd($order['order_trade_money'], $order['order_trade_fee'], 8),26,1,'提币拒绝返还',2);
                DB::commit();
                return 1;
            }
            DB::rollBack();return 3;

        }catch (\Exception $exception){
            DB::rollBack();return 3;

        }

    }


    /*比特币的提币逻辑*/
    public function BTCWithdraw($order)
    {
        if ($order['order_check_status'] != 0) return 3;
//        dd($order);
        $userWallet = $this->walletDetail->getOneRecord($order['user_id'],$order['coin_id']);//dd($userWallet->wallet_id);
        $userBalance = $this->coinServer->getBalance($userWallet->wallet_account);
        if ($userBalance > 0){
            //$this->coinServer->move($userWallet->wallet_account,$order['center_wallet']['center_wallet_account'],$userBalance);
            //$this->centerWalletTransfer->saveOneRecord($order['user_id'],$order['coin_id'],$userWallet->wallet_id,1,$userBalance);//转账表
            //钱包表字段
        }

//dd($userWallet->wallet_account);
        if (bccomp(bcadd($order['order_trade_money'],$order['order_trade_fee'],8),$this->coinServer->getBalance($order['center_wallet']['center_wallet_account']),8) == 1)
            return 2;//中央钱包余额不足

        try {
            DB::beginTransaction();
            $orderL = DB::table('coin_trade_order')->where('order_id', $order['order_id'])->lockForUpdate()->first();
            if ($orderL->order_check_status != 0) {
                DB::rollBack();
                return 3;
            }
            $result1 = $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_check_status' => 1]);
            $result2 = $this->walletDetail->decrementRecordC(['user_id' => $order['user_id'], 'coin_id' => $order['coin_id']], 'wallet_freeze_balance', bcadd($order['order_trade_money'], $order['order_trade_fee'], 8));
            if ($result1 && $result2) {
                if ($result = $this->coinServer->sendFrom($order['center_wallet']['center_wallet_account'], $order['order_trade_to'], $order['order_trade_money'])) {
                    $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_trade_hash' => $result, 'order_trade_from' => $order['center_wallet']['center_wallet_address'], 'transfer_type' => 1]);
                    (new CenterWalletRecord())->saveOneRecord($order['coin_id'],$order['order_trade_fee'],'提币手续费',$order['user_id']);
                    DB::commit();
                    return 1;
                }
                DB::rollBack();
                return 0;
            }
            DB::rollBack();return 0;

        }catch (\Exception $exception){
            DB::rollBack();return 0;

        }

    }

    /*以太坊的提币逻辑*/
    public function ETHWithdraw($order)
    {//dd($this->coinServer->getBalance('0x5d6f0e205131b0d051b0aa1bc289a380c37ba80e'));
        if ($order['order_check_status'] != 0) return 3;
//dd($this->coinServer->getBalance('0x5d6f0e205131b0d051b0aa1bc289a380c37ba80e'));
        $totalAmount = bcadd($order['order_trade_money'],bcdiv($order['coin_fees']['eth_gaslimit']*$order['coin_fees']['eth_gasprice'],'1000000000',8),8);
//dd($totalAmount);
        $centerBalance = bcdiv($this->coinServer->getBalance($order['center_wallet']['center_wallet_address']),'1000000000000000000',8);//中央钱包余额
//dd($centerBalance);
        if (bccomp($totalAmount,$centerBalance,8) == 1) return 2;//中央钱包没钱了,让他联系客服吧

        try {
            DB::beginTransaction();
            $orderL = DB::table('coin_trade_order')->where('order_id', $order['order_id'])->lockForUpdate()->first();
            if ($orderL->order_check_status != 0) {
                DB::rollBack();
                return 3;
            }
            $result1 = $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_check_status' => 1]);
            $result2 = $this->walletDetail->decrementRecordC(['user_id' => $order['user_id'], 'coin_id' => $order['coin_id']], 'wallet_freeze_balance', bcadd($order['order_trade_money'], $order['order_trade_fee'], 8));
            if ($result1 && $result2) {
                if (
                    ($result = $this->coinServer->sendTransaction($order['center_wallet']['center_wallet_address'], $order['order_trade_to'], $order['order_trade_money'], $order['center_wallet']['center_wallet_password'], $order['coin_fees']['eth_gaslimit'], $order['coin_fees']['eth_gasprice']))
                    && is_string($result)
                    && (false !== strpos($result, '0x'))
                ) {
                    $this->coinTradeOrder->updateOneRecord($order['order_id'], ['order_trade_hash' => $result, 'order_trade_from' => $order['center_wallet']['center_wallet_address'], 'transfer_type' => 1]);
                    (new CenterWalletRecord())->saveOneRecord($order['coin_id'],$order['order_trade_fee'],'提币手续费',$order['user_id']);
                    DB::commit();
                    return 1;
                }
                DB::rollBack();
                return 0;
            }

            DB::rollBack();
            return 0;
        }catch (\Exception $exception){
            DB::rollBack();
            return 0;
        }

    }


    /*以太坊代币的提币逻辑*/
    public function TokenWithdraw($order)
    {
//        dd($this->walletDetail->getOneRecord($order['user_id'],$order['coin_id'])->dec);


        if ($order['order_check_status'] != 0) return 3;
        if ($this->coinServer->getSymbol() === 0) return 0;

//        $this->coinServer->sendTransaction($order['center_wallet']['center_wallet_address'],$order['center_wallet']['center_wallet_password'],'0x2b5496d5caf8c608dc4a0f0dff44c4651d5e1f42','50000000000000000000');

//        dd($this->coinServer->getSymbol());
        $centerBalance = bcdiv($this->coinServer->getBalance($order['center_wallet']['center_wallet_address']),bcpow(10,$order['token']['token_decimal']));//
//dd($centerBalance);
        if (bccomp($order['order_trade_money'],$centerBalance,8) == 1) return 2;//中央钱包代币没钱

        $centerethBalance = bcdiv((new GethServer())->getBalance($order['center_wallet']['center_wallet_address']),'1000000000000000000',8);//中央钱包eth余额
        if (bccomp('0.0001',$centerBalance,8) == 1) return 2;//中央钱包没钱了,让他联系客服吧


        DB::beginTransaction();
        $orderL = DB::table('coin_trade_order')->where('order_id',$order['order_id'])->lockForUpdate()->first();
        if ($orderL->order_check_status != 0){
            DB::rollBack();
            return 3;
        }

        $result1 = $this->coinTradeOrder->updateOneRecord($order['order_id'],['order_check_status'=>1]);
        $result2 = $this->walletDetail->decrementRecordC(['user_id'=>$order['user_id'],'coin_id'=>$order['coin_id']],'wallet_freeze_balance',bcadd($order['order_trade_money'],$order['order_trade_fee'],8));
//dd(1);
        if ($result1 && $result2){
            TransferToken::dispatch($order)->onQueue('transfer_token');
//            $result = $this->coinServer->sendTransaction($order['center_wallet']['center_wallet_address'],$order['center_wallet']['center_wallet_password'],$order['order_trade_to'],bcmul($order['order_trade_money'],bcpow(10,$order['token']['token_decimal'])),$order['coin_fees']['eth_gaslimit'],$order['coin_fees']['eth_gasprice']);
            if (1
//                is_string($result)
//                && (false !== strpos($result,'0x'))
            ){
                $this->coinTradeOrder->updateOneRecord($order['order_id'],['order_trade_from'=>$order['center_wallet']['center_wallet_address'],'transfer_type'=>1]);
                (new CenterWalletRecord())->saveOneRecord($order['coin_id'],$order['order_trade_fee'],'提币手续费',$order['user_id']);
                DB::commit();return 1;
            }
            DB::rollBack();return 0;//$this->updateOrderStatus($order['order_id']);return 0;
        }

        DB::rollBack();return 0;

    }


    /*手动修改提币状态*/
    public function updateOrderStatus($orderId)
    {
        DB::beginTransaction();
        $order = DB::table('coin_trade_order')->where('order_id',$orderId)->lockForUpdate()->first();
        if ($order->order_check_status != 0){
            DB::rollBack();
            return 3;
        }

        if (
            $this->coinTradeOrder->updateOneRecord($orderId,['order_check_status'=>1,'order_status'=>1])
            && $this->walletDetail->decrementRecordC(['user_id'=>$order->user_id,'coin_id'=>$order->coin_id],'wallet_freeze_balance',bcadd($order->order_trade_money,$order->order_trade_fee,8))
        ){
            $this->redisDelete('WITHDRAW_'.$orderId);
            DB::commit();return 1;
        }

        DB::rollBack();return 2;



    }







}