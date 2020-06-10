<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 15:53
 */

namespace App\Server\UserServers\Dao;


use App\Model\UserIdentify;

class UserIdentifyDao extends UserIdentify
{


    public function getOneRecordByCard($identifyCard)
    {
        return $this->where('identify_card',$identifyCard)->where('is_usable',1)->first();
    }

    public function getOneRecordByUserId(int $userId)
    {
        return $this->with(['user_identify_area'])->where('user_id',$userId)->where('is_usable',1)->first();
    }

    /*更新记录*/
    public function updateOneRecordByUserId(int $userId,array $data)
    {
        return $this->where(['user_id'=>$userId,'is_usable'=>1])->update($data);
    }

    public function saveOneRecord(array $data)
    {
        $this->identify_name = $data['identify_name'];
        $this->identify_card = $data['identify_card'];
        $this->user_id = $data['user_id'];
        $this->identify_sex = $data['identify_sex'];
        $this->identify_area_id = $data['area_id'];
        return $this->save();
    }

    public function getOneRecordC($userId,$userName,$idCard)
    {
        return $this->where(['user_id'=>$userId,'identify_name'=>$userName,'identify_card'=>$idCard])->first();
    }


    public function topAuth($userId,$images,$data = [])
    {
        if (!$data) return $this->updateOneRecordByUserId($userId,$images);

        foreach (array_merge($images,$data,['user_id'=>$userId]) as $key=>$value){
            $this->$key = $value;
        }

        return $this->save();

    }

    //关联用户表
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'user_id');
    }

}