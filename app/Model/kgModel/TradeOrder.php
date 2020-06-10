<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/7
 * Time: 17:26
 */

namespace App\Model\kgModel;


use Illuminate\Database\Eloquent\Model;

class TradeOrder extends Model
{



    protected $connection = 'mysql_hjkg';

    protected $table = 'kcode_trade_order';

    protected $primaryKey = 'order_id';

    public $timestamps = false;






}