<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 9:49
 */

namespace App\Server\InsideTrade\Dao;

use App\Jobs\InsideOutTimeAutoTransferToOre;
use App\Jobs\transfer_lock_auto_free;
use App\Model\CenterWalletDayIncome;
use App\Model\CoinType;
use App\Model\InsideSetting;
use App\Model\OrePoolTransferRecord;
use App\Model\TransferLockRecord;
use App\Model\User;
use App\Model\WalletDetail;
use App\Model\CenterWallet;
use App\Model\WalletFlow;
use App\Traits\RedisTool;

class WalletDetailDao
{
    use
    RedisTool;

    protected $walletDetail;
    protected $centerWallet;
    //费率
    protected $rate=0.007;

    public function __construct(WalletDetail $walletDetail,CenterWallet $centerWallet,InsideSetting $insideSetting)
    {
        $this->walletDetail = $walletDetail;
        $this->centerWallet = $centerWallet;
//        empty($this->redisHgetAll('INSIDE_RATE')['rate']) ? : $this->rate = $this->redisHgetAll('INSIDE_RATE')['rate'];
        if (request('base_coin_id') && request('exchange_coin_id')) $this->rate = $insideSetting->getFee(request('base_coin_id'),request('exchange_coin_id'));
    }

    public function getWalletId($userId,$coinId)
    {
        return $this->walletDetail->where(['user_id'=>$userId,'coin_id'=>$coinId])->value('wallet_id');
    }
    public function getWalletDetail(){
        return $this->walletDetail;
    }

    public function insertOneRecord($Param){
        return $this->walletDetail::create($Param);
    }

    public function getCoinUsableBalance(int $coin_id,int $user_id){
        return $this->walletDetail->getCoinUsableBalance($coin_id,$user_id);
    }

    public function getWalletBalance(int $coin_id,int $user_id){
        return $this->walletDetail->getWalletBalance($coin_id,$user_id);
    }

    public function updateBalance($balance){
        return $this->walletDetail->updateBalance($balance);
    }

    public function addUsableBalance($coin_id,$user_id,$usable_money){
        return $this->walletDetail->addUsableBalance($coin_id,$user_id,$usable_money);
    }

    public function reduceFreezeBalance($coin_id,$user_id,$usable_money){
        return $this->walletDetail->reduceFreezeBalance($coin_id,$user_id,$usable_money);
    }

    /* 买家余额变化
     *
     */
    public function dealBuyBalance($buyInSideParam,$trade_num,$unit_price)
    {
        $user = User::find($buyInSideParam['user_id']);
        $exchangeCoin = CoinType::find($buyInSideParam['exchange_coin_id']);
//        $buyFWallet = $this->walletDetail->getOneRecord($user->user_id,$buyInSideParam['base_coin_id']);
        $buyUWallet = $this->walletDetail->getOneRecord($buyInSideParam['user_id'],$buyInSideParam['exchange_coin_id']);

        $buyFreezeBalance =  $this->walletDetail->reduceFreezeBalance($buyInSideParam['base_coin_id'],$buyInSideParam['user_id'],$unit_price*$trade_num);
        if ($exchangeCoin->coin_name != env('COIN_SYMBOL')){//判断币种是否为icic
            $buyUsableBalance =  $this->walletDetail->addWithdrawBalance($buyInSideParam['exchange_coin_id'],$buyInSideParam['user_id'],$trade_num-$this->rate*$trade_num);
        }else{
            $buyUsableBalance = $this->transfer_lock($user,$exchangeCoin,$trade_num-$this->rate*$trade_num);
        }

        if ($user->c2c_long_time_not_buy_status) dispatch(new InsideOutTimeAutoTransferToOre($user->user_id,3));




        if (!$user->pid || $exchangeCoin->coin_name != env('COIN_SYMBOL')){
            $centerCoinBalance = $this->centerWallet->addCenterCoinBalance($buyInSideParam['exchange_coin_id'],$this->rate*$trade_num);
        }else{
            $centerCoinBalance = $this->addPUser($user,$buyInSideParam['exchange_coin_id'],$this->rate*$trade_num);
        }
        $ore_pool_free = true;
        if ($exchangeCoin->coin_name == env('COIN_SYMBOL')) {//判断币种是否为icic
            $ore_pool_free = $this->ore_pool_free($user, $buyInSideParam['exchange_coin_id'], $trade_num);
        }

        (new WalletFlow())->insertOne($buyInSideParam['user_id'],$buyUWallet->wallet_id,$buyUWallet->coin_id,$trade_num-$this->rate*$trade_num,7,1,'场内交易',2,1,$this->rate*$trade_num);

        if($buyFreezeBalance && $buyUsableBalance && $centerCoinBalance && $ore_pool_free)
            return 1;
        return 0;
    }
    //icic买单余额锁定
    public function transfer_lock($user,$coin,$amount)
    {
        $wallet = $this->walletDetail->getOneRecord($user->user_id,$coin->coin_id);

        $wallet->increment('transfer_lock_balance',$amount);
        $rkey = 'transfer_lock_' . $user->user_id . '_' . $coin->coin_id;
        //$locktime = 0.02;//锁定时间;小时
        $locktime = $coin->coin_fees->inside_transfer_lock_time;
        if (!$this->redisExists($rkey)){
            $yc_time = $locktime*60;
//            dispatch(new transfer_lock_auto_free($lre->id,ceil($locktime*60)));
//            $this->stringSetex($rkey,ceil($locktime * 60),1);
        }else{
            $ttl = $this->getTTL($rkey);
            $yc_time = $ttl + $locktime*60;
        }

        $lre = (new TransferLockRecord())->insertOne($wallet->wallet_id,$wallet->user_id,$coin->coin_id,$amount,ceil(time()+$yc_time));
        if (
            $lre
        ){
            dispatch(new transfer_lock_auto_free($lre->id,ceil($yc_time)));
            $this->stringSetex($rkey,ceil($yc_time),1);
            return true;
        }
        return false;

    }

    //买单给上级加余额
    public function addPUser($user,$coinid,$amount)
    {
        $wallet = $this->walletDetail->getOneRecord($user->pid,$coinid);
        $this->walletDetail->addUsableBalance($coinid,$user->pid,$amount * (4/7));

        //给上级增加返佣收益 更新redis累计返佣收益排行榜
        if($user->pid){
            if($coinid == 8){
                $this->setZincrbyScore('icicInvitaRanking',$amount * (4/7),'uid_'.$user->pid);
            }elseif($coinid == 13){
                $this->setZincrbyScore('plcInvitaRanking',$amount * (4/7),'uid_'.$user->pid);
            }
        }

        dispatch(new InsideOutTimeAutoTransferToOre($user->pid,3));

        (new WalletFlow())->insertOne($user->pid,$wallet->wallet_id,$coinid,$amount * (4/7),13,1,'下级奖励',1,1,0,$user->user_id);
        $this->walletDetail->addOrePoolBalance($coinid,$user->pid,$amount * (3/7));
        (new OrePoolTransferRecord())->insertOne($wallet->wallet_id,$wallet->user_id,$wallet->coin_id,$amount * (3/7),5,1,$user->user_id);

        return true;
    }

    //释放矿池余额
    public function ore_pool_free($user,$coinId,$amount)
    {
        $wallet = $this->walletDetail->getOneRecord($user->user_id,$coinId);
//        if ($wallet->coin->coin_name != env('COIN_SYMBOL')) return true;
        if ($wallet->ore_pool_balance < 1) return true;
        $free_num = round(($wallet->coin->coin_fees->ore_pool_free_rate / 100) * $amount ,0);
        $free_num = min($free_num,$wallet->ore_pool_balance);
        $free_num = floor($free_num);
        if ($free_num < 1) return true;

        if ($wallet->increment('wallet_usable_balance',$free_num)
            && (new WalletFlow())->insertOne($wallet->user_id,$wallet->wallet_id,$wallet->coin_id,$free_num,15,1,'挖矿',1)
            && $wallet->decrement('ore_pool_balance',$free_num)
            && (new OrePoolTransferRecord())->insertOne($wallet->wallet_id,$wallet->user_id,$wallet->coin_id,$free_num * -1,4)
        ){
            return true;
        }

        return false;


    }

    /* 卖家余额变化
     */
    public function dealSellBalance($sellInSideParam,$trade_num,$unit_price)
    {
        $sellWallet = $this->walletDetail->getOneRecord($sellInSideParam['user_id'],$sellInSideParam['base_coin_id']);
        $sellUsableBalance =   $this->walletDetail->addWithdrawBalance($sellInSideParam['base_coin_id'],$sellInSideParam['user_id'],$unit_price*$trade_num-$unit_price*$trade_num*$this->rate);


        $sellFreezeBalance =   $this->walletDetail->reduceFreezeBalance($sellInSideParam['exchange_coin_id'],$sellInSideParam['user_id'], $trade_num);

        $centerCoinBalance = $this->centerWallet->addCenterCoinBalance($sellInSideParam['base_coin_id'],$unit_price*$trade_num*$this->rate);
        $this->centerWalletDayInc($sellInSideParam['base_coin_id'],$sellInSideParam['exchange_coin_id'],$unit_price*$trade_num*$this->rate);

        (new WalletFlow())->insertOne($sellInSideParam['user_id'],$sellWallet->wallet_id,$sellWallet->coin_id,$unit_price*$trade_num-$unit_price*$trade_num*$this->rate,7,1,'场内交易',2,1,$unit_price*$trade_num*$this->rate);
        if($sellUsableBalance && $sellFreezeBalance && $centerCoinBalance) return 1;
        return 0;
    }

    //中央钱包日统计累加
    public function centerWalletDayInc($baseCoinId,$exchangeCoinId,$amount)
    {
        $exchangeCoin = CoinType::find($exchangeCoinId);
        if ($exchangeCoin->coin_name != env('COIN_SYMBOL')) return true;

        (new CenterWalletDayIncome())->addCoinDayIncome($baseCoinId,$amount);
        return true;
    }


}
