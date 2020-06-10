<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 11:04
 */

namespace App\Server\InsideTrade\Dao;


use App\Model\CenterWallet;

class CenterWalletDao
{
    protected $centerWallet;

    public function __construct(CenterWallet $centerWallet)
    {
        $this->centerWallet = $centerWallet;
    }

    public function getCenterWallet(){
        return $this->centerWallet;
    }
}
