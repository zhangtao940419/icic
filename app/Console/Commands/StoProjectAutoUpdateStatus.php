<?php

namespace App\Console\Commands;

use App\Model\StoCoinData;
use Illuminate\Console\Command;

class StoProjectAutoUpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sto_status_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新sto项目状态';

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
    public function handle(StoCoinData $stoCoinData)
    {
//        \Log::channel('sto_update_log')->info(datetime() . '更新');

        //获取所有项目
        $datas = $stoCoinData->with(['sto_coin_stage.sto_coin_stage_day'])->get();


//        dd($datas->toArray());
        foreach ($datas as $data) {
//            if (!$data->sto_coin_stage){
//                continue;
//            }
            foreach ($data->sto_coin_stage as $value){
                if ($value->issue_status == 2) continue;//若是已结束直接跳过
                //查询今天是第几天
                $days = get_left_days($value->issue_begin_time);
                if ($days <= 0) continue;//dd($days);

                $s0 = 0;$s1 = 0;$s2 = 0;
                foreach ($value->sto_coin_stage_day as $item){
                    //天数检查
                    if ($item->issue_status == 2){
                        $s2++;continue;
                    }
                    if ($days == $item->issue_day){//当天
                        if (compare_time_with_now($value->start_time) == 1 && compare_time_with_now($value->end_time) == -1){//发行中
                            if ($item->issue_status == 0){
                                $item->update(['issue_status'=>1]);
                            }
                            $s1++;
                        }elseif (compare_time_with_now($value->end_time) == 1){//结束
                            $item->update(['issue_status'=>2]);
                            $s2++;
                        }else{//预热
                            $s0++;
                        }
                    }elseif ($days > $item->issue_day){//后n天
                        $item->update(['issue_status'=>2]);
                        $s2++;
                    }else{
                        $s0++;
                    }
                }
                if ($s1 > 0){//正在发行
                    if ($value->issue_status != 1){
                        $value->update(['issue_status'=>1]);
                    }

                }elseif ($s1 == 0 && $s0 > 0){//预热中

                    if ($value->issue_status != 0){
                        $value->update(['issue_status'=>0]);
                    }
                }elseif ($s1 == 0 && $s0 == 0 && $s2 > 0){//已结束
                    if ($value->issue_status != 2){
                        $value->update(['issue_status'=>2]);
                    }
                }



            }
        }

        return;










    }
}
