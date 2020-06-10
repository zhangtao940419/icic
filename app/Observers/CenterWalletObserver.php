<?php

namespace App\Observers;

use App\Model\CenterWallet;

class CenterWalletObserver
{
    public function created(CenterWallet $centerWallet)
    {
        \DB::table('real_coin_center_wallet_record')->insert([
            'coin_id' => $centerWallet->coin_id,
            'total_money' => $centerWallet->total_money != null ?: 0,
            'content' => "中心钱包在" . $centerWallet->created_at . "新增了虚拟货币id为" . $centerWallet->coin_id . "的货币". "数量为" . ($centerWallet->total_money != null ?: 0)
        ]);
    }

    public function updated(CenterWallet $centerWallet)
    {
        \DB::table('real_coin_center_wallet_record')->insert([
            'coin_id' => $centerWallet->coin_id,
            'total_money' => $centerWallet->total_money != null ?: 0,
            'content' => "中心钱包中" . $centerWallet->CoinType->coin_name . "货币在" . $centerWallet->updated_at . "变为了" .($centerWallet->total_money != null ?: 0)
        ]);
    }


    public function saved(CenterWallet $centerWallet)
    {
        \DB::table('real_coin_center_wallet_record')->insert([
            'coin_id' => $centerWallet->coin_id,
            'total_money' => $centerWallet->total_money != null ?: 0,
            'content' => "中心钱包中" . $centerWallet->CoinType->coin_name . "货币在" . $centerWallet->updated_at . "变为了" .($centerWallet->total_money != null ?: 0)
        ]);
    }


}
