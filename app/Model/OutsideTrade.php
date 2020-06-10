<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RedisTool;

class OutsideTrade extends Model
{
    use RedisTool;

    protected $primaryKey = 'trade_id';
    /*
     * 与模型关联的数据表
     * @var string
     */
    protected $table = 'outside_trade';

    private $select_fields=['trade_id','trade_order','user_id','coin_id',
        'trade_des', 'trade_number', 'trade_type', 'location_id', 'currency_id', 'get_money_type', 'trade_price_type', 'trade_premium_rate',
        'trade_with_trust', 'trade_with_confirm', 'trade_is_visual', 'trade_visual_time', 'trade_price','trade_ideality_price',
        'trade_min_limit_price', 'trade_max_limit_price', 'trade_limit_time', 'trade_status'];

    private $slect_order_fileds=['trade_id','trade_is_visual','get_money_type','trade_visual_time','trade_price_type','trade_premium_rate','trade_price','trade_order','user_id','trade_min_limit_price','trade_max_limit_price','trade_number','location_id','coin_id','currency_id','trade_type','created_at'];

    protected $guarded = [];


}

