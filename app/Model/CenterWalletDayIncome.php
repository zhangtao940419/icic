<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/9/22
 * Time: 16:47
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CenterWalletDayIncome extends Model
{

    protected $table = 'center_wallet_day_income';

    protected $primaryKey = 'id';



    protected $guarded = [];




    public function addCoinDayIncome($coinId,$amount)
    {
        $rec = $this->where(['coin_id' => $coinId,'day' => date('Ymd')])->first();
        if (!$rec){
            $id = $this->insertGetId([
                'coin_id' => $coinId,
                'day' => date('Ymd'),
                'created_at' => datetime(),
                'updated_at' => datetime()
            ]);
        }else{
            $id = $rec->id;
        }
        return $this->where(['id'=>$id])->increment('amount',$amount);

    }


    public function getTodayIncome()
    {

        $rec = $this->where(['day' => date('Ymd')])->first();

        if ($rec) return $rec->amount;return 0;

    }


}