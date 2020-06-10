<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/20
 * Time: 11:33
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ContractPriceAccount extends Model
{

    protected $table = 'contract_price_account';


    protected $primaryKey = 'id';



    public function getBuyMarket($num = 5)
    {
        return $this->where(['type'=>1])->orderByDesc('price')->offset(0)->limit(5)->get();
    }


    public function getSellMarket($num = 5)
    {
        return $this->where(['type'=>2])->orderBy('price','asc')->offset(0)->limit(5)->get();
    }


}