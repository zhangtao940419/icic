<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/15
 * Time: 9:37
 */

namespace App\Server;
use App\Model\CenterWalletRecord;
use App\Model\CoinTradeOrder;
use App\Model\EthToken;
use App\Model\kgModel\CoinType;
use App\Model\kgModel\UserWallet;
use App\Model\OutsideCoinTradeOrder;
use App\Model\WalletDetail;
use App\Server\CoinServers\GethTokenServer;
use App\Server\OutsideTrade\Dao\OutsideWalletDao;
use Illuminate\Support\Facades\DB;

/**
 * Class AdminOutsideCoinServer
 * @package App\Server
 * 场外提币审核server
 */
class AdminOutsideCoinServer
{

    protected $coinServer;

    protected $orderDao;

    protected $walletDao;
    public function __construct()
    {
        $this->orderDao = new OutsideCoinTradeOrder();
        $this->walletDao = new OutsideWalletDao();
    }


    //status:1通过2拒绝
    public function check($orderId,$status):bool
    {
        if (!in_array($status,[1,2])) return false;

        $order = $this->orderDao->getOrder($orderId);

        if (!$order || $order->order_check_status != 0) return false;

        //dd($order);
        if ($status == 1) return $this->pass($order);return $this->refuse($order);

    }


    //拒绝
    private function refuse($order)
    {
        try {

            DB::beginTransaction();
            $order = $this->orderDao->getOrder($order->order_id,['*'],1);
            $wallet = $this->walletDao->getUserWallet($order->user_id,$order->coin_id,['*'],1);
            if ($order->order_check_status != 0) {
                DB::rollBack();
                return false;
            }
            $amount = bcadd($order['order_trade_money'], $order['order_trade_fee'], 8);
            if (
                $order->update(['order_check_status' => 2])
                && $wallet->addWithdrawBalance($wallet->wallet_id,$amount)
                && $wallet->subFreezeBalance($wallet->wallet_id,$amount)
            ) {
                DB::commit();
                return true;
            }
            DB::rollBack();return false;

        }catch (\Exception $exception){
            DB::rollBack();return false;
        }

    }

    //通过
    private function pass($order)
    {//dd($order->ethToken);
        //1区块2场内3场外4矿工内部5星云
        switch ($order->transfer_type){
//            case 1:
//                return false;
//                break;
            case 2:
                return $this->cn($order);
                break;
            case 3:
                return $this->cw($order);
                break;
            case 4:
                return $this->kg($order);
                break;
            case 5:
                return $this->xy($order);
                break;
            default:
                return $this->block($order);
                break;
        }
    }

    //token
    private function cw($order)
    {//dd(1);
        try {
            DB::beginTransaction();
            $order = $this->orderDao->getOrder($order->order_id,['*'],1);
            if ($order->order_check_status != 0) {
                DB::rollBack();
                return false;
            }
            $toUserId = $this->walletDao->getUserIdByAddress($order->order_trade_to);
            $toWallet = $this->walletDao->getOneRecord($toUserId,$order->coin_id,1);//to钱包
            $fromWallet = $this->walletDao->getUserWallet($order->user_id,$order->coin_id,['*'],1);//from钱包
//dd(2);
//        if(!$r1) dd('1'.$r1);if(!$r2) dd('2'.$r2);if(!$r3) dd('3'.$r3);if(!$r4) dd($r4);
            if (
                $toWallet->addUsableBalance($toWallet->wallet_id, $order->order_trade_money)
                && $fromWallet->subFreezeBalance($fromWallet->wallet_id, bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $order->update(['order_check_status' => 1, 'order_status' => 1, 'order_trade_from' => $fromWallet->wallet_address])
                && $this->orderDao->saveOneRecord($toWallet->user_id, $order->coin_id, '', $fromWallet->wallet_address, $order['order_trade_to'], $order['order_trade_money'], 2, 0, 3)
            ) {
                //(new CenterWalletRecord())->saveOneRecord($order['coin_id'], $order['order_trade_fee'], '提币手续费', $order['user_id']);
                DB::commit();
                return true;
            }
            DB::rollBack();return false;

        }catch (\Exception $exception){
            DB::rollBack();return false;

        }

    }


    private function cn($order)
    {//dd($order->coinName->coin_name);
        $cnWalletDao = new WalletDetail();
        $cnOrderDao = new CoinTradeOrder();//dd(1);
        try {
            DB::beginTransaction();
            $order = $this->orderDao->getOrder($order->order_id,['*'],1);
            if ($order->order_check_status != 0) {
                DB::rollBack();
                return false;
            }
            $toUserId = $cnWalletDao->getUserIdByAddress($order->order_trade_to);
            $toWallet = $cnWalletDao->getOneRecord($toUserId,$order->coin_id,['*'],1);//to钱包
            $fromWallet = $this->walletDao->getUserWallet($order->user_id,$order->coin_id,['*'],1);//from钱包
//dd($order['order_trade_to']);
            if (
                $toWallet->addUsableBalance($order->coin_id,$toUserId, $order->order_trade_money)
                && $fromWallet->subFreezeBalance($fromWallet->wallet_id, bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $order->update(['order_check_status' => 1, 'order_status' => 1, 'order_trade_from' => $fromWallet->wallet_address])
                && $cnOrderDao->saveOneRecord($toWallet->user_id, $order->coin_id, '', $fromWallet->wallet_address, $order['order_trade_to'], $order['order_trade_money'], 2, 0, 5)
            ) {
                //(new CenterWalletRecord())->saveOneRecord($order['coin_id'], $order['order_trade_fee'], '提币手续费', $order['user_id']);
                DB::commit();
                return true;
            }
            DB::rollBack();return false;

        }catch (\Exception $exception){
            DB::rollBack();return false;

        }
    }

    public function kg($order)
    {
//        dd($order->coinName->coin_name);

        $kgCoin = (new CoinType())->getCoin($order->coinName->coin_name);
        if (!$kgCoin) return false;
//        dd($kgCoin);
        $kgWalletDao = new UserWallet();
        try {
            DB::beginTransaction();
            $fromWallet = $this->walletDao->getUserWallet($order->user_id,$order->coin_id,['*'],1);//from钱包
            $toUserId = $kgWalletDao->getUserIdByAddress($order->order_trade_to);
            $toWallet = $kgWalletDao->getWallet($toUserId,$kgCoin->coin_id,['*'],1);
            if (
                $toWallet->increment('wallet', $order['order_trade_money'])
                && $toWallet->increment('tts_into',$order['order_trade_money'])
                && $fromWallet->subFreezeBalance($fromWallet->wallet_id, bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $order->update(['order_check_status' => 1, 'order_status' => 1, 'order_trade_from' => $fromWallet->wallet_address])
            ) {
                (new CenterWalletRecord())->saveOneRecord($order['coin_id'], $order['order_trade_fee'], '场外提币手续费', $order['user_id']);
                DB::commit();
                return true;
            }
            DB::rollBack();
            return false;
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }


    }

    public function xy($order)
    {
        $xyCoin = (new \App\Model\XYModel\CoinType())->getCoin($order->coinName->coin_name);
        if (!$xyCoin) return false;
//        dd($xyCoin);
        $xyWalletDao = new \App\Model\XYModel\UserWallet();
        try {
            DB::beginTransaction();
            $fromWallet = $this->walletDao->getUserWallet($order->user_id,$order->coin_id,['*'],1);//from钱包
            $toUserId = $xyWalletDao->getUserIdByAddress($order->order_trade_to);
            $toWallet = $xyWalletDao->getUserWallet($toUserId,$xyCoin->coin_id,['*'],1);
            if (
                $toWallet->addUsableBalance($toUserId,$xyCoin->coin_id, $order['order_trade_money'])
                && $fromWallet->subFreezeBalance($fromWallet->wallet_id, bcadd($order['order_trade_money'], $order['order_trade_fee'], 8))
                && $order->update(['order_check_status' => 1, 'order_status' => 1, 'order_trade_from' => $fromWallet->wallet_address])
            ) {
                (new CenterWalletRecord())->saveOneRecord($order['coin_id'], $order['order_trade_fee'], '场外提币手续费', $order['user_id']);
                DB::commit();
                return true;
            }
            DB::rollBack();
            return false;
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }

    private function block($order)
    {
        //dd($order);
        switch ($order->coinName->coin_name){
            case 'BTC':

                break;
            case 'ETH':

                break;
            default:
                $ethToken = (new EthToken())->getRecordByCoinId($order->coin_id);
                $this->coinServer = new GethTokenServer($ethToken->token_contract_address,$ethToken->token_contract_abi);
                return $this->TokenWithdraw($order);
                break;
        }

    }


    /*比特币的提币逻辑*/
    private function BTCWithdraw($order)
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
    private function ETHWithdraw($order)
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
    private function TokenWithdraw($order)
    {dd($order->centerWallet);
//        dd($this->walletDetail->getOneRecord($order['user_id'],$order['coin_id'])->dec);


        if ($order['order_check_status'] != 0) return 3;
        if ($this->coinServer->getSymbol() === 0) return 0;
        $centerBalance = bcdiv($this->coinServer->getBalance($order['center_wallet']['center_wallet_address']),bcpow(10,$order['token']['token_decimal']));//
dd($centerBalance);
        if (bccomp($order['order_trade_money'],$centerBalance,8) == 1) return 2;//中央钱包没钱

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






}