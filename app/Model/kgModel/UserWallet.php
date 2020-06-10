<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/22
 * Time: 18:14
 */

namespace App\Model\kgModel;


use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    protected $connection = 'mysql_hjkg';

    protected $table = 'kcode_user_wallet';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public $hidden=['wallet_password'];

    public $fillable = ['wallet_address','wallet_password','wallet_out_num','wallet_into_num','tts_into'];


    /*创建一条记录*/
    public function saveOneRecord($userId,$address,$password,$coinId)
    {
        $this->user_id = $userId;
        $this->coin_id = $coinId;
        $this->wallet_address = $address;
        $this->wallet_password = $password;
        $this->create_time = time();
        $this->update_time = time();

        return $this->save();

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

    /*更新*/
    public function updateOneRecord(int $walletId,array $data)
    {
        return $this->find($walletId)->update($data);

    }

    public function incrementC($walletId,$column,$amount)
    {
        return $this->find($walletId)->increment($column,$amount);

    }

    public function decrementC($walletId,$column,$amount)
    {
        return $this->find($walletId)->decrement($column,$amount);

    }

    /*充值信息*/
    public function getRechargeMsg($userId,$walletId)
    {
        $wallet = $this->with('coinSetting')->where(['id'=>$walletId,'user_id'=>$userId])->first();
        if ($wallet) return $wallet->toArray();
        return [];
    }

    /*获取钱包列表*/
    public function getUserWallet(int $userId)
    {
        return $this->with('coinMsg')->where(['user_id'=>$userId])->get()->toArray();

    }

    /*单个钱包*/
    public function getWallet(int $userId,int $coinId,$colum = ['*'],$lock = 0)
    {
        if (!$lock)
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->first($colum);
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->lockForUpdate()->first($colum);
    }

    /*设置信息*/
    public function coinSetting()
    {
        return $this->hasOne('App\Model\CoinSetting','coin_id','coin_id');
    }

    /*货币信息*/
    public function coinMsg()
    {
        return $this->hasOne('App\Model\CoinList','coin_id','coin_id');
    }

    public function user()
    {
        return $this->belongsTo(Users::class,'user_id','user_id');
    }


    public function getUsersByAddresses(array $addresses)
    {
        $users = [];
        $records = $this->whereIn('wallet_address',$addresses)->with('user')->get();

        foreach ($records as $record){
            $record->user->user_icic_wallet = $record;
            $users[] = $record->user;
        }

        return $users;

    }

    //累计卖出
    public function getTotalSell()
    {
        return TradeSell::where(['user_id' => $this->user_id])->sum('trade_num');
    }

    //累计买入
    public function getTotalBuy()
    {
        return TradeBuy::where(['user_id' => $this->user_id])->sum('trade_num');
    }




}