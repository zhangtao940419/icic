<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/9/21
 * Time: 11:09
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class TransferLockRecord extends Model
{


    protected $table = 'transfer_lock_record';


    protected $primaryKey = 'id';


    protected  $guarded = [];






    public function insertOne($walletid,$userid,$coinid,$amount,$free_time = 0)
    {
        $this->wallet_id = $walletid;
        $this->user_id = $userid;
        $this->coin_id = $coinid;
        $this->amount = $amount;
        $this->free_time = $free_time;

        if ($this->save()) return $this;
        return false;

    }



    public function getUserLockRecords($userid,$walletId)
    {
        return $this->where(['user_id' => $userid,'wallet_id' => $walletId])->get();


    }




}