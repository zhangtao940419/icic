<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 17:12
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class OutsideWalletDetail extends Model
{



    protected $table = 'outside_wallet_detail';


    protected $primaryKey = 'wallet_id';


    protected $guarded = [];

    public function getUserWallets($userId,$colum = ['*'])
    {
        return $this->where('user_id',$userId)->get($colum);
    }


    public function saveOneRecord($userId,$coinId,$pId = 0)
    {
        $this->user_id = $userId;
        $this->coin_id = $coinId;
        if ($pId) $this->parent_id = $pId;
        if ($this->save()) return $this->wallet_id;
        return 0;
    }

    public function getUserWallet($userId,$coinId,$colum = ['*'],$lock = 0)
    {
        if (!$lock)
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->first($colum);
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->lockForUpdate()->first($colum);
    }


    public function updateByWalletId($walletId,$data)
    {
        return $this->where('wallet_id',$walletId)->update($data);
    }

    public function isAddress($address)
    {
        if ($this->where('wallet_address',$address)->first()) return true;
        return false;
    }

}