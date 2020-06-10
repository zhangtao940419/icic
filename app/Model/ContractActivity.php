<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/20
 * Time: 11:27
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ContractActivity extends Model
{

    protected $table = 'contract_activity';

    protected $primaryKey = 'id';

    protected $appends = ['jg_status_des'];

    protected $guarded = [];

    public function getJgStatusDesAttribute()
    {
        return ['--','多','平','空'][$this->jg_status];

    }


    public function insertOne($no,$coinId,$lastPrice,$nowPrice,$jgTime)
    {

        $this->activity_no = $no;
        $this->coin_id = $coinId;
        $this->last_price = $lastPrice;
        $this->now_price = $nowPrice;
        $this->jg_time = $jgTime;

        return $this->save();
    }

    public function getNewest()
    {

        return $this->with(['coin'=>function($q){
            $q->select(['coin_id','coin_name']);
        }])->latest('id')->first();


    }



    public function coin()
    {
        return $this->belongsTo(CoinType::class,'coin_id','coin_id');
    }


}