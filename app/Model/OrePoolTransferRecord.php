<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/9/18
 * Time: 14:56
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class OrePoolTransferRecord extends Model
{

    protected $table = 'ore_pool_transfer_record';


    protected $primaryKey = 'id';

    protected $guarded = [];


    public $appends = ['flow_status','flow_type','wallet_type_name','coin_name','flow_number'];

    public function insertOne($walletid,$userid,$coinid,$amount,$type,$status = 1,$s_user_id = 0)
    {
        $this->wallet_id = $walletid;
        $this->user_id = $userid;
        $this->coin_id = $coinid;
        $this->amount = $amount;
        $this->type = $type;
        $this->status = $status;
        $this->s_user_id = $s_user_id;
        return $this->save();

    }


    //
    public function getWalletFlow($walletid)
    {
        return $this->where(['wallet_id' => $walletid])->latest()->get();
    }


    public function getFlowStatusAttribute()
    {
        return [1=>'已完成'][$this->status];
    }

    public function getFlowTypeAttribute()
    {
        return [1=>'互链转入',2=>'场内转入',3=>'可提转入',4=>'释放(场内交易)',5=>'下级奖励',6=>'释放(自动释放)',7=>'sto买入',8=>'超时自动转入'
            ,9=>'后台增加',10=>'后台减少'
        ][$this->type];
    }

    public function getWalletTypeNameAttribute()
    {
        return '矿池';
    }

    public function getFlowNumberAttribute()
    {
        return $this->amount;
    }

    public function getCoinNameAttribute(){
        return CoinType::query()->where('coin_id',$this->coin_id)->value('coin_name');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }
    public function s_user()
    {
        return $this->belongsTo(User::class,'s_user_id','user_id');
    }
    public function coin()
    {
        return $this->belongsTo(CoinType::class,'coin_id','coin_id');
    }


}
