<?php

namespace App\Model\XYModel;

use Illuminate\Database\Eloquent\Model;

class UserWalletLog extends Model
{
    protected $connection = 'mysql_xy';
    protected $primaryKey = 'id';

    /*表名称*/
    protected $table = 'user_wallet_log';

    protected $guarded = [];

    protected $insertFields = ['user_id','coin_id','amount','fee','log_type'];

    public function user(){
        return $this->belongsTo('App\Model\User','user_id');
    }

    public static function log_types(){
        //receive_award每日领取，buy_insurance购买保险
        return [
            'receive_award'=>'每日领取',
            'buy_insurance'=>'购买保险',
            'withdraw' => '提币',
            'recharge' => '转入'
        ];
    }

    public function saveOne($data)
    {
        foreach ($data as $key=>$value){
            if (in_array($key,$this->insertFields)){
                $this->$key = $value;
            }
        }
        if ($this->save()) return $this->id;return false;
    }

}
