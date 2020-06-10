<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/9/27
 * Time: 16:51
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class C2c_User_Last_Trade_Time extends Model
{

    protected $table = 'c2c_user_last_trade_time';


    protected $primaryKey = 'id';


    protected $guarded = [];


    public $timestamps = false;



    public function insertOne($userId,$type=1){

        $re = $this->where(['user_id'=>$userId,'type'=>$type])->first();
        if ($re) return $re->update(['timestamp' => time()]);

        return $this->insert([
            'user_id' => $userId,
            'type' => $type,
            'timestamp' => time()
        ]);
    }



    public function getUserLastTime($userId,$type=1)
    {
        $user = User::find($userId);
        $user_reg_tims = strtotime($user->created_at);
        $re = $this->where(['user_id'=>$userId,'type'=>$type])->first();

        $c2cSetting = (new C2CSetting())->getOneRecord();

        $s_timestamps = (int)$c2cSetting['long_time_not_buy_check_day'] * 24* 3600;

        if ($s_timestamps == 0) return time() + 9999* 24 * 3600;


        if (!$re){
            if ($user_reg_tims > $c2cSetting['start_check_time']) return $user_reg_tims + $s_timestamps;

            return $c2cSetting['start_check_time'] + $s_timestamps;
        }


        return $re->timestamp + $s_timestamps;





    }





}