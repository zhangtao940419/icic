<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/19
 * Time: 16:51
 */

namespace App\Model\kgModel;


use Illuminate\Database\Eloquent\Model;

class CoinType extends Model
{


    protected $connection = 'mysql_hjkg';

    protected $table = 'kcode_coin_list';

    protected $primaryKey = 'coin_id';

    public $timestamps = false;

    protected $guarded = [];




    public function getCoin($coinName)
    {
        return $this->where('coin_name',$coinName)->first();
    }





}