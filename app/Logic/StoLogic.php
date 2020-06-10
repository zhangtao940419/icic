<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/9
 * Time: 14:57
 */

namespace App\Logic;


use App\Events\STOBuyBehavior;
use App\Http\Response\ApiResponse;
use App\Model\CenterStoWallet;
use App\Model\CoinType;
use App\Model\OrePoolTransferRecord;
use App\Model\StoCoinData;
use App\Model\StoCoinFreeStage;
use App\Model\StoCoinStage;
use App\Model\StoCoinStageDay;
use App\Model\StoRewardFlow;
use App\Model\StoUserWallet;
use App\Model\User;
use App\Model\UserBuyStoCoinRecord;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use App\Traits\RedisTool;
use Illuminate\Support\Facades\DB;

class StoLogic
{

    use ApiResponse,RedisTool;


    protected $userDao;

    protected $stoCoinStage;
    protected $stoCoinData;
    protected $stoCoinStageDay;
    protected $walletDetail;
    protected $coinType;
    protected $stoUserWallet;
    protected $userBuyStoCoinRecord;

    protected $start_time = '8:00';//每天开始开放购买的时间
    protected $end_time = '21:00';//每天结束购买的时间

    protected $single_min = 100;
    protected $single_max = 100000;

    protected $qc_icic_day_total = 6800000;


    public function __construct(User $user,StoCoinStage $stoCoinStage,StoCoinData $stoCoinData,StoCoinStageDay $stoCoinStageDay,WalletDetail $walletDetail,CoinType $coinType
                        ,StoUserWallet $stoUserWallet,UserBuyStoCoinRecord $userBuyStoCoinRecord
    )
    {
        $this->userDao = $user;

        $this->stoCoinStage = $stoCoinStage;
        $this->stoCoinData =$stoCoinData;
        $this->stoCoinStageDay = $stoCoinStageDay;
        $this->walletDetail = $walletDetail;
        $this->coinType = $coinType;
        $this->stoUserWallet = $stoUserWallet;
        $this->userBuyStoCoinRecord = $userBuyStoCoinRecord;


        $single_min = $this->stringGet('sto_single_min');
        $single_max = $this->stringGet('sto_single_max');
        $single_min = $single_min == null ? 100 : $single_min;
        $single_max = $single_max == null ? 100000 : $single_max;

        $this->single_min = $single_min;$this->single_max = $single_max;
    }


    //获取我的推荐人
    public function getMyPUser($userId)
    {


        $user = User::find($userId);

        if ($user->pid == 0){
            $puser = null;
        }else{


            $puser = $user->p_user->user_phone;


        }

        return $this->successWithData(['p_user' => $puser]);

    }



    //绑定推荐人
    public function bindMyPUser($phone)
    {

        $user = current_user();
        if ($user->user_phone == $phone){
            return api_response()->zidingyi('不能绑定自己');
        }

        if ($user->pid){
            return api_response()->zidingyi('已经绑定过推荐人');
        }


        $puser = $this->userDao->getUserByPhone($phone);
        if (!$puser) return api_response()->zidingyi('用户不存在');

        if ($puser->pid == $user->user_id) return api_response()->zidingyi('不能绑定被推荐人手机号');




        $re = $user->update(['pid'=>$puser->user_id]);

        if ($re){
            return api_response()->success();
        }
        return api_response()->error();


    }

    //获取所有sto项目
    public function getAllSTOProject($status)
    {
        $records = [];

        $projects = $this->stoCoinData->with([
            'coin'=>function($q){
                $q->select(['coin_id','coin_name']);
            }
            ,'sto_coin_stage'])->where(['is_usable'=>1])->latest()->get()->toArray();



        if ($status == 0){//0，代表预发行，1代表发行中，2代表阶段已完结',

            foreach ($projects as $project){
                $s1 = 0;$s0 = 0;$s2 = 0;
                if ($project['sto_coin_stage']){
                    foreach ($project['sto_coin_stage'] as $value){
                        if ($value['issue_status'] == 0){
                            $s0++;
                        }elseif ($value['issue_status'] == 1){
                            $s1++;
                        }elseif ($value['issue_status'] == 2){
                            $s2++;
                        }
                    }
                    if ($s1 == 0 && $s0 > 0 && $s2 == 0){
                        $records[] = $project;
                    }
                }
            }

        }elseif ($status == 1){

            foreach ($projects as $project){
                $s1 = 0;$s0 = 0;$s2 = 0;
                if ($project['sto_coin_stage']){
                    foreach ($project['sto_coin_stage'] as $value){
                        if ($value['issue_status'] == 0){
                            $s0++;
                        }elseif ($value['issue_status'] == 1){
                            $s1++;
                        }elseif ($value['issue_status'] == 2){
                            $s2++;
                        }
//                        if ($value['issue_status'] == 1){
//                            $records[] = $project;
//                            break;
//                        }
                    }
                    if ($s1 > 0 || ($s0>0 && $s2 > 0)){
                        $records[] = $project;
                    }
                }
            }
            $records = $this->addQcToIcicProject($records);

        }else{
            foreach ($projects as $project){
                $s1 = 0;$s0 = 0;$s2 = 0;
                if ($project['sto_coin_stage']){
                    foreach ($project['sto_coin_stage'] as $value){
                        if ($value['issue_status'] == 0){
                            $s0++;
                        }elseif ($value['issue_status'] == 1){
                            $s1++;
                        }elseif ($value['issue_status'] == 2){
                            $s2++;
                        }
                    }
                    if ($s1 == 0 && $s0 == 0){
                        $records[] = $project;
                    }
                }
            }


        }



        return api_response()->successWithData(['records' => $records]);

    }

    //动态增加icic/qc项目
    public function addQcToIcicProject($records)
    {
        $project = $this->stoCoinData->with(['coin'=>function($q){
            $q->select(['coin_id','coin_name']);
        }])->where(['coin_id' => 8,'is_usable' => 1])->first();

        if (!$project) return $records;

        $project1 = $project->toArray();

        $records[] = $project1;

        return $records;
    }


    //项目详情
    public function getStoDetail($dataId)
    {
        $coinData = $this->stoCoinData->find($dataId);
        if ($coinData->coin_id == 8) return $this->getQcToIcicDetail($coinData);

        $records = $this->stoCoinStage->with([
            'exchange_coin'=>function($q){
                $q->select(['coin_id','coin_name']);
            }
            ])->where(['data_id' => $dataId,'is_usable' => 1])->get();

        $res = $records->toArray();

        foreach ($records as $k=>$record){

            if ($record->issue_status == 0){
                $res[$k]['open_date'] = $record->get_near_open_day() . ' ' . $record->start_time;
            }


        }
//        dd($records);


        return api_response()->successWithData(['records' => $res,'sto_data' => $records[0]->sto_coin_data]);


    }

    public function getQcToIcicDetail($coinData)
    {
        $records = [[
                "stage_id" => 888888,//阶段id
                "base_coin_id" => $coinData->base_coin_id,
                "exchange_coin_id" => $coinData->coin_id,
                "exchange_rate" => "0.10",
                "data_id" => $coinData->data_id,
                "stage_number" => 1,//阶段;1代表第一阶段...
                "stage_issue_number" => $this->qc_icic_day_total,//token总量
                "stage_issue_remain_number" => $this->qc_icic_day_total,
                "issue_begin_time" => 1565346129,
                "issue_time" => 1,
                "issue_status" => 1,//0，代表预发行，1代表发行中，2代表阶段已完结',只有发行中的才可以进入下一级参与购买
                "pid" => 0,
                "is_usable" => 1,
                "created_at" => "2019-08-09 16:36:37",
                "updated_at" => "2019-08-09 16:36:39",
                "exchange_coin" => [
                    "coin_id" => $coinData->coin_id,
                    "coin_name" => "ICIC"//token名
                ]
            ]
        ];


        return api_response()->successWithData(['records' => $records,'sto_data' => $coinData]);

    }

    //获取正在发行中的阶段详情
    public function getStoStageDetail($stage_id)
    {
        if ($stage_id == 888888) return $this->getQcToIcicStageDetail($stage_id);

        $record = $this->stoCoinStage->with([
            'exchange_coin'=>function($q){
                $q->select(['coin_id','coin_name']);
            },
            'base_coin'=>function($q){
                $q->select(['coin_id','coin_name']);
            },
            'sto_coin_data'=>function($q){
                $q->select(['data_id','is_reward','white_paper']);
            }
        ])->find($stage_id);

        if (!$record || $record->issue_status != 1){
            return api_response()->error();
        }

        $days = get_left_days($record->issue_begin_time);//dd($days);


        $stage_day_record = $this->stoCoinStageDay->where(['stage_id'=>$record->stage_id,'data_id'=>$record->data_id,'issue_day'=>$days])->first();

        if (!$stage_day_record) return api_response()->zidingyi('该阶段已完结');

        $record = $record->toArray();

        $record['sto_coin_stage_day'] = $stage_day_record->toArray();


        $record['today'] = date('Y-m-d');
//        $record['start_time'] = $this->start_time;
//        $record['end_time'] = $this->end_time;

        $record['total_percent'] = get_percent(($record['stage_issue_number']-$record['stage_issue_remain_number']),$record['stage_issue_number'],2);
        $record['sto_coin_stage_day']['today_percent'] = get_percent(($stage_day_record['stage_issue_number']-$stage_day_record['stage_issue_remain_number']),$stage_day_record['stage_issue_number'],2);

        $record['count_down'] = get_daojishi($record['issue_begin_time'],$record['issue_time']);
        $record['issue_end_time'] = date('Y-m-d H:i:s',get_today_zero_timestamps($record['issue_begin_time']) + 24*3600*$record['issue_time']);

        $total_days = $stage_day_record->sto_coin_stage->sto_coin_stage_day;

        $jd_switch = $this->stringGet('jd_switch_'.$stage_day_record->day_id);

        if ($jd_switch){

            $record['total_percent'] = get_percent(($record['stage_issue_number']-$record['stage_issue_remain_number']) - ($stage_day_record['stage_issue_number']-$stage_day_record['stage_issue_remain_number'])+$stage_day_record['stage_issue_number'],$record['stage_issue_number'],2);
            $record['sto_coin_stage_day']['today_percent'] = get_percent(($stage_day_record['stage_issue_number']-0),$stage_day_record['stage_issue_number'],2);

            $record['sto_coin_stage_day']['stage_issue_remain_number'] = 0;

        }

        $selled_num = 0;//dd($total_days->toArray());
        foreach ($total_days as $total_day){
            $sjd_switch = $this->stringGet('jd_switch_'.$total_day->day_id);
            if ($sjd_switch){
                $selled_num += $total_day->stage_issue_number;
            }else{
                $selled_num += ($total_day['stage_issue_number']-$total_day['stage_issue_remain_number']);
            }

//
//            $record['total_percent'] = get_percent(($record['stage_issue_number']-$record['stage_issue_remain_number']) - ($stage_day_record['stage_issue_number']-$stage_day_record['stage_issue_remain_number'])+$stage_day_record['stage_issue_number'],$record['stage_issue_number'],2);


        }

        $record['total_percent'] = get_percent($selled_num,$record['stage_issue_number'],2);
        $record['stage_issue_remain_number'] = $record['stage_issue_number'] - $selled_num;



//        dd(get_daojishi(1565405043,2));

        return api_response()->successWithData(['record'=>$record]);

    }

    public function getQcToIcicStageDetail($stageid)
    {
        $project = $this->stoCoinData->with(['coin'=>function($q){
            $q->select(['coin_id','coin_name']);
        }])->where(['coin_id' => 8,'is_usable' => 1])->first();
        if (!$project) return api_response()->zidingyi('已完结');
        $left = $this->get_qc_to_icic_day_left();
        $record = [
            "stage_id" => $stageid,
			"base_coin_id" => $project->base_coin_id, //基准货币id, 用此id查询对应场内余额
			"exchange_coin_id" => $project->coin_id,
			"exchange_rate" => $this->getQcToIcicStoRate(), //兑换比率 1基准货币=0.1兑换货币
			"data_id" => $project->data_id,
			"stage_number" => 1, //第几期
			"stage_issue_number" => $project->issue_coin_number,
			"stage_issue_remain_number" => $project->issue_coin_number,
			"issue_begin_time" => 1565397955,
			"issue_time" => 1,
			"issue_status" => 1,
			"pid" => 0,
			"is_usable" => 1,
			"created_at" => "2019-08-09 16:36:37",
			"updated_at" => "2019-08-09 16:36:39",
			"exchange_coin" => [
                "coin_id" => $project->coin_id,
				"coin_name" => $project->coin->coin_name //兑换货币
			],
			"base_coin" => [
                "coin_id" => $project->base_coin_id,
				"coin_name" => $project->base_coin->coin_name //基准货币
			],
			"sto_coin_data" =>[
                "data_id" => $project->id,
				"is_reward" => $project->is_reward, //是否需要奖励上级;0表示不奖励,则不显示绑定上级的输入框,并且不显示上级手机
				"white_paper" => $project->white_paper //白皮书
			],
			"sto_coin_stage_day" => [
                "day_id" => $stageid, //当日id
				"stage_id" => $stageid,
				"data_id" => $project->id,
				"coin_id" => $project->coin_id,
				"stage_issue_number" => $this->qc_icic_day_total, //今日总量
				"stage_issue_remain_number" => $left, //剩余//可购买的数量
				"issue_day" => 1, //发行的第几天
				"issue_status" => 1, //'每一天的发行进度，0,代表预发行，1代表发行中，2代表阶段已结束',发行中才可购买
				"is_usable" => 1,
				"created_at" => "2019-08-09 17:31:31",
				"updated_at" => "2019-08-09 17:31:32",
				"today_percent" => get_percent($this->qc_icic_day_total - $left,$this->qc_icic_day_total,2) //今日百分比
			],
			"today" => date('Y-m-d'), //当前时间
			"start_time" => "8:00", //每日开始抢购时间
			"end_time" => "21:00", //每日结束抢购时间
			"total_percent" => 0, //总的百分比
			"count_down" => get_daojishi(time(),1), //倒计时(天:时:分:秒)
			"issue_end_time" => "2019-12-12 00:00:00" //结束时间
		];

        return api_response()->successWithData(['record'=>$record]);

    }

    public function getQcToIcicStoRate()
    {
        return bcmul($this->stringGet('qc_to_icic_sto_rate'),2,1);
    }


    //获取icic可用余额
    public function getICICUsableBalance()
    {
        $user = current_user();

        $icic = $this->coinType->getRecordByCoinName('ICIC');

        $balance = $this->walletDetail->getCoinUsableBalance($icic->coin_id,$user->user_id);


        return api_response()->successWithData(['balance' => $balance]);

    }

    //购买
    public function buy($day_id,$amount)
    {
        if ($day_id == 888888) return $this->buyIcicByQc($day_id,$amount);

        $beishu = 100;
        if ($amount % $beishu != 0) return api_response()->zidingyi('限制购买'.$beishu.'的倍数');
        try{
            $lock_key = 'sto_buy_lock_' . $day_id;
            if (!$this->setKeyLock($lock_key,2)){
                return api_response()->zidingyi('系统繁忙');
            }

            $user = current_user();
            DB::beginTransaction();

            $day = $this->stoCoinStageDay->lockForUpdate()->find($day_id);

            if ($day->is_special_user){
                if (!$user->is_sto_special_user){
                    $this->redisDelete($lock_key);
                    return api_response()->zidingyi('网络拥挤，连线中…');
                }

            }

            if (!$day || $day->issue_status != 1){
                DB::rollBack();
                $this->redisDelete($lock_key);
                return api_response()->zidingyi('尚未开售');
            }

            $jd_switch = $this->stringGet('jd_switch_'.$day_id);
            if ($jd_switch){
                $this->redisDelete($lock_key);
                return api_response()->zidingyi('剩余数量不足');
            }

            $exchangeCoin = $amount * $day->sto_coin_stage->exchange_rate;
            //单笔上下限
            if ($exchangeCoin < $this->single_min || $exchangeCoin > $this->single_max){
                DB::rollBack();
                $this->redisDelete($lock_key);
                return api_response()->zidingyi('单笔限制' . $this->single_min . '-' . $this->single_max);
            }

            if ($exchangeCoin > $day->stage_issue_remain_number){
                DB::rollBack();
                $this->redisDelete($lock_key);
                return api_response()->zidingyi('剩余数量不足');
            }

            $userWallet = $this->walletDetail->where(['user_id'=>$user->user_id,'coin_id'=>$day->sto_coin_stage->base_coin_id])->first();

            if($userWallet->wallet_usable_balance < $amount){
                DB::rollBack();
                $this->redisDelete($lock_key);
                return api_response()->zidingyi('余额不足');
            }

            $userSTOWallet =$this->stoUserWallet->getUserWalletByCoinId($user->user_id,$day->coin_id);

            $re1 = $day->dec_remain_num($exchangeCoin);
            $re2 = $day->sto_coin_stage->dec_remain_num($exchangeCoin);
            $re3 = $userWallet->reduceUsableBalance($day->sto_coin_stage->base_coin_id,$user->user_id,$amount);
            $re4 = $userSTOWallet->inc_usable_balance($exchangeCoin);
            $re5 = (new WalletFlow())->insertOne($user->user_id,$userWallet->wallet_id,$day->sto_coin_stage->base_coin_id,$amount,11,2,'sto买入',1);
            $re6 = $this->userBuyStoCoinRecord->insert_one($user->user_id,$day->data_id,$day->sto_coin_stage->base_coin_id,$day->coin_id,$day->stage_id,$day_id,$amount,$exchangeCoin,$day->sto_coin_stage->exchange_rate);
            $re7 = (new StoRewardFlow())->insertOne($user->user_id,$userSTOWallet->id,$userSTOWallet->coin_id,1,$exchangeCoin,$re6->record_id);
            $re8 = (new CenterStoWallet())->get_wallet($day->sto_coin_stage->base_coin_id,$day->sto_coin_stage->exchange_coin_id)->inc_balance($amount);


            if ($re1 && $re2 && $re3 && $re4 && $re5 && $re6 && $re7 && $re8){
                DB::commit();
                //分发一个事件
                event(new STOBuyBehavior($user->user_id,$day_id));
                $this->redisDelete($lock_key);
                return api_response()->success();
            }


            DB::rollBack();//dd($userWallet);
            $this->redisDelete($lock_key);
            return api_response()->zidingyi('购买失败,请稍后再试');

//
        }catch (\Exception $exception){

            DB::rollBack();
            $this->redisDelete($lock_key);
            return api_response()->zidingyi('系统繁忙');
        }


    }

    public function get_qc_to_icic_day_left()
    {
        $rkey = 'qc_to_icic_day_left_' . date('Ymd');
        $r_left_amount = $this->stringGet($rkey);
        if ($r_left_amount === null) {
            $this->stringSet($rkey,$this->qc_icic_day_total);
            return $this->qc_icic_day_total;
        }else{
            return $r_left_amount;
        }
    }

    //购买icic
    public function buyIcicByQc($day_id,$amount)
    {
        $beishu = 1;
        if ($amount % $beishu != 0) return api_response()->zidingyi('限制购买整数');
        try{


            $lock_key = 'sto_buy_lock_' . $day_id;
            if (!$this->setKeyLock($lock_key,2)){
                return api_response()->zidingyi('系统繁忙');
            }

            $user = current_user();
            $project = $this->stoCoinData->with(['coin'=>function($q){
                $q->select(['coin_id','coin_name']);
            }])->where(['coin_id' => 8,'is_usable' => 1])->first();
            if (!$project) return api_response()->zidingyi('当前不可购买');
            DB::beginTransaction();
            $rate = $this->getQcToIcicStoRate();

            $exchangeCoin = $amount * $rate;
            //检测当日剩余数量
            $left = $this->get_qc_to_icic_day_left();
            if ($left < $exchangeCoin || $exchangeCoin == 0) {
                DB::rollBack();return api_response()->zidingyi('剩余数量不足');
            }

            //单笔上下限
//            if ($exchangeCoin < $this->single_min || $exchangeCoin > $this->single_max){
//                DB::rollBack();
//                $this->redisDelete($lock_key);
//                return api_response()->zidingyi('单笔限制' . $this->single_min . '-' . $this->single_max);
//            }

            $userWallet = $this->walletDetail->where(['user_id'=>$user->user_id,'coin_id'=>$project->base_coin_id])->first();

            if($userWallet->wallet_usable_balance < $amount){
                DB::rollBack();
                $this->redisDelete($lock_key);
                return api_response()->zidingyi('余额不足');
            }

            $coinWallet =$this->walletDetail->getOneRecord($user->user_id,$project->coin_id);

            $re1 = true;
            $re2 = $this->setIncrementFloat('qc_to_icic_day_left_' . date('Ymd'),-1 * $exchangeCoin);
            $re3 = $userWallet->reduceUsableBalance($project->base_coin_id,$user->user_id,$amount);
            $re4 = $coinWallet->addOrePoolBalance($project->coin_id,$user->user_id,$exchangeCoin);
            $re5 = (new WalletFlow())->insertOne($user->user_id,$userWallet->wallet_id,$project->base_coin_id,$amount,11,2,'sto买入',1);
            $re6 = $this->userBuyStoCoinRecord->insert_one($user->user_id,$project->data_id,$project->base_coin_id,$project->coin_id,$day_id,$day_id,$amount,$exchangeCoin,$rate);
//            $re7 = (new StoRewardFlow())->insertOne($user->user_id,$userSTOWallet->id,$userSTOWallet->coin_id,1,$exchangeCoin,$re6->record_id);
            $re7 = (new OrePoolTransferRecord())->insertOne($coinWallet->wallet_id,$user->user_id,$coinWallet->coin_id,$exchangeCoin,7);
            $re8 = (new CenterStoWallet())->get_wallet($project->base_coin_id,$project->coin_id)->inc_balance($amount);


            if ($re1 && $re2 && $re3 && $re4 && $re5 && $re6 && $re7 && $re8){
                DB::commit();
                //分发一个事件
//                event(new STOBuyBehavior($user->user_id,$day_id));
                $this->insert_qc_icic_record_to_redis($day_id,$user->user_phone,$exchangeCoin,'ICIC',$re6->record_id);
                $this->redisDelete($lock_key);
                return api_response()->success();
            }


            DB::rollBack();//dd($userWallet);
            $this->redisDelete($lock_key);
            return api_response()->zidingyi('购买失败,请稍后再试');

//
        }catch (\Exception $exception){

            DB::rollBack();
            $this->redisDelete($lock_key);
            return api_response()->zidingyi('系统繁忙');
        }
    }

    //插入redis hash表
    public function insert_qc_icic_record_to_redis($dayid,$phone,$num,$coinname,$recordid)
    {

        $key = 'stozset'. $dayid;

        $rec = [
            "exchange_trade_number" => $num,
            'user' => [
                "user_id" => 0,
                "user_phone" => substr_replace($phone,'****',3,4)
            ],
            'exchange_coin' => [
                "coin_id" => 0,
                "coin_name" => $coinname
            ],
            'rand' => rand(1,999999)
        ];


        $this->setZadd($key,$recordid,json_encode($rec));
//        $this->redisHset($key,(string)$buy_record->record_id,json_encode($rec));

        $this->setExpire($key,get_today_zero_timestamps(time()) + 24*60*60 - time());



    }

    //获取会员购买订单
    public function getUserBuyOrders($day_id)
    {
//        $records = $this->userBuyStoCoinRecord->with([
//            'user' => function($q){
//                $q->select(['user_id','user_phone']);
//            }
//            ,'exchange_coin'=>function($q){
//                $q->select(['coin_id','coin_name']);
//            }
//        ])->where(['day_id'=>$day_id])->latest('record_id')->get()->toArray();//dd($records);

        $records = [];

        $key = 'stozset' . $day_id;//dd($this->redisDelete($key));
        $zset = $this->getZaddZrevrangebyscore($key,1000000000,0);//dd($this->redisExists($key));
        foreach ($zset as $value)
        {
            $records[] = json_decode($value);
        }

        $xn_switch = $this->stringGet('xn_switch_'.$day_id);//虚拟订单数量

        if ($xn_switch){
            $day = $this->stoCoinStageDay->find($day_id);
            $phones = randMobile($xn_switch,'*');
            foreach ($phones as $phone){
//                $rand_num = [1000,1000,2000,3000,4000][rand(0,4)] * $day->sto_coin_stage->exchange_rate;
                $rand_num = rand(5,200) * 1000;
                $rec = [
                    "exchange_trade_number" => $rand_num,
                    'user' => [
                        "user_id" => 0,
                        "user_phone" => $phone
                    ],
                    'base_coin' => [
                        "coin_id" => 0,
                        "coin_name" => $day->sto_coin_stage->base_coin->coin_name
                    ],
                    'exchange_coin' => [
                        "coin_id" => 0,
                        "coin_name" => $day->sto_coin_stage->exchange_coin->coin_name
                    ]
                ];
                $records[] = $rec;

            }
        }
//        dd($records);
        return api_response()->successWithData(['records' => $records]);


    }

    //获取购买页面的交易记录
    public function getBuyRecord($data_id)
    {
        $user = current_user();
        $records = $this->userBuyStoCoinRecord->with([
            'base_coin'=>function($q){
                $q->select(['coin_id','coin_name']);
            }
            ,'exchange_coin'=>function($q){
                $q->select(['coin_id','coin_name']);
            }
        ])->where(['user_id' => $user->user_id,'data_id' => $data_id])->latest()->get();

        return api_response()->successWithData(['records' => $records]);


    }

    //sto资产
    public function getStoWallets()
    {
        $user = current_user();

//        $coinDatas = $this->stoCoinData->select(['coin_id'])->groupBy('coin_id')->get()->toArray();
        $stoCoinList = CoinType::where(['is_sto'=>1])->pluck('coin_id')->toArray();

        foreach ($stoCoinList as $coinId){
            $this->stoUserWallet->getUserWalletByCoinId($user->user_id,$coinId);
        }
//        dd($coinDatas);

        $wallets = $this->stoUserWallet->with([
            'coin.coinIcon',
            'sto_coin_data'
        ])->where(['user_id'=>$user->user_id])->get();

        $records = $wallets->toArray();

        foreach ($wallets as $k=>$wallet){
            $records[$k]['issue_status'] = $wallet->sto_coin_data == null ? 2 : $wallet->sto_coin_data->getStatus();
        }


        return api_response()->successWithData(['records'=>$records]);



    }

    //资产详情
    public function getWalletDetail($walletId)
    {
        $user = current_user();
        $wallet = $this->stoUserWallet->with([
            'coin.coinIcon',
            'sto_coin_data',
            'sto_wallet_flow'=>function($q){
                $q->latest();
            }
        ])->where(['id'=>$walletId,'user_id'=>$user->user_id])->first();

        if (!$wallet) return api_response()->error();

        $record = $wallet->toArray();
        $record['extract_amount'] = $record['usable_balance'];


        return api_response()->successWithData(['record'=>$record]);


    }




    //获取提取阶段
    public function getCoinFreeStage($coinId)
    {
        $user = current_user();
        $dataId = $this->stoCoinData->getCoinDataId($coinId);

        $totalBuy = $this->userBuyStoCoinRecord->getUserTotalBuy($user->user_id,$dataId);

        $stages = (new StoCoinFreeStage())->with(['user_tq_record'=>function($q) use($user){
            $q->where(['user_id' => $user->user_id]);
        }])->where(['data_id' =>$dataId])->get()->toArray();

        foreach ($stages as $k=>$stage){
            if ($stage['user_tq_record'] != null){
                $stages[$k]['status'] = 2;
            }
            $stages[$k]['free_amount'] = ($stage['free_rate']/100) * $totalBuy;
        }


        return api_response()->successWithData(['total_buy' => $totalBuy,'stages' => $stages]);


    }

    //sto提取
    public function free($coinId,$freeStageId)
    {

        $user = current_user();
        $checkIsTQ = (new StoRewardFlow())->checkIsTQ($user->user_id,$freeStageId);

        if ($checkIsTQ) return api_response()->zidingyi('不可重复提取');

        $freeStage = (new StoCoinFreeStage())->getBuyId($freeStageId);
        if (!$freeStage || date('Ymd') < $freeStage->start_day) return api_response()->zidingyi('未到提取日期');

        $wallet = $this->stoUserWallet->getUserWalletByCoinId($user->user_id,$coinId);

        if ($wallet->usable_balance == 0) return api_response()->zidingyi('余额不足');

        $totalBuy = $this->userBuyStoCoinRecord->getUserTotalBuy($user->user_id,$freeStage->data_id);
        $freeAmount = $totalBuy * ($freeStage->free_rate/100);
        DB::beginTransaction();
        $r1 = $wallet->tq($freeAmount);

        $r2 = (new StoRewardFlow())->insertOne($user->user_id,$wallet->id,$wallet->coin_id,2,$freeAmount*-1,0,0,0,0,1,$freeStageId);
        $cnWallet = $this->walletDetail->getOneRecord($user->user_id,$coinId);

        if (!$cnWallet) {
            DB::rollBack();return api_response()->zidingyi('未知错误');
        }

        $r3 = $cnWallet->addUsableBalance($wallet->coin_id,$wallet->user_id,$freeAmount);
        $r4 = (new WalletFlow())->insertOne($user->user_id,$cnWallet->wallet_id,$coinId,$freeAmount,12,1,'sto提取',1);

        if ($r1 && $r2 && $r3 && $r4){
            DB::commit();return api_response()->success();
        }

        DB::rollBack();
        return api_response()->error();

    }







}