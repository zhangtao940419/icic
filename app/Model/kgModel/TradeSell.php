<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/7
 * Time: 17:31
 */

namespace App\Model\kgModel;


use Illuminate\Database\Eloquent\Model;

class TradeSell extends Model
{







    protected $connection = 'mysql_hjkg';

    protected $table = 'kcode_trade_sell';

    protected $primaryKey = 'order_id';

    public $timestamps = false;










}