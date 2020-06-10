<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CoinType extends Model
{
    /*表名称*/
    protected $table = 'fictitious_coin_type';

    protected $primaryKey = 'coin_id';

    private $fields =['coin_id','coin_name', 'coin_icon'];

//    protected $fillable = ['coin_name', 'coin_icon', 'coin_content'];
    protected $guarded = [];

    //根据条件获取数据
    public function getRecordByCondition($where){

        return $this->where($where)->get();

    }

    public function getOutsideCoin($column = ['*'])
    {
        return $this->where('is_outside',1)->get($column);
    }

    /*获取所有的币种信息*/
    public function getAllCoinType():array
    {
        return $this->select($this->fields)->where(['is_usable'=>1])->get()->toArray();
    }

    //关联币币交易汇率
    public function coin_transaction()
    {
        return $this->hasOne('App\Model\Admin\CoinTransaction', 'coin_id');
    }


    //关联场外交易
    public function outsidetrade()
    {
        return $this->belongsTo('App\Model\OutsideTrade', 'coin_id');
    }

    //关联个人钱包
    public function walletDetail()
    {
        return $this->belongsTo('App\Model\WalletDetail', 'coin_id');
    }


    //关联中心钱包
    public function centerWallet()
    {
        return $this->hasOne('App\Model\CenterWallet', 'coin_id');
    }

    public function ethtoken()
    {
        return $this->belongsTo('App\Model\EthToken', 'coin_id');
    }


    /*根据coinname查询单条*/
    public function getRecordByCoinName($coinName)
    {
        return $this->where(['is_usable'=>1,'coin_name'=>$coinName])->first();
    }

    /*根据id查询*/
    public function getRecordById(int $id)
    {
        return $this->find($id)->toArray();
    }

    /*获取*/
    public function getExtraMsg(int $id)
    {
        return $this->where(['is_usable'=>1,'coin_id'=>$id])->select('coin_withdraw_limit','coin_withdraw_message','coin_recharge_message')->first()->toArray();
    }



    //获取货币名称
    public function getCoinName($data)
    {
        return $this->where('is_usable', 1)->where($data)->pluck('coin_name')->first();
    }

    //获取货币交易对
    public function getChanges()
    {
        $array = [];
        foreach ($this->all() as $v) {
            foreach ($this->all() as $i) {
                if ($v != $i) {
                    $array[] = 'INSIDE_TEAM_' . $v->coin_id . '_' . $i->coin_id;
                }
            }
        }

        return $array;
    }
     /*获取特定币种*/
    public function getSomeCoin(array $coin):array
    {
      return $this->select($this->fields)->where('is_usable',1)->whereIn('coin_id',$coin)->get()->toArray();
    }

//    //关联场内交易信息
//    public function insidetrade()
//    {
//        return $this->belongsTo('App\Model\CoinType', 'base_coin_id', 'coin_id');
//    }

    //关联邀请表
    public function invitation()
    {
        return $this->hasOne(Invitation::class, 'coin_id', 'coin_id');
    }

    //币种图表
    public function coinIcon()
    {
        return $this->hasOne('App\Model\CoinDes','coin_symbol','coin_name')->select(['coin_symbol','coin_icon']);
    }

    public function coin_des()
    {
        return $this->hasOne('App\Model\CoinDes','coin_symbol','coin_name');
    }

    public function coin_fees()
    {
        return $this->hasOne(CoinFees::class,'coin_id','coin_id');
    }

    //货币总和
    public function totalAmount($coinId){
        $userT = WalletDetail::where('coin_id',$coinId)->first([\DB::raw('SUM(wallet_usable_balance+wallet_withdraw_balance+wallet_freeze_balance) as total')])->total;
        $centerT = CenterWallet::where('coin_id',$coinId)->value('total_interest_money');
        $stoT = (new CenterStoWallet())->getBalance($coinId);
        return bcadd($userT,$centerT,8) + $stoT;
    }
    //场内总和
    public function totalInside($coinId)
    {
        $totalInside = WalletDetail::where('coin_id',$coinId)->sum('wallet_usable_balance');
        return $totalInside;
    }
    //可提总和
    public function totalWithdraw($coinId)
    {
        $totalInside = WalletDetail::where('coin_id',$coinId)->sum('wallet_withdraw_balance');
        return $totalInside;
    }
//商家可提总和
    public function bTotalWithdraw($coinId)
    {
        $buserids = User::where(['is_business'=>1])->pluck('user_id')->toArray();
        $totalInside = WalletDetail::where('coin_id',$coinId)->whereIn('user_id',$buserids)->sum('wallet_withdraw_balance');
        return $totalInside;
    }

    //用户货币总和
    public function userTotalAmount($coinId){
        $userT = WalletDetail::where('coin_id',$coinId)->first([\DB::raw('SUM(wallet_usable_balance+wallet_withdraw_balance+wallet_freeze_balance) as total')])->total;
        return $userT;
    }

    //用户冻结总和
    public function freezeAmount($coinId){
        return WalletDetail::where('coin_id',$coinId)->sum('wallet_freeze_balance');
    }

    //手续费总和
    public function feeAmount($coinId){
        return CenterWallet::where('coin_id',$coinId)->value('total_interest_money');
    }

    public function blockAmount($coinId)
    {
        return WalletDetail::where('coin_id',$coinId)->sum('wallet_into_balance_amount');

    }


    public function stoIncome($coinId)
    {
        $re = (new CenterStoWallet())->getBalance($coinId);
        return $re;
    }

}
