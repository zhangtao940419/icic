<?php

namespace App\Model\XYModel;

use Illuminate\Database\Eloquent\Model;

class CoinType extends Model
{

    protected $connection = 'mysql_xy';
    protected $primaryKey = 'coin_id';

    /*表名称*/
    protected $table = 'coin_type';

    protected $guarded = [];



    public function getCoin($coinName)
    {
        return $this->where(['coin_name'=>$coinName,'is_usable'=>1])->first();
    }



    public function getUsableCoinList()
    {
        return $this->where(['is_usable'=>1])->get();
    }


    public function getCoinByCoinName($coinName,$colum = ['*'])
    {
        return $this->where(['coin_name'=>$coinName,'is_usable'=>1])->first($colum);
    }





}
