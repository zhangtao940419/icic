<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/9
 * Time: 11:37
 */

namespace App\Model;


use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class StoRewardFlow extends Model
{

    use Filterable;


    protected $table = 'sto_reward_flow';

    protected $primaryKey = 'id';


    protected $guarded = [];

    public function modelFilter()
    {
        return $this->provideFiltet(\App\ModelFilters\StoRewardFlowFilter::class);
    }

//CREATE TABLE `sto_reward_flow` (
//`id` int(11) unsigned NOT NULL,
//`user_id` int(11) unsigned NOT NULL DEFAULT '0',
//`wallet_id` int(11) unsigned NOT NULL DEFAULT '0',
//`coin_id` tinyint(4) unsigned NOT NULL DEFAULT '0',
//`flow_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '1买入2提取3下级奖励',
//`flow_amount` decimal(25,12) unsigned NOT NULL DEFAULT '0.000000000000',
//`s_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下级uid',
//`s_user_buy_amount` decimal(25,12) unsigned NOT NULL DEFAULT '0.000000000000' COMMENT '下级购买数量',
//`record_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买记录id',
//`fee` decimal(25,12) unsigned NOT NULL DEFAULT '0.000000000000' COMMENT '手续费',
//`status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '1已完成',
//`created_at` datetime DEFAULT NULL,
//`updated_at` datetime DEFAULT NULL,
//PRIMARY KEY (`id`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='sto资产流水';




    public function insertOne($userId,$wallet_id,$coin_id,$flowtype,$flow_amount,$record_id = 0,$s_user_id = 0,$s_user_buy_amount = 0,$fee = 0,$status = 1,$free_stage_id = 0)
    {

        $this->user_id = $userId;

        $this->wallet_id = $wallet_id;
        $this->coin_id = $coin_id;
        $this->flow_type = $flowtype;
        $this->flow_amount = $flow_amount;
        $this->s_user_id = $s_user_id;
        $this->s_user_buy_amount = $s_user_buy_amount;
        $this->fee = $fee;
        $this->status = $status;
        $this->record_id = $record_id;
        $this->free_stage_id = $free_stage_id;

        return $this->save();

    }

    public function checkIsTQ($userId,$freeStageId)
    {
        return $this->where(['user_id' => $userId,'free_stage_id' => $freeStageId])->first();

    }

    public function user()
    {

        return $this->belongsTo(User::class,'user_id','user_id');

    }

    public function s_user()
    {
        return $this->belongsTo(User::class,'s_user_id','user_id');
    }

    public function record()
    {
        return $this->belongsTo(UserBuyStoCoinRecord::class,'record_id','record_id');
    }

    public function coin()
    {
        return $this->belongsTo(CoinType::class,'coin_id','coin_id');
    }



}