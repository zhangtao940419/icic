<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 15:52
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class WalletTransferRecords extends Model
{

    protected $table = 'wallet_transfer_records';

    protected $primaryKey = 'id';


    function saveOneRecord(int $walletId,$amount,int $coinId,int $userId,$fee = 0)
    {
        $this->wallet_id = $walletId;
        $this->amount = $amount;
        $this->coin_id = $coinId;
        $this->user_id = $userId;
        $this->fee = $fee;
        return $this->save();

    }



    public function user()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }




}