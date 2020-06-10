<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OutsideTradeOrder extends Model
{
    /*表名称*/
    protected $table = 'outside_trade_order';

    protected $primaryKey = 'order_id';

    private $select_fields =['order_id','order_number','trade_order','user_id','trade_user_id','order_type',
    'currency_id','coin_id','trade_coin_num','trade_total_money','trade_status'];

    private $insert_fields=['order_id','order_number','trade_order','user_id','trade_user_id','order_type',
        'currency_id','coin_id','trade_coin_num','trade_total_money','trade_status'];

    private $slect_order_fileds=['order_id','trade_order','order_number','order_type','user_id', 'trade_user_id','coin_id','trade_coin_num','trade_total_money','trade_status','created_at'];

    protected $guarded = [];

    /*
     * 订单入库
     * @param $outSideRequestParam
     *
     * return 1|0
     */
    public function saveOrder($outSideRequestParam){

        foreach ($outSideRequestParam as $key=>$value){

            if(in_array($key,$this->insert_fields)){
                $this->$key=$value;
            }
        }
        if(!$this->save()) return 0;
        return 1;
    }

    public function getOrderById($id,$column = ['*'],$lock = 0)
    {
        if ($lock)
            return $this->lockForUpdate()->find($id,$column);
        return $this->find($id,$column);
    }


    /* 根据订单号码获取订单状态
     *  @param
     *  trade_order:场外市场的挂单订单号
     *  order_number：下单的订单号码
     * retun integer
     */
    public function getOneOrderStatus($trade_order,$order_number){

      return $this->where(['trade_order'=>$trade_order,'order_number'=>$order_number,'is_usable'=>1])->value('trade_statu');

    }

     /* 根据订单号码获取订单状态
      * @param
      *  trade_order:场外市场挂单的订单号
      *  order_number：用户已下单的订单号码
      * retun array()
      */
    public function getOneOrder($trade_order,$order_number){

        return $this->where(['trade_order'=>$trade_order,'order_number'=>$order_number,'is_usable'=>1])->select($this->select_fields)->get()->toArray()[0];


    }


     /* 变更订单状态，自动撤销
      *
      *  trade_order:挂单的订单号
      *  order_number：卖单订单号码
      *  retun integer
      */
    public function userCancelOrder($trade_order,$order_number){

        return  $this->where(['trade_order'=>$trade_order,'order_number'=>$order_number,'is_usable'=>1])->update(['trade_statu'=>0]);

    }


    /* 变更订单状态，自动撤销
     *
     *  trade_order:挂单的订单号
     *  order_number：卖单订单号码
     *  retun integer
     */
    public function autoCancelOrder($trade_order,$order_number){

         return  $this->where(['trade_order'=>$trade_order,'order_number'=>$order_number,'is_usable'=>1])->update(['trade_statu'=>-1]);

    }


    /*
     * 用户确认付款
     *  param:
     *  trade_order:场外交易订单号，通过该次订单查找相关的信息
     *  order_number:已下单的订单号码
     */
     public function confirmPay($confirmParam){

         return  $this->where(['trade_order'=>$confirmParam['trade_order'],'order_number'=>$confirmParam['order_number'],'is_usable'=>1])->update(['trade_statu'=>2]);

     }



    /*
     *  用户确认发货
     *  param:
     *  trade_order:场外交易订单号，通过该次订单查找相关的信息
     *  order_number:已下单的订单号码
     */
    public function autoConfirmSend($confirmParam){

        return  $this->where(['trade_order'=>$confirmParam['trade_order'],'order_number'=>$confirmParam['order_number'],'is_usable'=>1])->update(['trade_statu'=>3]);

    }


      /* 根据订单号码获取订单交易的货币数额
       * @param
       *  trade_order: 场外市场挂单的订单号
       *  order_number：用户已下单的订单号码
       * retun numeric
       */
    public function getOneOrderTradeNum($trade_order,$order_number){

        return $this->where(['trade_order'=>$trade_order,'order_number'=>$order_number,'is_usable'=>1])->value('trade_coin_num');


    }

     /* 根据订单号码获取是否有基于该订单的交易状态中的订单
      * @param
      *  trade_order: 场外市场挂单的订单号
      *  user_id: 场外市场挂单的订单号
      * retun numeric
      */
    public function getOrderTrade($trade_order,$user_id){

        return $this->where(['trade_order'=>$trade_order,'trade_user_id'=>$user_id,'is_usable'=>1])->whereIn('trade_statu', [1,2])->count();

    }

    /* 获取挂单出售的币种数量
     *   @param
     *   $user_id：挂单发起者
     *   $trade_statu :订单状态
     */

    public function getUserTradeOrder(int $user_id,int $trade_statu):array {

        if($trade_statu==0)
        return $this->with('getCoin','getUserInfo','getOutSideOrder','getTradeUserInfo')
            ->where(['user_id'=>$user_id,'is_usable'=>1])->select($this->slect_order_fileds)
            ->whereBetween('trade_statu',[-1,0])
            ->union($this->where(['trade_user_id'=>$user_id,'is_usable'=>1])->select($this->slect_order_fileds)->whereBetween('trade_statu',[-1,0]))
            ->latest()
            ->get()->toArray();
        if($trade_statu==1)
            return $this->with('getCoin','getUserInfo','getOutSideOrder','getTradeUserInfo')
                ->where(['user_id'=>$user_id,'is_usable'=>1])->select($this->slect_order_fileds)
                ->whereBetween('trade_statu',[1,2])
                ->union($this->where(['trade_user_id'=>$user_id,'is_usable'=>1])->select($this->slect_order_fileds)->whereBetween('trade_statu',[1,2]))
                ->latest()
                ->get()->toArray();
        if($trade_statu==3)
            return $this->with('getCoin','getUserInfo','getOutSideOrder','getTradeUserInfo')
                ->where(['user_id'=>$user_id,'is_usable'=>1])->select($this->slect_order_fileds)
                ->whereBetween('trade_statu',[3,4])
                ->union($this->where(['trade_user_id'=>$user_id,'is_usable'=>1])->select($this->slect_order_fileds)->whereBetween('trade_statu',[3,4]))
                ->latest()
                ->get()->toArray();
    }

    /*
     * 关联Coin表
     */
    public function getCoin(){

        return $this->hasOne('App\Model\CoinType','coin_id','coin_id')
            ->where('is_usable',1)
            ->select(['coin_id','coin_name']);
    }

    /*
     * 发起人关联user表
     */
    public function getUserInfo(){

        return $this->belongsTo('App\Model\User','user_id','user_id')
            ->where('is_usable',1);
    }

    /*
     * 发起人关联user表
     */
    public function getTradeUserInfo(){

        return $this->hasOne('App\Model\User','user_id','trade_user_id')
            ->where('is_usable',1)
            ->select(['user_id','user_name','user_auth_level','user_headimg']);

    }


    /*
     * 交易人关联user表
     */
    public function getOrderInfo(){

        return $this->belongsTo('App\Model\User','trade_user_id','user_id')
            ->where('is_usable',1);
    }
     /*
      * 获取场外订单
      */
    public function getOutSideOrder(){

        return $this->belongsTo('App\Model\OutsideTrade','trade_order','trade_order')
            ->where('is_usable',1)
            ->select(['trade_id','trade_order','trade_type','trade_des','get_money_type']);
    }

    public function getOrderStatus()
    {
        return [
           -2 => '强制被撤单',
           -1 => '自动撤单',
            0 => '主动撤单',
            1 => '订单确认中',
            2 => '买家已付款,等待卖家确认',
            3 => '卖家已确认,待完成',
            4 => '已完成'
        ];
    }

    /* 获取某个所有的发布的广告
     *
     *
     */
    public function getSomeUserTrade($userInparam){



        return $this->with('getCoin')->where(['user_id'=>$userInparam['user_id'],'trade_user_id'=>$userInparam['trade_user_id']])
            ->select($this->slect_order_fileds)
            ->get()->toArray();

    }




    //获取订单状态
    public function getOrder($order_number)
    {

        return $this->where(['order_number' => $order_number])->value('trade_statu');
    }

    /*获取交易次数*/
    public function getTradeNum(int $userId1,int $userId2)
    {
        return $this->where(['user_id'=>$userId1,'trade_user_id'=>$userId2])->count();

    }


}

