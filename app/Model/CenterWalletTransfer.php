<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/15
 * Time: 10:28
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CenterWalletTransfer extends Model
{
//中央钱包和用户钱包互转记录
    protected $table = 'center_wallet_transfer_record';

    protected $primaryKey = 'record_id';

    public function saveOneRecord($userId,$coinId,$walletId,$type,$amount,$fee=0)
    {
        $this->user_id = $userId;
        $this->coin_id = $coinId;
        $this->user_wallet_id = $walletId;
        $this->transfer_amount = $amount;
        $this->transfer_fee = $fee;
        $this->transfer_type = $type;
        return $this->save();
    }

}