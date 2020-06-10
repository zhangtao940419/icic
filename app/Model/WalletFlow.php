<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/12
 * Time: 11:37
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class WalletFlow extends Model
{

    protected $table = 'wallet_flow';

    protected $primaryKey = 'id';

    protected $appends = ['coin_name','wallet_type_name'];

//`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//`user_id` int(11) unsigned NOT NULL DEFAULT '0',
//`wallet_id` int(11) unsigned NOT NULL DEFAULT '0',
//`coin_id` tinyint(4) unsigned NOT NULL DEFAULT '0',
//`flow_number` decimal(25,12) unsigned NOT NULL,
//`flow_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '1提币2转入3:c2c买4:c2c卖5商家买6商家卖7场内交易8理财9理财提取10资金划转11sto买12sto提取',
//`symbol` tinyint(2) unsigned NOT NULL COMMENT '1+2-',
//`title` varchar(20) NOT NULL COMMENT '类型',
//`des` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态1已完成',
//`fee` decimal(20,12) unsigned NOT NULL DEFAULT '0.000000000000' COMMENT '手续费',
//`wallet_type` tinyint(2) unsigned NOT NULL COMMENT '钱包类型1场内2可提',

    const des_finish = 1;

    public function getCoinNameAttribute(){
        return CoinType::query()->where('coin_id',$this->coin_id)->value('coin_name');
    }

    public function getWalletTypeNameAttribute(){
        $arr = [
            1=>'场内',
            2=>'可提',
        ];

        return $arr[$this->wallet_type];
    }

    public static function des_map()
    {
        return [
          self::des_finish => '已完成'
        ];
    }


    public function insertOne($userid,$walletid,$coinid,$flow_amount,int $flow_type,int $symbol,$title,$walletType,$des = 1,$fee = 0,$s_user_id = 0)
    {

        $this->user_id = $userid;
        $this->wallet_id = $walletid;
        $this->coin_id = $coinid;
        $this->flow_number = $flow_amount;
        $this->flow_type = $flow_type;
        $this->symbol = $symbol;
        $this->title = $title;
        $this->wallet_type = $walletType;
        $this->des = $des;
        $this->fee = $fee;
        $this->sort = time();
        $this->s_user_id = $s_user_id;

        if ($walletType == 1){
            $this->balance = WalletDetail::find($walletid,['wallet_usable_balance'])->wallet_usable_balance;
        }else{
            $this->balance = WalletDetail::find($walletid,['wallet_withdraw_balance'])->wallet_withdraw_balance;
        }


        return $this->save();

    }


    public function getDesAttribute($v)
    {
        return self::des_map()[$v];
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
