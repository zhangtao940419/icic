<?php
namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class UserFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    //用户名筛选
    public function userName($username)
    {
        $this->where('user_name','like', '%'.$username.'%')->orWhere('user_phone', 'like','%'.$username.'%');
    }

    //商家,特殊用户筛选
    public function status($status)
    {
        switch ($status) {
            case 1:
                $where = ['is_special_user' => 1];
                break;
            case 2:
                $where = ['is_business' => 1];
                break;
            case 3:
                $where = ['is_special_user' => 1, 'is_business' => 1];
                break;
            case 4:
                $where = ['is_special_user' => 0];
                break;
            case 5:
                $where = ['is_inside_user' => 1];
                break;
            case 6:
                $where = ['is_sto_special_user' => 1];
                break;
            case 7:
                $where = ['is_frozen' => 0];
                break;
            default:
        }

        $this->where($where);
    }

    //认证用户筛选
    public function userAuthLevel($user_auth_level)
    {
        $this->where(['user_auth_level' => $user_auth_level]);
    }

    //钱包地址搜索
    public function walletAddress($wallet_address)
    {
        $this->whereHas('userWallet',function ($query) use($wallet_address) {
            $query->where('wallet_address', $wallet_address);
        });
    }

    //开始时间
    public function beginTime($begin_time)
    {
        $this->where('created_at', '>=', $begin_time);
    }

    //结束时间
    public function endTime($end_time)
    {
        $this->where('created_at', '<=', $end_time);
    }



}
