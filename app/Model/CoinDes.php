<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/6
 * Time: 11:28
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CoinDes extends Model
{

    protected $table = 'coin_des';

    protected  $primaryKey = 'id';

    protected $guarded = [];




    public function getCoinIconAttribute($value)
    {
        try{
            return 'http://' . $_SERVER['HTTP_HOST'] . $value;
        }catch (\Exception $exception){
            return $value;
        }

    }

}