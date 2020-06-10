<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9
 * Time: 14:26
 */

namespace App\Model\XYModel;


use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{

    protected $connection = 'mysql_xy';
    protected $primaryKey = 'id';

    /*表名称*/
    protected $table = 'user_wallet';

    protected $guarded = [];

    protected $hidden = ['wallet_password'];


    public function getUserWallet($userId,$coinId,$colum = ['*'],$lock = 0)
    {
        if (!$lock)
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->first($colum);
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->lockForUpdate()->first($colum);

    }

    public function getUserIdByAddress($address)
    {
        return $this->where(['wallet_address'=>$address])->value('user_id');
    }

    public function getUserWalletById($walletId,$colum = ['*'],$lock = 0)
    {
        if (!$lock)
            return $this->where(['id'=>$walletId,'is_usable'=>1])->first($colum);
        return $this->where(['id'=>$walletId,'is_usable'=>1])->lockForUpdate()->first($colum);

    }

    public function isAddress($address)
    {
        if ($this->where(['wallet_address'=>$address])->first()) return 1;return 0;
    }

    public function getWalletByAddress($address)
    {
        return $this->where('wallet_address',$address)->first();
    }

    public function getUsableBalance()
    {

    }

    public function updateOneRecordById($id,$data)
    {
        return $this->find($id)->update($data);
    }

    public function addUsableBalance($userId,$coinId,$amount)
    {
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->increment('wallet',$amount);
    }

    public function subUsableBalance($userId,$coinId,$amount)
    {
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->decrement('wallet',$amount);
    }

    public function addFreezeBalance($userId,$coinId,$amount)
    {
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->increment('wallet_freeze',$amount);
    }

    public function subFreezeBalance($userId,$coinId,$amount)
    {
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->decrement('wallet_freeze',$amount);
    }

    public function addBlockInToBalance($userId,$coinId,$amount)
    {
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId,'is_usable'=>1])->increment('block_into_amount',$amount);
    }





}
