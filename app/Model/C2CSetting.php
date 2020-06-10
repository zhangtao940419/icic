<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 10:20
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class C2CSetting extends Model
{

    protected $table = 'c2c_setting';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function getOneRecord()
    {
        $result = $this->with('coin')->where(['is_usable'=>1])->first();
        if (!$result) return [];
        return $result->toArray();
    }

//    protected $fillable = ['business_buy_order_limit', 'business_sell_order_limit', 'business_buy_order_time_space', 'business_buy_order_confirm_time',
//        'business_sell_order_confirm_time', 'buy_order_auto_handle', 'buy_price', 'sell_price', 'coin_id','user_sell_day_max','unusual_rate'];


    //关联货币表
    public function coin()
    {
        return $this->hasOne('App\Model\CoinType', 'coin_id', 'coin_id')->where(['is_usable'=>1])->select('coin_id', 'coin_name');
    }

}