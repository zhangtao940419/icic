<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/7
 * Time: 10:24
 */

namespace App\Server\Interfaces;

interface CoinServerInterface{

    public function getBalance($account);

    public function listAccounts();

    public function getTransaction($transactionId);

    public function getWalletInfo();

    public function newAccount($password);

//    public function sendFrom();

}