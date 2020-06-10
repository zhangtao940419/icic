<?php

namespace App\Listeners;

use App\Events\STOBuyBehavior;
use App\Model\CenterStoWallet;
use App\Model\StoCoinStageDay;
use App\Model\StoRewardFlow;
use App\Model\StoUserWallet;
use App\Model\User;
use App\Model\UserBuyStoCoinRecord;
use App\Traits\RedisTool;
use function GuzzleHttp\Psr7\str;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class STOBuyListener
{
    use RedisTool;

    protected $userBuyStoCoinRecord;
    protected $stoCoinStageDay;
    protected $stoUserWallet;

    protected $first_rate = 0.01;//初次购买返还上级比例
    protected $normal_rate = 0.01;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserBuyStoCoinRecord $userBuyStoCoinRecord,StoUserWallet $stoUserWallet,StoCoinStageDay $stoCoinStageDay)
    {
        //
        $this->userBuyStoCoinRecord = $userBuyStoCoinRecord;
        $this->stoCoinStageDay = $stoCoinStageDay;
        $this->stoUserWallet = $stoUserWallet;

//        $first_percent = $this->stringGet('sto_first_percent');
//        $normal_percent = $this->stringGet('sto_normal_percent');


//        $first_percent = $first_percent == null ? 2 : $first_percent;
//        $normal_percent = $normal_percent == null ? 1 : $normal_percent;
//
//        $this->first_rate = $first_percent / 100;
//        $this->normal_rate = $normal_percent / 100;

    }

    /**
     * Handle the event.
     *
     * @param  STOBuyBehavior  $event
     * @return void
     */
    public function handle(STOBuyBehavior $event)
    {
        $s_user = User::find($event->userId);

        $buy_record = $this->userBuyStoCoinRecord->where(['user_id'=>$event->userId,'day_id'=>$event->dayId])->latest('record_id')->first();//会员该笔订单

        $this->insert_to_redis($buy_record);

        $p_user = $s_user->p_user;
        if (!$p_user) return;

        $sto_coin_stage_day = $this->stoCoinStageDay->with(['sto_coin_data'])->find($event->dayId);


        //
        $buyRecordNum = $this->userBuyStoCoinRecord->where(['user_id'=>$event->userId,'data_id'=>$sto_coin_stage_day->data_id])->count();
        if ($buyRecordNum == 0) return;

//        dd($buyRecordNum);

        if ($sto_coin_stage_day->sto_coin_data->is_reward == 0) return;
        $this->first_rate = $sto_coin_stage_day->sto_coin_data->first_reward_rate / 100;
        $this->normal_rate = $sto_coin_stage_day->sto_coin_data->reward_rate / 100;


        if ($buyRecordNum == 1){//第一次购买

            $s_user_buy_total_num = $buy_record->exchange_trade_number;//用户首次购买的数量

            $p_user_buy_total_num = $this->userBuyStoCoinRecord->where(['user_id'=>$p_user->user_id,'data_id'=>$sto_coin_stage_day->data_id])->sum('exchange_trade_number');

            $usable_num = min($s_user_buy_total_num,$p_user_buy_total_num);
            if ($usable_num == 0) return;

            $reward_num = $this->first_rate * $usable_num;//加到上级的余额中

            $p_user_wallet = $this->stoUserWallet->getUserWalletByCoinId($p_user->user_id,$sto_coin_stage_day->coin_id);
            DB::beginTransaction();

            $re1 = $p_user_wallet->inc_usable_balance($reward_num);//加余额
            $re2 = (new StoRewardFlow())->insertOne($p_user->user_id,$p_user_wallet->id,$p_user_wallet->coin_id,3,$reward_num,$buy_record->record_id,$s_user->user_id,$s_user_buy_total_num);

            if ($re1 && $re2){
                DB::commit();return;
            }

            DB::rollBack();return;

        }else{//不是第一次购买
//dd(1);
            $s_user_buy_total_num = $buy_record->exchange_trade_number;//用户该次购买的数量

            //上一笔订单
            $s_user_last_buy_record = $this->userBuyStoCoinRecord->where(['user_id'=>$event->userId])->where('record_id','!=',$buy_record->record_id)->latest()->first();

            //上级购买数量
            $p_user_buy_total_num = $this->userBuyStoCoinRecord->where(['user_id'=>$p_user->user_id,'data_id'=>$sto_coin_stage_day->data_id])->where('user_begin_time','>',$s_user_last_buy_record->user_begin_time)->where('user_begin_time','<',$buy_record->user_begin_time)->sum('exchange_trade_number');
//            dd($p_user_buy_total_num);
            $usable_num = min($s_user_buy_total_num,$p_user_buy_total_num);
            if ($usable_num == 0) return;

            $reward_num = $this->normal_rate * $usable_num;//加到上级的余额中

            $p_user_wallet = $this->stoUserWallet->getUserWalletByCoinId($p_user->user_id,$sto_coin_stage_day->coin_id);
            DB::beginTransaction();

            $re1 = $p_user_wallet->inc_usable_balance($reward_num);//加余额
            $re2 = (new StoRewardFlow())->insertOne($p_user->user_id,$p_user_wallet->id,$p_user_wallet->coin_id,3,$reward_num,$buy_record->record_id,$s_user->user_id,$s_user_buy_total_num);

            if ($re1 && $re2){
                DB::commit();return;
            }

            DB::rollBack();return;


        }





    }



    //sto中央钱包
    public function handle_sto_center_wallet($buy_record)
    {

        $sto_center_wallet = CenterStoWallet::query()->where(['base_coin_id'=>$buy_record->base_coin_id,'exchange_coin_id'=>$buy_record->exchange_coin_id])->first();

        if (!$sto_center_wallet){
            if ($this->setKeyLock('create_sto_center_wallet_'.$buy_record->base_coin_id.$buy_record->exchange_coin_id,3)){
                $sto_center_wallet = (new CenterStoWallet())->create_wallet($buy_record->base_coin_id,$buy_record->exchange_coin_id);
            }
        }



        $sto_center_wallet->inc_balance($buy_record->base_trade_number);


    }


    //插入redis hash表
    public function insert_to_redis($buy_record)
    {

        $key = 'stozset'. $buy_record->day_id;

        $rec = [
            "exchange_trade_number" => $buy_record->exchange_trade_number,
            'user' => [
                "user_id" => 0,
                "user_phone" => substr_replace($buy_record->user->user_phone,'****',3,4)
            ],
            'exchange_coin' => [
                "coin_id" => 0,
                "coin_name" => $buy_record->exchange_coin->coin_name
            ]
        ];


        $this->setZadd($key,$buy_record->record_id,json_encode($rec));
//        $this->redisHset($key,(string)$buy_record->record_id,json_encode($rec));

        $this->setExpire($key,24*60*60);



    }




}
