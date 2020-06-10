<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/9
 * Time: 11:09
 */

namespace App\Model;

use EloquentFilter\Filterable;

use Illuminate\Database\Eloquent\Model;

class UserBuyStoCoinRecord extends Model
{
    use Filterable;


    protected $table = 'user_buy_sto_coin_record';

    protected $primaryKey = 'record_id';


    protected $guarded = [];

//`record_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
//`user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
//`data_id` int(11) NOT NULL DEFAULT '0' COMMENT 'sto发行虚拟币资料id',
//`base_coin_id` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟货币钱包类型id,基币，base_coin_id币种买入exchange_coin_id',
//`exchange_coin_id` int(11) NOT NULL DEFAULT '0' COMMENT '兑换代币,例如基币是ICIC，兑币是cast，意思就是用icic兑换多少个cast',
//`stage_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户在第几阶段购买的',
//`day_id` int(11) unsigned NOT NULL COMMENT '第几天购买的',
//`base_trade_number` decimal(25,12) NOT NULL DEFAULT '0.000000000000' COMMENT '用户购买sto花费的虚拟币数量',
//`exchange_trade_number` decimal(25,12) NOT NULL DEFAULT '0.000000000000' COMMENT '用户购买sto得到的虚拟币数量',
//`exchange_rate` int(11) NOT NULL DEFAULT '0' COMMENT '兑换比率，整形，计算时需要除于100，cast/icic,例子，如果该值为10，也就是10/100,也就是10个icic，只能兑换1个cast',
//`user_begin_time` int(11) NOT NULL DEFAULT '0' COMMENT '用户购买的时间戳',
//`is_usable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '该条数据是否逻辑删除，0代表不可用，1代表可用',

    public function modelFilter()
    {
        return $this->provideFiltet(\App\ModelFilters\UserBuyStoCoinRecordFilter::class);
    }

    public function insert_one($userId,$data_id,$base_coin_id,$exchange_coin_id,$stage_id,$day_id,$base_trade_number,$exchange_trade_number,$exchange_rate)
    {

        $this->user_id = $userId;
        $this->data_id = $data_id;
        $this->base_coin_id = $base_coin_id;
        $this->exchange_coin_id = $exchange_coin_id;
        $this->stage_id = $stage_id;
        $this->day_id = $day_id;
        $this->base_trade_number = $base_trade_number;
        $this->exchange_trade_number = $exchange_trade_number;
        $this->exchange_rate = $exchange_rate;
        $this->user_begin_time = time();
        if ($this->save()) return $this;
        return 0;



    }

    public function getUserTotalBuy($userId,$dataId)
    {
        return $this->where(['user_id'=>$userId,'data_id'=>$dataId])->sum('exchange_trade_number');
    }


    public function user()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }

    public function base_coin()
    {
        return $this->belongsTo(CoinType::class,'base_coin_id','coin_id');
    }
    public function exchange_coin()
    {
        return $this->belongsTo(CoinType::class,'exchange_coin_id','coin_id');
    }

    public function stage()
    {
        return $this->belongsTo(StoCoinStage::class,'stage_id','stage_id');
    }

    public function day()
    {
        return $this->belongsTo(StoCoinStageDay::class,'day_id','day_id');
    }




}