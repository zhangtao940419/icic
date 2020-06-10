<?php

namespace App\Observers;

use App\Model\WalletDetail;

class WalletDetailObserver
{
    public function created(WalletDetail $walletDetail)
    {
        \DB::table('wallet_detail_record')->insert([
            'coin_id' => $walletDetail->coin_id,
            'wallet_usable_balance' => $walletDetail->wallet_usable_balance != null ?: 0,
            'wallet_freeze_balance' => $walletDetail->wallet_freeze_balance != null ?: 0,
            'content' => "用户id为" . $walletDetail->user_id . "的用户在" . $walletDetail->created_at . "新增了虚拟货币id为" . $walletDetail->coin_id . "的货币数量为" . ($walletDetail->wallet_usable_balance != null ?: 0)
        ]);
    }

    public function updated(WalletDetail $walletDetail)
    {   // dd($walletDetail);
        \DB::table('wallet_detail_record')->insert([
            'coin_id' => $walletDetail->coin_id,
            'wallet_usable_balance' => $walletDetail->wallet_usable_balance != null ?: 0,
            'wallet_freeze_balance' => $walletDetail->wallet_freeze_balance != null ?: 0,
            'content' => "用户id为" . $walletDetail->user_id . "的用户在" . $walletDetail->updated_at . "虚拟货币id为" . $walletDetail->coin_id . "的货币" . "冻结余额为" . ($walletDetail->wallet_freeze_balance != null ?: 0) . "余额为" . ($walletDetail->wallet_usable_balance != null ?: 0)
        ]);
    }

    public function saved(WalletDetail $walletDetail)
    {
         //dd($walletDetail);
        \DB::table('wallet_detail_record')->insert([
            'coin_id' => $walletDetail->coin_id,
            'wallet_usable_balance' => $walletDetail->wallet_usable_balance != null ?: 0,
            'wallet_freeze_balance' => $walletDetail->wallet_freeze_balance != null ?: 0,
            'content' => "用户id为" . $walletDetail->user_id . "的用户在" . $walletDetail->updated_at . "虚拟货币id为" . $walletDetail->coin_id . "的货币" . "冻结余额为" . ($walletDetail->wallet_freeze_balance != null ?: 0) . "余额为" . ($walletDetail->wallet_usable_balance != null ?: 0)
        ]);
    }
}
