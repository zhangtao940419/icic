<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/9
 * Time: 11:07
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class StoCoinStage extends Model
{


    protected $table = 'sto_coin_stage';

    protected $primaryKey = 'stage_id';

    protected $insertField = ['base_coin_id', 'exchange_coin_id', 'exchange_rate', 'data_id', 'stage_number', 'stage_issue_number',
        'stage_issue_remain_number', 'issue_begin_time', 'issue_time', 'issue_status', 'start_time','end_time'];

    protected $updateFiled = ['base_coin_id', 'exchange_coin_id', 'exchange_rate', 'data_id', 'stage_number', 'stage_issue_number',
        'stage_issue_remain_number', 'issue_begin_time', 'issue_time', 'issue_status', 'start_time','end_time', 'is_usable'];

    protected $guarded = [];

    public function insertData($data)
    {
        foreach ($this->insertField as $value) {
            $this->$value = $data[$value];
        }
        return $this->save($data);
    }

    public function addStoStage($data){
      return    $this->insertGetId($data);
    }

    //减库存
    public function dec_remain_num($num)
    {
        return $this->where('stage_id',$this->stage_id)->where('stage_issue_remain_number','>=',$num)->decrement('stage_issue_remain_number',$num);
    }

    //根据条件获取数据
    public function getRecordByCondition($where)
    {

        return $this->with('getBaseCoinNames', 'getExchangeCoinNames')->where($where)->get();
    }

    //关联每日
    public function sto_coin_stage_day()
    {
        return $this->hasMany(StoCoinStageDay::class,'stage_id','stage_id');
    }


    public function exchange_coin()
    {
        return $this->belongsTo(CoinType::class,'exchange_coin_id','coin_id');
    }
    public function base_coin()
    {
        return $this->belongsTo(CoinType::class,'base_coin_id','coin_id');
    }

    public function getOneRecord($id)
    {
        return $this->find($id);

    }

    public function sto_coin_data()
    {
        return $this->belongsTo(StoCoinData::class,'data_id','data_id');
    }

    public function updateData($data,$id){

       return $this->where('stage_id', $id)
            ->update($data);

    }

    //获取最近一次的开售时间
    public function get_near_open_day()
    {
        if ($this->issue_status != 0) return false;

        $days = $this->sto_coin_stage_day;
        $rd = null;

        foreach ($days as $day){
            if ($day->issue_status == 0){
                $rd = $day;
                break;
            }
        }

        $timestamps = get_today_zero_timestamps($this->issue_begin_time) + 24*3600*($rd->issue_day-1);

        $date = date('Y-m-d',$timestamps);

        return $date;


    }


    //关联获取虚拟货币的名称
    public function getBaseCoinNames()
    {

        return $this->hasOne('App\Model\CoinType', 'coin_id', 'base_coin_id')->select('coin_id', 'coin_name');

    }

    //关联获取虚拟货币的名称
    public function getExchangeCoinNames()
    {

        return $this->hasOne('App\Model\CoinType', 'coin_id', 'exchange_coin_id')->select('coin_id', 'coin_name');

    }

    //返回状态说明 issue_status
    public function getIssueStatus()
    {
        $issue_status=[
            '0'=>"<font color='red'>预热<font>",
            '1'=>"<font color='green'>发行中<font>",
            '2'=>"<font color='#ff4500'>已完结<font>"
        ];
        return $issue_status[$this->issue_status];
    }


}