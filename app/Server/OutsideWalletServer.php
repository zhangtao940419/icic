<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10
 * Time: 10:07
 */

namespace App\Server;


use App\Model\CoinType;
use App\Model\EthToken;
use App\Model\OmnicoreToken;
use App\Model\OutsideCoinTradeOrder;
use App\Model\OutsideWalletDetail;
use App\Server\Interfaces\CoinServerInterface;
use App\Server\OutsideTrade\Dao\OutsideWalletDao;
use Illuminate\Support\Facades\DB;

class OutsideWalletServer
{

    //场外钱包服务
    protected $coinServer;
    protected $walletDao;
    protected $coinType;
    protected $coinTradeOrder;
    public function __construct()
    {
        //$this->walletDao = new OutsideWalletDetail();
        $this->walletDao = new OutsideWalletDao();
        $this->coinType = new CoinType();
        $this->coinTradeOrder = new OutsideCoinTradeOrder();
    }


    //创建数据库钱包
    public function createAccount($userId,$coinList)
    {
        //$this->walletDao = new OutsideWalletDetail();
        foreach ($coinList as $key=>$value){
            if ($this->walletDao->getUserWallet($userId,$value['coin_id'])){
                unset($coinList[$key]);continue;
            }
            if ($value['coin_name'] == 'ETH'){
                $ethId = (new OutsideWalletDetail())->saveOneRecord($userId,$value['coin_id']);
                if (!$ethId) return false;
                unset($coinList[$key]);
            }

        }
//dd($coinList);
        foreach ($coinList as $key=>$value){
//            if ($this->walletDao->getUserWallet($userId,$value['coin_id'])) continue;
            if (EthToken::where('coin_id',$value['coin_id'])->first()){
                if (!isset($ethId)) {
                    $ethCoin = $this->coinType->getRecordByCoinName('ETH');
                    $ethId = $this->walletDao->where(['user_id' => $userId,'coin_id'=>$ethCoin->coin_id])->value('wallet_id');
                }
                (new OutsideWalletDetail())->saveOneRecord($userId,$value['coin_id'],$ethId);
            }else{//dd($value['coin_name']);
                (new OutsideWalletDetail())->saveOneRecord($userId,$value['coin_id']);
            }

        }

    }




    /*创建区块钱包地址*/
    public function createBlockAccount(CoinServerInterface $coinServer,$walletId,$coinName,$userId)
    {
        $account = '';
        $password = '';
        $address = '';
        if ($coinName == 'BTC'){//dd($coinServer->getWalletInfo());
            if ($coinServer->getWalletInfo() == 0) return false;
            $account = 'outside_user_'.$userId;
            $address = $coinServer->getAccountAddress($account);
        }elseif ($coinName == 'ETH'){
            $password = 'eth_pass_' . $userId;
            $address = $coinServer->newAccount($password);
        }elseif ($coinName == 'USDT'){
            $account = 'outside_user_'.$userId;
            $address = $coinServer->getAccountAddress($account);
        }
        if (!$address) return false;
        return $this->walletDao->updateByWalletId($walletId,['wallet_account'=>$account,'wallet_address'=>$address,'wallet_password'=>$password]);
    }




    ///////////////////////////////////////////////////////
    /*处理更新用户钱包余额的逻辑*/
    public function updateUserWallet(CoinServerInterface $coinServer,$wallet,$token=[])
    {
        $this->coinServer = $coinServer;
        $this->walletDao = new OutsideWalletDao();

        switch ($wallet['coin_name']['coin_name']){
            case 'BTC':
                return $this->updateBTCWallet($wallet);
                break;
            case 'ETH':
                return $this->updateGETHWallet($wallet);
                break;
            case 'USDT':
                return $this->updateUSDTWallet($wallet);
                break;
            default:
                if ($token) return $this->updateTokenWallet($wallet,$token);
                return false;
                break;
        }

    }
    /*处理比特币钱包的更新逻辑*/
    public function updateBTCWallet($wallet)
    {
        if ($this->coinServer->getWalletInfo() == 0) return 0;

//        dd($this->coinServer->getAccountAddress('btp_center'));
        $bitCoreBalance = $this->coinServer->getBalance($wallet['wallet_account']);//dd($this->coinServer->listUnspent());
        if ($bitCoreBalance == 0) return 0;


        $totalBalance = bcadd($bitCoreBalance,$wallet['wallet_divert_amount'],8);
        $usable = bcsub($totalBalance,$wallet['wallet_into_balance_amount'],8);
        if ($usable <= 0) return 1;
        return $this->update($wallet,$usable);

    }

    public function updateUSDTWallet($wallet)
    {
        $propertyId = OmnicoreToken::where('coin_id',$wallet['coin_id'])->value('property_id');
        $realBalance = $this->coinServer->getBalance($wallet['wallet_address'],$propertyId);
        if (!$realBalance) return 0;


        $totalBalance = bcadd($realBalance,$wallet['wallet_divert_amount'],8);
        $usable = bcsub($totalBalance,$wallet['wallet_into_balance_amount'],8);
        if ($usable <= 0) return 1;
        return $this->update($wallet,$usable);
    }

    /*处理以太坊钱包的更新逻辑*/
    public function updateGETHWallet($wallet)
    {
        //dd($this->coinServer->getBalance('0x7b8172e885fba4f0fd593ede603c067a7fb17971')/'1000000000000000000');
        $realBalance = bcdiv($this->coinServer->getBalance($wallet['wallet_address']),bcpow(10,18),8);//只保留八位小数,剩下的自动舍除
        if ($realBalance == -1) return 0;


        $totalBalance = bcadd($realBalance,$wallet['wallet_divert_amount'],8);
        $usable = bcsub($totalBalance,$wallet['wallet_into_balance_amount'],8);
        if ($usable <= 0) return 1;
        return $this->update($wallet,$usable);

    }

    /*处理以太坊代币钱包更新逻辑*/
    public function updateTokenWallet($wallet,$token)
    {
//dd(1);
        //dd($this->coinServer->getBalance('0xb56f204877caa2afc6b587dbe308491917f2c76f'));
        $decimal = bcpow(10,$token['token_decimal'],0);
        $realBalance = bcdiv($this->coinServer->getBalance($wallet['wallet_address']),$decimal,8);//只保留八位小数,剩下的自动舍除
        if ($realBalance == -1) return 0;
        $totalBalance = bcadd($realBalance,$wallet['wallet_divert_amount'],8);
        $usable = bcsub($totalBalance,$wallet['wallet_into_balance_amount'],8);
        if ($usable <= 0) return 1;

        return $this->update($wallet,$usable);
    }


    private function update($wallet,$usable)
    {
        DB::beginTransaction();
        if (
            $this->walletDao->addBlockIntoBalance($wallet['wallet_id'],$usable)
            &&  $this->walletDao->addUsableBalance($wallet['wallet_id'],$usable)
            && $this->coinTradeOrder->saveOneRecord($wallet['user_id'],$wallet['coin_id'],'','',$wallet['wallet_address'],$usable,2)
        ){
            DB::commit();return 1;
        }
        DB::rollBack();return 0;
    }



//////////////////////////////////////////////////////



}