<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26
 * Time: 16:01
 */

namespace App\Server\UserServers\Dao;

use App\Model\CenterWalletRecord;
use App\Model\CoinType;
use App\Model\OrePoolTransferRecord;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use Illuminate\Support\Facades\DB;
use App\Model\WalletTransferRecords;

class WalletDetailDao extends WalletDetail
{
//    private $walletDetailModel;
//    public function __construct()
//    {
//        $this->walletDetailModel = new WalletDetail();
//    }
    /* 获取用户币种的可用余额
     * @param
     *  coin_id:虚拟币种id；
     *  user_id:用户id
     *   return $balance
     */

    public function getCoinUsableBalance(int $coin_id,int $user_id){

        return $this->select('wallet_usable_balance')->where(['is_usable'=>1,'coin_id'=>$coin_id,'user_id'=>$user_id])->value('wallet_usable_balance');

    }

    //关联用户表
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'user_id');
    }

    //关联货币表
    public function coin()
    {
        return $this->hasOne('App\Model\CoinType', 'coin_id', 'coin_id');
    }

    /* 扣除用户可用余额
     *  @param
     *  coin_id:虚拟币种id；
     *  user_id:用户id；
     *  reduce_money：需要扣除的余额
     */
    public function reduceUsableBalance($coin_id,$user_id,$reduce_money){

        return $this->where(['is_usable'=>1,'coin_id' => $coin_id, 'user_id'=>$user_id])->decrement('wallet_usable_balance',$reduce_money);

    }


    /* 扣除用户冻结余额
     *  @param
     *  coin_id:虚拟币种id；
     *  user_id:用户id；
     *  reduce_money：需要扣除的余额
     */
    public function reduceFreezeBalance($coin_id,$user_id,$reduce_money){
        $FreezeBalance =  $this->getCoinFreezeBalance($coin_id,$user_id)-$reduce_money;
        if(0<$FreezeBalance && $FreezeBalance<0.000001)
            $FreezeBalance=0;
        return $this->where(['is_usable'=>1,'coin_id' =>$coin_id,'user_id'=>$user_id])->update(['wallet_freeze_balance'=>$FreezeBalance]);

    }

    /*  增加用户冻结余额
     *  @param
     *  coin_id:虚拟币种id；
     *  user_id:用户id；
     *  freeze_money：需要冻结的余额
     */
    public function addFreezeBalance($coin_id,$user_id,$freeze_money){

        return $this->where(['is_usable'=>1,'coin_id' => $coin_id, 'user_id'=>$user_id])->increment('wallet_freeze_balance', $freeze_money);

    }

    /*  增加用户可用的场内余额
     *  @param
     *  coin_id:虚拟币种id；
     *  user_id:用户id；
     *  Usable_money：需要添加的余额
     */
    public function addUsableBalance($coin_id,$user_id,$usable_money){

        return $this->where(['is_usable'=>1,'coin_id' => $coin_id,'user_id'=>$user_id])->increment('wallet_usable_balance',(double)$usable_money);

    }


    /*  增加用户可用的场内余额
     *  @param
     *  coin_id:虚拟币种id；
     *  user_id:用户id；
     *  Usable_money：需要添加的余额
     */
    public function addWithdrawBalance($coin_id,$user_id,$usable_money){

        return $this->where(['is_usable'=>1,'coin_id' => $coin_id,'user_id'=>$user_id])->increment('wallet_withdraw_balance',(double)$usable_money);

    }


    /* 获取用户虚拟货币的冻结余额
     * @param
     *  coin_id:虚拟币种id；
     *  user_id:用户id
     *   return $balance
     */

    public function getCoinFreezeBalance(int $coin_id,int $user_id){

        return $this->select('wallet_freeze_balance')->where(['is_usable'=>1,'coin_id'=>$coin_id,'user_id'=>$user_id])->value('wallet_freeze_balance');
    }


    /* 获取用户账户信息
      *  @param
      *  coin_id:虚拟币种id；
      *  user_id:用户id；
      *  reduce_money：需要扣除的余额
     */
    public function getWalletBalance(int $coin_id,int $user_id){

        if(!empty( $result=$this->where(['coin_id'=>$coin_id,'user_id'=>$user_id,'is_usable'=>1])->get()->toArray())){
            return $result[0];
        }
        return 0;
    }

    /* 当取消挂单时更新钱包
     * @param array Balance
     *  coin_id:虚拟币种id；
     *  user_id:用户id；
     *  wallet_usable_balance 可用余额
     *  wallet_freeze_balance 冻结余额
     */
    public function updateBalance($balance){
        return  $this->where(['coin_id'=>$balance['coin_id'],'user_id'=>$balance['user_id'],'is_usable'=>1])->update([
            'wallet_usable_balance'=>$balance['wallet_usable_balance'],
            'wallet_freeze_balance'=>$balance['wallet_freeze_balance'],
        ]);
    }



    /*钱包地址入库*/
    public function saveOneRecord(int $userId,int $coinId,$address='',$account = '',$parentId = 0,$walletPassword = '')
    {
        $this->user_id = $userId;
        $this->coin_id = $coinId;
        if ($address) $this->wallet_address = $address;
        if ($account) $this->wallet_account = $account;
        if ($parentId) $this->parent_id = $parentId;
        if ($walletPassword) $this->wallet_password = $walletPassword;
        return $this->save();
    }

    /*获取用户的钱包列表*/
    public function getUserWallet(int $userId)
    {
        $seeCoin = CoinType::where(['is_see' => 1])->pluck('coin_id')->toArray();
        $records = $this->with(['coinName'=>function($q){
            $q->with('coinIcon');
        }
            ,'coinFees'])->where(['is_usable'=>1,'user_id'=>$userId])->select($this->fields)->whereIn('coin_id',$seeCoin)->get()->toArray();
        foreach ($records as $key=>$record){
            if ($record['parent_id']){
                $records[$key]['wallet_address'] = $this->getRecordByParentId($record['parent_id'])['wallet_address'];
            }
            if ($record['coin_name']['coin_icon'] == null){
                $records[$key]['coin_name']['coin_icon'] = [
                    'coin_icon' => 'http://'.$_SERVER['HTTP_HOST'].'/app/coin_icon/eth.png'
                ];
            }
        }
        $order = ['USDT','BTC','ETH','ICIC'];//排序
//        $recordsN = $records;
        foreach ($records as $key=>$value){

            foreach ($records as $k=>$v){
                if (isset($order[$key]) && $v['coin_name']['coin_name'] == $order[$key]){
                    if ($key != $k){
                        $records[$key] = $v;
                        $records[$k] = $value;
                    }
//                    dd($key);
                }

            }
            //dd('222');
        }
//        dd($records);
        return $records;
    }

    /*根据主键id和userid获取记录,限制两个字段是为了确保权限正确*/
    public function getRecordById(int $walletId,int $userId):array
    {
        $result = $this->select($this->fields)->where(['wallet_id'=>$walletId,'user_id'=>$userId,'is_usable'=>1])->with('coinName','coinFees')->first();
        if (! $result) return [];
        $result = $result->toArray();
        if ($result['parent_id']) {
            $result['wallet_address'] = $this->getRecordByParentId($result['parent_id'])['wallet_address'];
            return $result;
        }
        return $result;
    }

    /*根据地址获取记录*/
    public function getRecordByAddress($address)
    {
        return $this->where(['wallet_address'=>$address,'is_usable'=>1])->first();
    }

    /*根据parentid获取父类信息*/
    public function getRecordByParentId(int $parentId)
    {
        if ($result = $this->find($parentId)) return $result->toArray();return ['wallet_address'=>''];
    }

    /*只根据主键wallet_id获取记录*/
    public function getRecordByWalletId(int $walletId)
    {
        return $this->with('coinFees')->select($this->fields)->find($walletId)->toArray();
    }

    /*字段递减*/
    public function decrementRecord($walletId,$column,$amount)
    {
        return $this->where('wallet_id',$walletId)->decrement($column,$amount);
    }

    /*自定义条件递减*/
    public function decrementRecordC(array $conditions,$column,$amount)
    {
        return $this->where($conditions)->where('is_usable',1)->decrement($column,$amount);
    }
    /*自定义条件递zeng*/
    public function incrementRecordC(array $conditions,$column,$amount)
    {
        return $this->where($conditions)->where('is_usable',1)->increment($column,$amount);
    }

    /*字段递增*/
    public function incrementRecord($walletId,$column,$amount)
    {
        return $this->where('wallet_id',$walletId)->increment($column,$amount);
    }

    /*更新一条记录*/
    public function updateOneRecord($walletId,array $data)
    {
        return $this->where('wallet_id',$walletId)->update($data);
    }

    /*充值信息*/
    public function getRechargeMsg(int $userId,int $walletId)
    {
        $result = $this->with('coinNotice','coinFees','coinName')->select('wallet_address','coin_id','parent_id')->where(['wallet_id'=>$walletId,'user_id'=>$userId])->first();
        if (! $result) return [];
        $result = $result->toArray();
        if ($result['parent_id']) {
            $result['wallet_address'] = $this->getRecordByParentId($result['parent_id'])['wallet_address'];
            return $result;
        }
        return $result;
    }

    /*提币信息*/
    public function getWithdrawMsg(int $userId,int $walletId)
    {
        $result = $this->with('coinNotice','coinFees','coinName')->select('wallet_usable_balance','wallet_withdraw_balance','coin_id','parent_id')->where(['wallet_id'=>$walletId,'user_id'=>$userId])->first();
        if (! $result) return [];
        $result = $result->toArray();
        if ($result['parent_id']) {
            $result['wallet_address'] = $this->getRecordByParentId($result['parent_id'])['wallet_address'];
            return $result;
        }
        return $result;
    }

    /*关联费用表*/
    public function coinFees()
    {
        return $this->hasOne('App\Model\CoinFees','coin_id','coin_id');
    }


    public function centerWallet()
    {
        return $this->hasOne('App\Model\CenterWalletDetail','coin_id','coin_id')->where('is_usable',1)->select(['coin_id','center_wallet_account','center_wallet_address','center_wallet_password']);
    }


    /*模型关联:coinname*/
    public function coinName()
    {
        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')->select(['coin_id','coin_name']);
    }

    /*模型关联:注意事项*/
    public function coinNotice()
    {
        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')->select(['coin_id','coin_name','coin_withdraw_message','coin_recharge_message','coin_recharge_extra_message']);
    }



    /*    public function getWalletUsableBalanceAttribute($value)
            {
                return number_format($value,6);
            }

            public function getWalletFreezeBalanceAttribute($value)
            {
                return number_format($value,6);
            }*/

    //资产划转
    public function transferAccount(int $walletId,int $userId,$amount)
    {
        DB::beginTransaction();
        $wallet = $this->where(['wallet_id'=>$walletId,'user_id'=>$userId])->lockForUpdate()->first();

        if (!$wallet) return 3;
//        if (!$wallet || $wallet['coin_id'] == 5){
//            DB::rollBack();
//            return 3;
//        }

        if (bccomp($amount,$wallet['wallet_withdraw_balance'],8) == 1){
            DB::rollBack();
            return 0;
        }
//        $feeL = 0.007;
        $feeL = $wallet->coin->coin_fees->transfer_fee / 100;
        $fee = bcmul($amount,$feeL,8);
        if (bccomp($fee,0,8) != 0){
            (new CenterWalletRecord())->saveOneRecord($wallet['coin_id'],$fee,'资金划转手续费',$userId);
        }
        if (
            $wallet->decrement('wallet_withdraw_balance',$amount)
            && $wallet->increment('wallet_usable_balance',bcsub($amount,$fee,8))
            && (new WalletTransferRecords())->saveOneRecord($walletId,$amount,$wallet['coin_id'],$userId,$fee)
            && (new WalletFlow())->insertOne($wallet->user_id,$walletId,$wallet->coin_id,$amount,10,2,'资金划转',2)
            && (new WalletFlow())->insertOne($wallet->user_id,$walletId,$wallet->coin_id,$amount - $fee,10,1,'资金划转',1,1,$fee)
        ){
            DB::commit();return 1;
        }
        DB::rollBack();return 2;

    }

    //内部用户划转usdt
    public function transferUSDT($wallet,$amount)
    {

        DB::beginTransaction();
//        $wallet = $this->where(['wallet_id'=>$walletId,'user_id'=>$userId])->lockForUpdate()->first();


        if (bccomp($amount,$wallet['wallet_withdraw_balance'],8) == 1){
            DB::rollBack();
            return 0;
        }
//        $feeL = 0.007;
//        $fee = bcmul($amount,$feeL,8);
        if (
            $wallet->decrement('wallet_withdraw_balance',$amount)
            && $wallet->increment('wallet_usable_balance',$amount)
            && (new WalletTransferRecords())->saveOneRecord($wallet->wallet_id,$amount,$wallet['coin_id'],$wallet['user_id'])
            && (new WalletFlow())->insertOne($wallet->user_id,$wallet->wallet_id,$wallet->coin_id,$amount,10,2,'资金划转',2)
            && (new WalletFlow())->insertOne($wallet->user_id,$wallet->wallet_id,$wallet->coin_id,$amount,10,1,'资金划转',1)
//            && (new CenterWalletRecord())->saveOneRecord($wallet['coin_id'],$fee,'资金划转手续费',$wallet['user_id'])
        ){
            DB::commit();return 1;
        }
        DB::rollBack();return 2;


    }

    //矿池划转
    public function ore_pool_transfer($wallet,$amount,$type)
    {
        try{
            DB::beginTransaction();
            if ($type == 1){
                if ($amount > $wallet->wallet_usable_balance){
                    DB::rollBack();return false;
                }

                if (
                    $wallet->decrement('wallet_usable_balance',$amount)
                    && $wallet->increment('ore_pool_balance',$amount * $wallet->coin->coin_fees->cn_to_ore_times)
                    && (new WalletFlow())->insertOne($wallet->user_id,$wallet->wallet_id,$wallet->coin_id,$amount,14,2,'矿池划转',$type)
                        && (new OrePoolTransferRecord())->insertOne($wallet->wallet_id,$wallet->user_id,$wallet->coin_id,$amount * $wallet->coin->coin_fees->cn_to_ore_times,2)
                ){
                    DB::commit();return true;
                }
                DB::rollBack();return false;

            }else{
                if ($amount > $wallet->wallet_withdraw_balance){
                    DB::rollBack();return false;
                }
                if (
                    $wallet->decrement('wallet_withdraw_balance',$amount)
                    && $wallet->increment('ore_pool_balance',$amount * $wallet->coin->coin_fees->kt_to_ore_times)
                    && (new WalletFlow())->insertOne($wallet->user_id,$wallet->wallet_id,$wallet->coin_id,$amount,14,2,'矿池划转',$type)
                        && (new OrePoolTransferRecord())->insertOne($wallet->wallet_id,$wallet->user_id,$wallet->coin_id,$amount * $wallet->coin->coin_fees->kt_to_ore_times,3)
                ){
                    DB::commit();return true;
                }
                DB::rollBack();return false;
            }



        }catch (\Exception $exception){
            DB::rollBack();return false;
        }


    }


    //钱包流水
    public function coinTradeOrder()
    {
        return $this->hasMany('App\Model\CoinTradeOrder','user_id','user_id');
    }

    public function c2cTrade()
    {
        return $this->hasMany('App\Model\C2CTrade','user_id','user_id');
    }

    public function c2cTradeOrder()
    {
        return $this->hasMany('App\Model\C2CTradeOrder','business_user_id','user_id');
    }

    public function insideTradeBuy()
    {
        return $this->hasMany('App\Model\InsideTradeBuy','user_id','user_id');
    }

    public function insideTradeSell()
    {
        return $this->hasMany('App\Model\InsideTradeSell','user_id','user_id');
    }

    public function insideTradeOrderBuy()
    {
        return $this->hasMany('App\Model\InsideTradeOrder','buy_user_id','user_id');
    }

    public function insideTradeOrderSell()
    {
        return $this->hasMany('App\Model\InsideTradeOrder','sell_user_id','user_id')->where('trade_statu',1);
    }

    public function adminWalletFlow()
    {
        return $this->hasMany('App\Model\Admin\AdminWalletFlow','user_id','user_id');
    }

    public function userInvestmentRecord()
    {
        return $this->hasMany('App\Model\UserInvestmentRecord','user_id','user_id');
    }

    public function walletTransferRecords()
    {
        return $this->hasMany('App\Model\WalletTransferRecords','user_id','user_id');
    }


    public function flow()
    {
        return $this->hasMany(WalletFlow::class,'wallet_id','wallet_id');
    }

}