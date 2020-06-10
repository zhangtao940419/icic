<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/7
 * Time: 15:28
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EthToken extends Model
{

    /*表名称*/
    protected $table = 'eth_token_list';

    protected $primaryKey = 'token_id';

    protected $fillable = ['token_symbol', 'token_total_supply', 'token_contract_address', 'token_contract_abi', 'token_decimal', 'coin_id'];


    /*获取单条记录*/
    public function getRecordByCoinId(int $coinId)
    {
        return $this->where(['is_usable'=>1,'coin_id'=>$coinId])->first();
    }

    //关联货币表
    public function coin()
    {
        return $this->hasOne('App\Model\CoinType', 'coin_id', 'coin_id')
            ->where('is_usable', 1)
            ->select(['coin_id', 'coin_name']);
    }

}