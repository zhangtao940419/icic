<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/21
 * Time: 17:31
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ContractPriceFloat extends Model
{

    protected $table = 'contract_price_float';

    protected $primaryKey = 'id';





    public function getNewestPrice()
    {
        $re = $this->latest('id')->where('time','<=',time())->first(['price']);

        if ($re) return $re->price;

        return ContractSetting::query()->where(['is_usable'=>1])->value('start_price');

    }




    public function getNewest()
    {

        return $this->latest('id')->first();

    }

    public function insertOne($coinId,$price,$time)
    {


        $this->coin_id = $coinId;
        $this->price = $price;
        $this->time = $time;
        if ($this->save()) return $this;return false;



    }


    public function check_total_num($num_max = 5000)
    {
        $num = $this->count();
        if ($num > 5000){
            return $this->offset(0)->limit($num-5000)->delete();
        }
        return true;
    }



}