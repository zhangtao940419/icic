<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 11:02
 */

namespace App\Server\InsideTrade\Dao;

use App\Model\User;

class UserDao
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function getOneRecord($where){
        return $this->user->where($where)->first();
    }
}
