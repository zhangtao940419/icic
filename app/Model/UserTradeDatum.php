<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/13
 * Time: 14:30
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class UserTradeDatum extends Model
{

    /*表名称*/
    protected $table = 'outside_user_trade_datum';

    protected $primaryKey = 'datum_id';
    /**
     * 应被转换为日期的属性。
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'trade_total_num',
        'trade_trust_num',
        'user_id',
        'trade_favourable_comment',
        'is_usable',
        'created_at',
        'updated_at',
    ];


    /*创建新纪录*/
    public function saveOneRecord(int $userId)
    {
        if ($this->where('user_id',$userId)->first()) return 0;
        $this->user_id = $userId;
        return $this->save();

    }

    /*添加信任*/
    public function addTrust(int $userId)
    {
        return $this->where(['is_usable'=>1,'user_id'=>$userId])->increment('trade_trust_num',1);
    }

    /*移除信任*/
    public function removeTrust(int $userId)
    {
        return $this->where(['is_usable'=>1,'user_id'=>$userId])->decrement('trade_trust_num',1);
    }

    /*获取一条记录*/
    public function getOneRecord(int $userId)
    {
        return $this->where(['is_usable'=>1,'user_id'=>$userId])->first()->toArray();
    }

    public function getUserTradeMsg(int $userId)
    {
        return $this->with('userMsg')->where(['user_id'=>$userId,'is_usable'=>1])->first()->toArray();
    }

    public function userMsg()
    {
        return $this->hasOne('App\Model\User','user_id','user_id')->select('user_id','user_name','user_headimg','user_auth_level');
    }




}