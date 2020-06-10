<?php

namespace App\Console\Commands;

use App\Jobs\contract_auto_jg_kj;
use App\Model\ContractActivity;
use App\Model\ContractPriceFloat;
use App\Model\ContractSetting;
use App\Model\ContractUserBuyRecords;
use App\Traits\RedisTool;
use Illuminate\Console\Command;

class Contract extends Command
{
    use RedisTool;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '合约交易定时任务';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ContractActivity $contractActivity,ContractPriceFloat $contractPriceFloat,ContractSetting $contractSetting,ContractUserBuyRecords $contractUserBuyRecords)
    {
        //
        $activity = $contractActivity->getNewest();





//        if (!$activity) return;
        if ($activity){
            if ($activity->jg_status == 0){//定时交割/开奖

                if ($this->setKeyLock('contract_jg_kj_'.$activity->id,10000)){
                    $jg_times = strtotime($activity->jg_time);
                    $delay = $jg_times - time();
                    if ($delay >= 0)
                    dispatch(new contract_auto_jg_kj($activity->id,$delay));
                }



            }
        }




        //价格浮动
//        if (!$activity) return;
        $contractPriceFloat->check_total_num();
        $setting = $contractSetting->getOne();
        if (!$setting) return;
        $newest = $contractPriceFloat->latest('id')->first();
        if (!$newest) $newest = $contractPriceFloat->insertOne($setting->coin_id,$setting->start_price,time());

        if ($activity && $activity->jg_status == 0){
            $jg_times = strtotime($activity->jg_time);
            if (($jg_times - time()) < (2*60) && ($newest->time < $jg_times)){//已经停仓,直接定交割价

                $lefts = $jg_times - time();
                $nums = ceil($lefts/5);//5s刷新一次

                $opens = $contractUserBuyRecords->open($activity->id);
                if ($opens == 1){
                    $jg_price = getRandFloatNumber($activity->last_price,1,$setting->float_max);
                }elseif ($opens == 2){
                    $jg_price = $activity->last_price;
                }else{
                    $jg_price = getRandFloatNumber($activity->last_price,-1*$setting->float_min,-1);
                }

                $activity->update(['now_price' => $jg_price]);

                $data = [];
                for ($i = 1;$i<=$nums;$i++){
                    if (($i - $nums) == -2){
                        $data[] = [
                            'coin_id' => $activity->coin_id,
                            'price' => getRandFloatNumber($jg_price,-5,5),
                            'time' => $newest->time + $i*5,
                            'created_at' => datetime(),
                            'updated_at' => datetime()
                        ];
                    }elseif (($i - $nums) == -1){
                        $data[] = [
                            'coin_id' => $activity->coin_id,
                            'price' => getRandFloatNumber($jg_price,-2,2),
                            'time' => $newest->time + $i*5,
                            'created_at' => datetime(),
                            'updated_at' => datetime()
                        ];
                    }elseif (($i - $nums) == 0){
                        $data[] = [
                            'coin_id' => $activity->coin_id,
                            'price' => $jg_price,
                            'time' => $jg_times,
                            'created_at' => datetime(),
                            'updated_at' => datetime()
                        ];
                    }else{
                        $data[] = [
                            'coin_id' => $activity->coin_id,
                            'price' => getRandFloatNumber($activity->last_price,-1*$setting->float_min,$setting->float_max),
                            'time' => $newest->time + $i*5,
                            'created_at' => datetime(),
                            'updated_at' => datetime()
                        ];
                    }


                }
                $contractPriceFloat->insert($data);

                return;
            }
        }

        //putong
        if ($newest->time >= (time() + 70)) return;
        $lefts = 70 + time() - $newest->time;
        $nums = ceil($lefts/5);//5s刷新一次
        $data = [];
        for ($i = 1;$i<=$nums;$i++) {
            if (($i - $nums) == 0) {
                $data[] = [
                    'coin_id' => $activity->coin_id,
                    'price' => getRandFloatNumber($activity->last_price, -1 * $setting->float_min, $setting->float_max),
                    'time' => 70 + time(),
                    'created_at' => datetime(),
                    'updated_at' => datetime()
                ];
            } else {
                $data[] = [
                    'coin_id' => $activity->coin_id,
                    'price' => getRandFloatNumber($activity->last_price, -1 * $setting->float_min, $setting->float_max),
                    'time' => $newest->time + $i * 5,
                    'created_at' => datetime(),
                    'updated_at' => datetime()
                ];
            }



        }
        $contractPriceFloat->insert($data);
        return;


    }





}
