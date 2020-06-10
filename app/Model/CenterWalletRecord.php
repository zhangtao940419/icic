<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 15:09
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CenterWalletRecord extends Model
{
//中央钱包手续费流水表
    protected $table = 'real_coin_center_wallet_record';

    protected $primaryKey = 'id';

    protected $guarded = [];
    //zj代表需要入账中央钱包
    public function saveOneRecord($coinId,$totalMoney,$content,$userId = 0,$zj = 1)
    {
        if ($zj) {
//            CenterWallet::where('coin_id', $coinId)->update(array(
//
//                'total_interest_money' => \DB::raw('total_interest_money + ' . $totalMoney),
//
//                'coin_sum_money' => \DB::raw('coin_sum_money + ' . $totalMoney),
//
//            ));
            CenterWallet::where('coin_id', $coinId)->increment('total_interest_money',$totalMoney);
            CenterWallet::where('coin_id', $coinId)->increment('coin_sum_money',$totalMoney);
        }
        $this->coin_id = $coinId;
        $this->total_money = $totalMoney;
        $this->content = $content;
        if ($userId) $this->user_id = $userId;
        return $this->save();

    }

    public function coin()
    {
        return $this->hasOne('App\Model\CoinType','coin_id','coin_id');
    }
    public function user()
    {
        return $this->hasOne(User::class,'user_id','user_id');
    }

}