<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/23
 * Time: 12:01
 */

namespace App\Model\Admin;


use App\Model\CoinType;
use App\Model\User;
use Illuminate\Database\Eloquent\Model;

class AdminWalletFlow extends Model
{

    protected $table = 'admin_wallet_flow';

    protected $primaryKey = 'id';



    protected $guarded = [];

    protected $column = ['user_id','coin_id','amount','wallet_type','des','admin_user_id','type'];



    function saveOneFlow($data)
    {
        foreach ($data as $key=>$value){
            if (in_array($key,$this->column))
                $this->$key = $value;
        }
//        dd($this);
        return $this->save();


    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }


    public function admin_user()
    {
        return $this->hasOne('App\Model\Admin\adminUser','id','admin_user_id');
    }

    public function coin()
    {
        return $this->hasOne(CoinType::class,'coin_id','coin_id');
    }


    public function type()
    {
        return [
            '1'=>'增加',
            '2'=>'减少'
        ];
    }

    public function wallet_type()
    {
        return [
            '1'=>'场内',
            '2'=>'可提',
            '3'=>'矿池'
        ];
    }

}