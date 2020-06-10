<?php

namespace App\Model;

use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethServer;
use App\Server\CoinServers\GethTokenServer;
use App\Server\CoinServers\OmnicoreServer;
use Illuminate\Database\Eloquent\Model;
use App\Model\EthToken;
use Illuminate\Support\Facades\DB;
use App\Model\WalletTransferRecords;

class WalletDetail extends Model
{
    /*表名称*/
    protected $table = 'wallet_detail';

    protected $primaryKey = 'wallet_id';

    protected $fields =['wallet_id','coin_id','wallet_account','wallet_address','user_id','wallet_usable_balance','wallet_freeze_balance','wallet_withdraw_balance','wallet_into_balance_amount','wallet_divert_amount','transfer_lock_balance','parent_id'];

//    protected $fillable = ['wallet_usable_balance','wallet_freeze_balance','wallet_withdraw_balance','wallet_into_balance_amount','wallet_divert_amount'];
    protected $guarded = [];

    private $coinServer;

    public function getOrCreateUserWallet($userid,$coinid)
    {
        $re = $this->where(['user_id' => $userid,'coin_id' => $coinid])->first();
        if ($re) return $re;

        $this->user_id = $userid;
        $this->coin_id = $coinid;
        if ($this->save()) return $this;return null;



    }

    /* 获取用户币种的可用余额
     * @param
     *  coin_id:虚拟币种id；
     *  user_id:用户id
     *   return $balance
     */

    public function getCoinUsableBalance(int $coin_id,int $user_id){

        return $this->select('wallet_usable_balance')->where(['is_usable'=>1,'coin_id'=>$coin_id,'user_id'=>$user_id])->value('wallet_usable_balance');

    }

    public function getCoinUsableBalance1(int $coin_id,int $user_id){

        $w = $this->select('wallet_usable_balance')->where(['is_usable'=>1,'coin_id'=>$coin_id,'user_id'=>$user_id])->first();
        if ($w) return $w->wallet_usable_balance;return 0;

    }

    public function getOrePoolTotalAmount($coinId)
    {
        return $this->where(['coin_id'=> $coinId])->sum('ore_pool_balance');
    }
    /* 获取用户币种的矿池余额
 * @param
 *  coin_id:虚拟币种id；
 *  user_id:用户id
 *   return $balance
 */

    public function getCoinOrePoolBalance(int $coin_id,int $user_id){

        return $this->select('ore_pool_balance')->where(['is_usable'=>1,'coin_id'=>$coin_id,'user_id'=>$user_id])->value('ore_pool_balance');

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

    public function userIdentify()
    {
        return $this->hasOne(UserIdentify::class,'user_id','user_id');
    }

    public function getOneWallet($uid,$cid)
    {
        return $this->where(['user_id' => $uid,'coin_id' => $cid])->first();
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

        return $this->where(['user_id'=>$user_id,'coin_id' => $coin_id,'is_usable'=>1])->increment('wallet_usable_balance',$usable_money);

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

    //zengjia矿池余额
    public function addOrePoolBalance($coin_id,$user_id,$usable_money){

        return $this->where(['is_usable'=>1,'coin_id' => $coin_id,'user_id'=>$user_id])->increment('ore_pool_balance',(double)$usable_money);

    }

    public function decOrePoolBalance($usable_money){

        return $this->where(['wallet_id'=>$this->wallet_id])->decrement('ore_pool_balance',(double)$usable_money);

    }


    /*  减少用户可提现余额
     *  @param
     *  coin_id:虚拟币种id；
     *  user_id:用户id；
     *  Usable_money：需要减少的余额
     */
    public function reduceWithdrawBalance($coin_id,$user_id,$reduce_money){

        return $this->where(['is_usable'=>1,'coin_id' => $coin_id,'user_id'=>$user_id])->decrement('wallet_withdraw_balance',(double)$reduce_money);

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

    //根据用户id和coinid获取单条记录
    public function getOneRecord(int $userId,int $coinId,$column = ['*'],$lock = 0)
    {
        if (!$lock)
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->first($column);
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->lockForUpdate()->first($column);
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
        $records = $this->with('coinName','coinFees')->where(['is_usable'=>1,'user_id'=>$userId])->select($this->fields)->get()->toArray();
        foreach ($records as $key=>$record){
            if ($record['parent_id']){
                $records[$key]['wallet_address'] = $this->getRecordByParentId($record['parent_id'])['wallet_address'];
            }
        }
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

    public function getUserIdByAddress($address)
    {
        return $this->where('wallet_address',$address)->value('user_id');
    }

    public function isAddress($address)
    {
        if ($this->where('wallet_address',$address)->first()) return true;
        return false;
    }

    /*根据地址获取记录*/
    public function getRecordByAddress($address)
    {
        return $this->where(['wallet_address'=>$address,'is_usable'=>1])->first();
    }

    /*根据parentid获取父类信息*/
    public function getRecordByParentId(int $parentId)
    {
        return $this->find($parentId)->toArray();//return ['wallet_address'=>''];
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
        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')->select(['coin_id','coin_name','coin_withdraw_message','coin_recharge_message']);
    }



/*    public function getWalletUsableBalanceAttribute($value)
        {
            return number_format($value,6);
        }

        public function getWalletFreezeBalanceAttribute($value)
        {
            return number_format($value,6);
        }*/

    public function transferAccount(int $walletId,int $userId,$amount)
    {
        DB::beginTransaction();
        $wallet = $this->where(['wallet_id'=>$walletId,'user_id'=>$userId])->lockForUpdate()->first();

        if (!$wallet || $wallet['coin_id'] == 5){
            DB::rollBack();
            return 3;
        }
        if (bccomp(0,$amount,8) == 1){
            DB::rollBack();
            return 0;
        }
        if (bccomp($amount,$wallet['wallet_withdraw_balance'],8) == 1){
            DB::rollBack();
            return 0;
        }
        if (
            $wallet->decrement('wallet_withdraw_balance',$amount)
            && $wallet->increment('wallet_usable_balance',$amount)
            && (new WalletTransferRecords())->saveOneRecord($walletId,$amount,$wallet['coin_id'],$userId)
        ){
            DB::commit();return 1;
        }
        DB::rollBack();return 2;

    }



    public function getBlockBalance($address,$coinName)
    {
        if (!$address) return 0;
        switch ($coinName){
            case 'BTC':
                $coinServer = new BitCoinServer();
                $account = $this->where('wallet_address',$address)->first();
                if ($account) $account = $account->wallet_account;
                if (!$account) $account = CenterWalletDetail::where('center_wallet_address',$address)->first()->center_wallet_account;
                return $coinServer->getBalance($account);
                break;
            case 'ETH':
                $coinServer = new GethServer();
                return bcdiv($coinServer->getBalance($address),bcpow(10,18),8);
                break;
            case 'USDT':
                $token = OmnicoreToken::where('coin_name',$coinName)->first();
                $coinServer = new OmnicoreServer();
                return $coinServer->getBalance($address,$token->property_id);
                break;
            default:
                $token = EthToken::where('token_name',$coinName)->first();
                $coinServer = new GethTokenServer($token->token_contract_address,$token->token_contract_abi);
                return bcdiv($coinServer->getBalance($address),bcpow(10,$token->token_decimal),8);
                break;
        }
    }

    public function getExplorer($address,$coinName)
    {
        if (!$address) return '#';
        switch ($coinName){
            case 'BTC':
                return 'https://live.blockcypher.com/btc/address/' . $address;
                break;
            case 'ETH':
                return 'https://etherscan.io/address/' . $address;
                break;
            case 'USDT':
                return 'https://www.omniexplorer.info/address/' . $address;
                break;
            default:
                return 'https://etherscan.io/address/' . $address;
                break;
        }
    }

}
