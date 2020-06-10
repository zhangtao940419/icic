<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 10:54
 */

namespace App\Server\InsideTrade\Dao;

use App\Model\CoinType;

class CoinTypeDao
{
    protected $coinType;

    public function __construct(CoinType $coinType)
    {
        $this->coinType = $coinType;
    }

    public function getAllCoinType(){
        return $this->coinType->getAllCoinType();
    }

}
