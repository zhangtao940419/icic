<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 17:14
 */

namespace App\Server\OutsideTrade\Dao;


use App\Model\OutsideWalletDetail;

class OutsideWalletDao extends OutsideWalletDetail
{


    protected $fields =['wallet_id','coin_id','wallet_account','wallet_address','user_id','wallet_usable_balance','wallet_freeze_balance','wallet_withdraw_balance','wallet_into_balance_amount','wallet_divert_amount','parent_id'];

    public function getOneRecord($userId,$coinId,$lock = 0)
    {
        if (!$lock)
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->first();
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->lockForUpdate()->first();
    }

    public function getWalletById($walletId,$colum = ['*'],$lock = 0)
    {
        if (!$lock)
            return $this->find($walletId,$colum);
        return $this->lockForUpdate()->find($walletId,$colum);
    }


    public function getUsableBalance($userId,$coinId)
    {
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->value('wallet_usable_balance');
    }

    public function getWithdrawBalance($userId,$coinId)
    {
        return $this->where(['user_id'=>$userId,'coin_id'=>$coinId])->value('wallet_withdraw_balance');
    }

    public function addUsableBalance($walletId,$amount)
    {
        return $this->where('wallet_id',$walletId)->increment('wallet_usable_balance',$amount);
    }
    public function subUsableBalance($walletId,$amount)
    {
        return $this->where('wallet_id',$walletId)->decrement('wallet_usable_balance',$amount);
    }
    public function addFreezeBalance($walletId,$amount)
    {
        return $this->where('wallet_id',$walletId)->increment('wallet_freeze_balance',$amount);
    }
    public function subFreezeBalance($walletId,$amount)
    {
        return $this->where('wallet_id',$walletId)->decrement('wallet_freeze_balance',$amount);
    }
    public function addBlockIntoBalance($walletId,$amount)
    {
        return $this->where('wallet_id',$walletId)->increment('wallet_into_balance_amount',$amount);
    }
    public function subBlockIntoBalance($walletId,$amount)
    {
        return $this->where('wallet_id',$walletId)->decrement('wallet_into_balance_amount',$amount);
    }
    public function addWithdrawBalance($walletId,$amount)
    {
        return $this->where('wallet_id',$walletId)->increment('wallet_withdraw_balance',$amount);
    }
    public function subWithdrawBalance($walletId,$amount)
    {
        return $this->where('wallet_id',$walletId)->decrement('wallet_withdraw_balance',$amount);
    }


    /*获取用户的钱包列表*/
    public function walletIndex(int $userId)
    {
        $records = $this->with(['coinName'=>function($q){
            $q->with('coinIcon');
        }
            ,'coinFees'])->where(['is_usable'=>1,'user_id'=>$userId])->select($this->fields)->get()->toArray();
        foreach ($records as $key=>$record){
            if ($record['parent_id']){
                $records[$key]['wallet_address'] = $this->getRecordByParentId($record['parent_id'])['wallet_address'];
            }
        }
        $order = ['USDT','BTC','ETH','ICIC'];//排序
//        $recordsN = $records;
        foreach ($records as $key=>$value){

            foreach ($records as $k=>$v){
                if (isset($order[$key]) && $v['coin_name']['coin_name'] == $order[$key]){
                    if ($key != $k){
                        $records[$key] = $v;
                        $records[$k] = $value;
                    }
//                    dd($key);
                }

            }
            //dd('222');
        }
//        dd($records);
        return $records;
    }

    /*根据主键id和userid获取记录,限制两个字段是为了确保权限正确*/
    public function getRecordById(int $walletId,int $userId):array
    {
        $result = $this->select($this->fields)->where(['wallet_id'=>$walletId,'user_id'=>$userId,'is_usable'=>1])->with('coinName','coinFees')->first();
        if (! $result) return [];
        $result = $result->toArray();
        if ($result['parent_id']) {
            $result['wallet_address'] = $this->getRecordByParentId($result['parent_id'])['wallet_address'];
            return $result;
        }
        return $result;
    }


    /*充值信息*/
    public function getRechargeMsg(int $userId,int $walletId)
    {
        $result = $this->with('coinNotice','coinFees','coinName')->select('wallet_address','coin_id','parent_id')->where(['wallet_id'=>$walletId,'user_id'=>$userId])->first();
        if (! $result) return [];
        $result = $result->toArray();
        if ($result['parent_id']) {
            $result['wallet_address'] = $this->getRecordByParentId($result['parent_id'])['wallet_address'];
            return $result;
        }
        return $result;
    }

    /*提币信息*/
    public function getWithdrawMsg(int $userId,int $walletId)
    {
        $result = $this->with('coinNotice','coinFees','coinName')->select('wallet_usable_balance','wallet_withdraw_balance','coin_id','parent_id')->where(['wallet_id'=>$walletId,'user_id'=>$userId])->first();
        if (! $result) return [];
        $result = $result->toArray();
        if ($result['parent_id']) {
            $result['wallet_address'] = $this->getRecordByParentId($result['parent_id'])['wallet_address'];
            return $result;
        }
        return $result;
    }



    /*根据parentid获取父类信息*/
    public function getRecordByParentId(int $parentId)
    {
        if ($result = $this->find($parentId,['wallet_address'])) return $result->toArray();return ['wallet_address'=>''];
    }


    public function getUserIdByAddress($address)
    {
        return $this->where('wallet_address',$address)->value('user_id');
    }


    /*模型关联:coinname*/
    public function coinName()
    {
        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')->select(['coin_id','coin_name']);
    }

    /*模型关联:注意事项*/
    public function coinNotice()
    {
        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')->select(['coin_id','coin_name','coin_withdraw_message','coin_recharge_message']);
    }


    //关联用户表
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'user_id');
    }

    //关联货币表
    public function coin()
    {
        return $this->hasOne('App\Model\CoinType', 'coin_id', 'coin_id');
    }

    /*关联费用表*/
    public function coinFees()
    {
        return $this->hasOne('App\Model\CoinFees','coin_id','coin_id');
    }

}