<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/9
 * Time: 11:06
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class StoCoinData extends Model
{


    protected $table = 'sto_coin_data';

    protected $primaryKey = 'data_id';

    protected $insertField = ['coin_id','base_coin_id','total_coin_issuance','issue_coin_number','img','des_img','des','is_reward','white_paper'];

    protected $updateFiled = ['coin_id','base_coin_id','total_coin_issuance','issue_coin_number','des','img','des_img','is_usable','is_reward','white_paper','first_reward_rate','reward_rate'];

    protected $guarded = [];

    public function insertData($data){
        foreach ($this->insertField as $value){
            $this->$value=$data[$value];
        }
        return $this->save($data);
    }
    //根据条件获取数据
    public function getRecordByCondition($where){

        return $this->with('getCoinNames','getBaseCoinNames')->where($where)->get();
    }


    public function getOneRecord($id){

       return $this->with('getCoinNames','getInvestNames')->find($id);

    }
    public function getCoinDataId($coinid)
    {
        return $this->where(['coin_id' => $coinid])->first(['data_id'])->data_id;
    }

     //更新
    public function updateData($data,$id){

        $sto =  $this->find($id);

        foreach ($data as $key=> $value){
            if(in_array($key,$this->updateFiled))
            $sto->$key=$data[$key];
        }
        return $sto->save($data);
    }



    public function sto_coin_stage()
    {
        return $this->hasMany(StoCoinStage::class,'data_id','data_id');
    }



    public function coin()
    {
        return $this->belongsTo(CoinType::class,'coin_id','coin_id');
    }

    public function base_coin()
    {
        return $this->belongsTo(CoinType::class,'base_coin_id','coin_id');
    }

    public function getAllRecords($where){

        return $this->with('getCoinNames','getBaseCoinNames')->where($where)->get();
    }




    //关联获取虚拟货币的名称
    public function getCoinNames(){

        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')->select('coin_id','coin_name');

    }

    //关联获取虚拟货币的名称
    public function getBaseCoinNames(){

        return $this->hasOne('App\Model\CoinType','coin_id','base_coin_id')->select('coin_id','coin_name');

    }

    public function getImgAttribute($v)
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . $v;

    }

    public function getDesImgAttribute($v)
    {
        return env('QI_NIU_URL') . $v;
    }

    public function getWhitePaperAttribute($v)
    {
        return env('QI_NIU_URL') . $v;
    }

    //获取状态0，代表预热中，1代表在售，2代表已完结',
    public function getStatus()
    {
        $stages = $this->sto_coin_stage;
        $s0 = 0;$s1 = 0;$s2 = 0;
        foreach ($stages as $stage){
            if ($stage->issue_status == 0){
                $s0++;
            }elseif ($stage->issue_status == 1){
                $s1++;
            }else{
                $s2++;
            }
        }

        if ($s1 == 0 && $s0 != 0){
            return 0;
        }elseif ($s1 != 0){
            return 1;
        }else{
            return 2;
        }


    }




}