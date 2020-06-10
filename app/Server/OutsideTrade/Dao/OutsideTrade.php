<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 16:34
 */

namespace App\Server\OutsideTrade\Dao;


use App\Model\Admin\Currency;
use App\Model\CoinType;
use App\Model\User;
use App\Model\UserDatum;
use App\Model\WorldArea;
use App\Model\WorldCurrency;
use Illuminate\Support\Facades\DB;

class OutsideTrade extends \App\Model\OutsideTrade
{



    private $tradeFields=['trade_id','trade_order','user_id','coin_id',
        'trade_des', 'trade_number','trade_left_number', 'trade_type', 'location_id', 'currency_id', 'get_money_type', 'trade_price_type', 'trade_premium_rate',
        'trade_with_trust', 'trade_with_confirm', 'trade_is_visual', 'trade_visual_time', 'trade_price','trade_ideality_price',
        'trade_min_limit_price', 'trade_max_limit_price', 'trade_limit_time', 'trade_status'];

    private $select_fileds=['trade_id','user_id','coin_id',
        'trade_des', 'trade_number','trade_left_number', 'trade_type', 'location_id', 'currency_id', 'get_money_type', 'trade_price_type', 'trade_premium_rate',
        'trade_with_trust', 'trade_with_confirm', 'trade_is_visual', 'trade_visual_time', 'trade_price','trade_ideality_price',
        'trade_min_limit_price', 'trade_max_limit_price','created_at'];


    public function saveTrade($data)
    {

        foreach ($data as $key=>$value){
            if(in_array($key,$this->tradeFields)){
                $this->$key=$value;
            }
        }
        if($this->save())   return 1;
        return 0;

    }


    public function getRecords($where,$column = ['*'])
    {

        return $this->where($where)->latest()->get($column);

    }


    public function getOneRecord($where,$column = ['*'],$lock = 0)
    {
        if (!$lock)
        return $this->where($where)->first($column);
        return $this->where($where)->lockForUpdate()->first($column);
    }

    public function getTrade($tradeId,$column = ['*'],$lock = 0)
    {
        if (!$lock)
            return $this->with(['coin','currency'])->find($tradeId,$column);
        return $this->lockForUpdate()->find($tradeId,$column);
    }

    public function getOutsideTrade($where)
    {
        return $this->with(['user','datum','coin','currency'])->where(['trade_status'=>1,'trade_type'=>$where['trade_type'],'location_id'=>$where['location_id'],'coin_id'=>$where['coin_id']])->where('trade_left_number','>',0)->latest()->get($this->select_fileds)->toArray();
    }

    public function getUserTrades($where)
    {//dd($where);
        return $this->with(['coin','currency'])->where($where)->where('trade_left_number','>',0)->latest()->get($this->select_fileds)->toArray();
    }

    public function tradeManage($userId,$status)
    {
        switch ($status){
            case 1:
                $status = [1];
                break;
            case 2:
                $status = [2];
                break;
            case 3:
                $status = [-1,0,2];
                break;
        }

        return $this->with(['coin','currency'])->where('user_id',$userId)->whereIn('trade_status',$status)->latest()->get();

    }


    public function coin()
    {
        return $this->hasOne(CoinType::class,'coin_id','coin_id')->select(['coin_id','coin_name']);
    }


    public function location()
    {
        return $this->hasOne(WorldArea::class,'country_id','location_id');
    }

    public function currency()
    {
        return $this->hasOne(WorldCurrency::class,'currency_id','currency_id')->select(['currency_id','currency_code']);
    }

    public function user()
    {
        return $this->hasOne(User::class,'user_id','user_id')->select(['user_id','user_name','user_headimg']);
    }

    public function datum()
    {
        return $this->hasOne(UserDatum::class,'user_id','user_id')->select(['user_id','trade_total_num','trade_trust_num','trade_favourable_comment']);
    }

    public function getGetMoneyTypeAttribute($value)
    {
        return explode(',',$value);
    }

    public function getTradeLimitTimeAttribute($value)
    {
        return json_decode($value,true);
    }

}