<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/7
 * Time: 14:47
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\C2CTrade;
use App\Model\CoinTotalDayTongji;
use App\Model\CoinTradeOrder;
use App\Model\CoinType;
use App\Model\InsideTradeBuy;
use App\Model\InsideTradeSell;
use App\Model\kgModel\UserWallet;
use App\Model\User;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use App\Traits\RedisTool;
use App\Traits\Tools;
use Illuminate\Http\Request;

class TongJiController extends Controller
{

    use Tools,RedisTool;

    protected $relation_hl_addresses = [];


    public function __construct()
    {


    }

    //
    public function new_tongji(Request $request,User $user,WalletDetail $walletDetail,C2CTrade $c2CTrade,CoinType $coinType,CoinTotalDayTongji $coinTotalDayTongji)
    {
        $hid = 1;

        $snum = $this->stringGet('tongji_s_num');
        if ($request->s_num){
            $this->stringSet('tongji_s_num',$request->s_num);
            $snum = $request->s_num;
        }

        if ($snum === null){
            $hid = 0;
            return view('admin.wallet.new_tongji',compact('hid'));
        }


        $u1 = $user->where('user_phone','13078670552')->first();
        $u2 = $user->where('user_phone','13929771840')->first();
        $u3 = $user->where('user_phone','18211558731')->first();
//        $u3 = $user->where('user_phone','15574832499')->first();
        $wq1 = $walletDetail->where(['user_id'=>$u1->user_id,'coin_id'=>12])->first();
        $wq2 = $walletDetail->where(['user_id'=>$u2->user_id,'coin_id'=>12])->first();
        $wq3 = $walletDetail->where(['user_id'=>$u3->user_id,'coin_id'=>12])->first();
        $wi1 = $walletDetail->where(['user_id'=>$u1->user_id,'coin_id'=>8])->first();//dd($wi1);
        $wi2 = $walletDetail->where(['user_id'=>$u2->user_id,'coin_id'=>8])->first();
        $wi3 = $walletDetail->where(['user_id'=>$u3->user_id,'coin_id'=>8])->first();

        $team = $this->redisHgetAll('INSIDE_TEAM_12_8');
        $tui = $wi1->wallet_usable_balance + $wi2->wallet_usable_balance + $wi3->wallet_usable_balance + $wi1->wallet_withdraw_balance + $wi2->wallet_withdraw_balance + $wi3->wallet_withdraw_balance + $wi1->wallet_freeze_balance + $wi2->wallet_freeze_balance + $wi3->wallet_freeze_balance;
        $tuq = $wq1->wallet_usable_balance + $wq2->wallet_usable_balance + $wq3->wallet_usable_balance + $wq1->wallet_withdraw_balance + $wq2->wallet_withdraw_balance + $wq3->wallet_withdraw_balance + $wq1->wallet_freeze_balance + $wq2->wallet_freeze_balance + $wq3->wallet_freeze_balance;

        $c2c_t_b = $c2CTrade->where(['trade_type'=>1,'trade_status'=>3])->sum('trade_number');//用户所有买入qc之和
        $c2c_t_s = $c2CTrade->where(['trade_type'=>2,'trade_status'=>3])->sum('trade_number');
//        dd(date('Y-m-d',time()-24*3600) . ' 21:00:00');
        $c2c_d_t_b = $c2CTrade->where(['trade_type'=>1,'trade_status'=>3])->whereBetween('created_at',[date('Y-m-d',time()-24*3600) . ' 21:00:00',datetime()])->sum('trade_number');//用户今日所有买入qc之和
        $c2c_d_t_s = $c2CTrade->where(['trade_type'=>2,'trade_status'=>3])->whereBetween('created_at',[date('Y-m-d',time()-24*3600) . ' 21:00:00',datetime()])->sum('trade_number');

        //流通icic总和
//        $user_total_icic = $coinType->totalAmount(8);
        $user_total_icic = $coinType->userTotalAmount(8);
//        $user_total_qc = $coinType->totalAmount(12);
        $user_total_qc = $coinType->userTotalAmount(12);

        $last_day_total_icic = $coinTotalDayTongji->getLastTotalNum(8);//dd($last_day_total_icic);

        //用户qc
        $totalInside = $coinType->totalInside(12);
        $totalWithdraw = $coinType->totalWithdraw(12);
        $bTotalWithdraw = $coinType->bTotalWithdraw(12);

        return view('admin.wallet.new_tongji',compact('hid','snum','u1','u2','u3','wq1','wq2','wq3','wi1','wi2','wi3','team','tui','tuq','c2c_t_b','c2c_t_s','c2c_d_t_b','c2c_d_t_s','user_total_icic','user_total_qc','last_day_total_icic','totalInside','totalWithdraw','bTotalWithdraw'));
    }


    //会员走向统计表
    public function new_tongji1(Request $request,User $user,WalletDetail $walletDetail,C2CTrade $c2CTrade,CoinType $coinType,CoinTotalDayTongji $coinTotalDayTongji)
    {
        $hid = 1;

        if ($request->user_phone === null){
            $hid =0;
            return view('admin.wallet.new_tongji1',compact('hid'));
        }

        $user = $user->with(['userIdentify'])->where('user_phone',$request->user_phone)->first();


        $userTotalBuy = $c2CTrade->getUserTotalBuy1($user->user_id);
        $userTotalSell = $c2CTrade->getUserTotalSell1($user->user_id);

        $qcWallet = $walletDetail->getOneWallet($user->user_id,12);
        $icicWallet = $walletDetail->getOneWallet($user->user_id,8);

        $team = $this->redisHgetAll('INSIDE_TEAM_12_8');

        return view('admin.wallet.new_tongji1',compact('hid','user','userTotalBuy','userTotalSell','qcWallet','icicWallet','team'));
    }

    //会员走向统计表
    public function new_tongji2(Request $request,User $user,WalletDetail $walletDetail,C2CTrade $c2CTrade,CoinType $coinType,CoinTotalDayTongji $coinTotalDayTongji,WalletFlow $walletFlow,CoinTradeOrder $coinTradeOrder,InsideTradeBuy $insideTradeBuy,InsideTradeSell $insideTradeSell)
    {
        $hid = 1;

        if ($request->user_phone === null){
            $hid =0;
            return view('admin.wallet.new_tongji1',compact('hid'));
        }

        $user = $user->with(['userIdentify'])->where('user_phone',$request->user_phone)->first();

        $qc_cn_ly_1 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>1])->whereIn('flow_type',[3])->sum('flow_number');
        $qc_cn_ly_2 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>1])->whereIn('flow_type',[18])->where(['symbol'=>1])->sum('flow_number');
        $qc_cn_ly_3 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>1])->whereIn('flow_type',[10])->where(['symbol'=>1])->sum('flow_number');

        $qc_cn_qx_1 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>1])->whereIn('flow_type',[7])->where(['symbol'=>2])->sum('flow_number');
        $qc_cn_qx_2 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>1])->whereIn('flow_type',[18])->where(['symbol'=>2])->sum('flow_number');
        $qc_cn_qx_3 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>1])->whereIn('flow_type',[11])->where(['symbol'=>2])->sum('flow_number');

        $qc_kt_ly_1 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>2])->whereIn('flow_type',[7])->where(['symbol'=>1])->sum('flow_number');
        $qc_kt_ly_2 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>2])->whereIn('flow_type',[18])->where(['symbol'=>1])->sum('flow_number');
        $qc_kt_ly_3 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>2])->whereIn('flow_type',[5])->where(['symbol'=>1])->sum('flow_number');

//        $qc_kt_qx_1 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>2])->whereIn('flow_type',[4])->where(['symbol'=>2])->sum('flow_number');
        $qc_kt_qx_1 = $c2CTrade->where(['user_id'=>$user->user_id,'trade_type'=>2,'trade_status'=>3])->sum('trade_number');
        $qc_kt_qx_2 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>2])->whereIn('flow_type',[18])->where(['symbol'=>2])->sum('flow_number');
        $qc_kt_qx_3 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>2])->whereIn('flow_type',[6])->where(['symbol'=>2])->sum('flow_number');
        $qc_kt_qx_4 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>12,'wallet_type'=>2])->whereIn('flow_type',[10])->where(['symbol'=>2])->sum('flow_number');



        $icic_cn_ly_1 = $coinTradeOrder->where(['user_id'=>$user->user_id,'coin_id'=>8])->where(['order_type'=>2])->whereIn('transfer_type',[2])->sum('order_trade_money');
        $icic_cn_ly_2 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>1])->whereIn('flow_type',[18])->where(['symbol'=>1])->sum('flow_number');
        $icic_cn_ly_3 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>1])->whereIn('flow_type',[10])->where(['symbol'=>1])->sum('flow_number');
        $icic_cn_ly_4 = $coinTradeOrder->where(['user_id'=>$user->user_id,'coin_id'=>8])->where(['order_type'=>2])->whereIn('transfer_type',[1])->sum('order_trade_money');
        $icic_cn_ly_5 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>1])->whereIn('flow_type',[15,23])->where(['symbol'=>1])->sum('flow_number');
        $icic_cn_ly_6 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>1])->whereIn('flow_type',[13])->where(['symbol'=>1])->sum('flow_number');

        $icic_cn_qx_1 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>1])->whereIn('flow_type',[7])->where(['symbol'=>2])->sum('flow_number');
        $icic_cn_qx_2 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>1])->whereIn('flow_type',[11])->where(['symbol'=>2])->sum('flow_number');
        $icic_cn_qx_3 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>1])->whereIn('flow_type',[18])->where(['symbol'=>2])->sum('flow_number');

        $icic_kt_ly_1 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>2])->whereIn('flow_type',[7])->where(['symbol'=>1])->sum('flow_number');
        $icic_kt_ly_2 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>2])->whereIn('flow_type',[18])->where(['symbol'=>1])->sum('flow_number');
//        $icic_kt_ly_3 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>2])->whereIn('flow_type',[5])->where(['symbol'=>1])->sum('flow_number');

        $icic_kt_qx_1 = $coinTradeOrder->where(['user_id'=>$user->user_id,'coin_id'=>8])->where(['order_type'=>1])->whereIn('transfer_type',[1])->sum('order_trade_money');
        $icic_kt_qx_2 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>2])->whereIn('flow_type',[18])->where(['symbol'=>2])->sum('flow_number');
        $icic_kt_qx_3 = $coinTradeOrder->where(['user_id'=>$user->user_id,'coin_id'=>8])->where(['order_type'=>1])->whereIn('transfer_type',[2])->sum('order_trade_money');
        $icic_kt_qx_4 = $walletFlow->where(['user_id'=>$user->user_id,'coin_id'=>8,'wallet_type'=>2])->whereIn('flow_type',[10])->where(['symbol'=>2])->sum('flow_number');

        $qc_wallet = $walletDetail->where(['user_id'=>$user->user_id,'coin_id'=>12])->first();
        $icic_wallet = $walletDetail->where(['user_id'=>$user->user_id,'coin_id'=>8])->first();
        //场内冻结的金额--场内买单冻结
        $qc_cn_freeze = $insideTradeBuy->getFreezeAmount($user->user_id,12);//dd($qc_cn_freeze);
        //累计撤单的金额
        $qc_cn_chedan = $walletFlow->whereIn('wallet_id',WalletDetail::query()->where(['user_id'=>$user->user_id,'coin_id'=>12])->pluck('wallet_id')->toArray())->whereIn('flow_type',[19])->sum('flow_number');

        //可提qc冻结--c2c
        $qc_kt_freeze = $c2CTrade->getFreezeAmount($user->user_id);

        //场内icci冻结金额--场内卖单冻结
        $icic_cn_freeze = $insideTradeSell->getFreezeAmount($user->user_id,8);//dd(WalletDetail::query()->where(['user_id'=>$user->user_id,'coin_id'=>8])->pluck('wallet_id')->toArray());
        //场内icic撤单金额
        $icic_cn_chedan = $walletFlow->whereIn('wallet_id',WalletDetail::query()->where(['user_id'=>$user->user_id,'coin_id'=>8])->pluck('wallet_id')->toArray())->whereIn('flow_type',[19])->sum('flow_number');

        //可提icic冻结--提币
        $icic_kt_freeze = $coinTradeOrder->where(['user_id'=>$user->user_id,'coin_id'=>8])->where(['order_type'=>1])->where('order_check_status',0)->sum('order_trade_money');

//        $userTotalBuy = $c2CTrade->getUserTotalBuy1($user->user_id);
//        $userTotalSell = $c2CTrade->getUserTotalSell1($user->user_id);
//
//        $qcWallet = $walletDetail->getOneWallet($user->user_id,12);
//        $icicWallet = $walletDetail->getOneWallet($user->user_id,8);

//        $team = $this->redisHgetAll('INSIDE_TEAM_12_8');

        return view('admin.wallet.new_tongji2',compact('hid','user','qc_cn_ly_1','qc_cn_ly_2','qc_cn_ly_3','qc_cn_qx_1','qc_cn_qx_2','qc_cn_qx_3','qc_kt_ly_1','qc_kt_ly_2','qc_kt_ly_3','qc_kt_qx_1','qc_kt_qx_2','qc_kt_qx_3','qc_kt_qx_4','icic_cn_ly_1','icic_cn_ly_2','icic_cn_ly_3','icic_cn_ly_4','icic_cn_ly_5','icic_cn_ly_6','icic_cn_qx_1','icic_cn_qx_2','icic_cn_qx_3',$icic_cn_qx_3,'icic_kt_ly_1','icic_kt_ly_2','icic_kt_qx_1','icic_kt_qx_2','icic_kt_qx_3','icic_kt_qx_4','qc_wallet','icic_wallet','qc_cn_freeze','qc_cn_chedan','qc_kt_freeze','icic_cn_freeze','icic_cn_chedan','icic_kt_freeze'));
    }



    //用户资产统计
    public function UserMoneyTongJi(Request $request)
    {

        $userPhone = $request->user_phone;


        if ($userPhone){
            $user = User::query()->where(['user_phone' => $userPhone])->first();

            $hlLists = $this->get_relation_hl_account($user->user_id);

            $ttsLists = $this->get_relation_tts_account($user->user_id);

            $hlFlows = [];

            foreach ($hlLists as $hlList){
                $hlFlows[] = ['r'=>$hlList->user_icic_wallet->tts_into,'s'=>$hlList->user_icic_wallet->getTotalSell(),'b'=>$hlList->user_icic_wallet->getTotalBuy()];
            }

            $hl_total_r = 0;$hl_total_s = 0;$hl_total_b = 0;
            foreach ($hlFlows as $hlFlow){
                $hl_total_r += $hlFlow['r'];
                $hl_total_s += $hlFlow['s'];
                $hl_total_b += $hlFlow['b'];
            }

            $total_icic_trans_to_hl = $this->getTotalICICTransToHl($user->user_id);
            $total_icic_trans_from_hl = $this->getTotalICICFromHl($user->user_id);
            $total_r_icic = $this->getTotalRIcic([$user->user_id]);//累计转入
            $total_w_icic = $this->getTotalWIcic([$user->user_id]);//累计转出

            $total_c2c_buy = $this->getC2cTotalBuy($user->user_id);//累计c2c买入
            $total_c2c_sell = $this->getC2cTotalSell($user->user_id);//累计c2c卖出

            $relation_tts_out_to_hl = $this->get_some_tts_out_to_hl_num($ttsLists);
            $relation_tts_from_hl = $this->get_some_tts_from_hl_num($ttsLists);

            $relation_tts_withdraw = $this->get_some_tts_withdraw($ttsLists);
            $relation_tts_recharge = $this->get_some_tts_recharge($ttsLists);

            $relation_tts_c2c_buy = $this->get_some_tts_c2c_buy($ttsLists);
            $relation_tts_c2c_sell = $this->get_some_tts_c2c_sell($ttsLists);


        }else{
            $user = null;
        }



        return view('admin.wallet.zichan_tongji',compact('user','ttsLists','hlLists','hlFlows','hl_total_r','hl_total_s','hl_total_b','total_icic_trans_to_hl','total_icic_trans_from_hl','total_r_icic'
        ,'total_w_icic','total_c2c_buy','total_c2c_sell','relation_tts_from_hl','relation_tts_out_to_hl','relation_tts_withdraw','relation_tts_recharge',
            'relation_tts_c2c_buy','relation_tts_c2c_sell'));



    }





    //获取关联的tts账号
    public function get_relation_tts_account($userId)
    {
        $users = [];
        $userIds = [];


        //1,获取直接转过账的tts账户
        $records = CoinTradeOrder::query()->with([
            'from_wallet.user'
        ])->where(['user_id'=>$userId,'transfer_type'=>2,'order_type'=>2])->get();//像我转过账的

        foreach ($records as $value){
            $users[] = $value->from_wallet->user;
            $userIds[] = $value->from_wallet->user->user_id;
        }
        $records = CoinTradeOrder::query()->with([
            'to_wallet.user'
        ])->where(['user_id'=>$userId,'transfer_type'=>2,'order_type'=>1])->get();//我转过账的

        foreach ($records as $value){
            if (!in_array($value->to_wallet->user->user_id,$userIds)){
                $users[] = $value->to_wallet->user;
                $userIds[] = $value->to_wallet->user->user_id;
            }
        }

        //2.获取间接转过账的tts账户,即我转到hl,hl再转到tts的tts账户/我收到的hl地址中,像该hl地址转过账的tts地址
        if ($this->relation_hl_addresses){
            $records = CoinTradeOrder::query()->with('user')->where('user_id','!=',$userId)->where(function ($q){
                $q->whereIn('order_trade_from',$this->relation_hl_addresses)->orWhereIn('order_trade_to',$this->relation_hl_addresses);
            })->get();
            foreach ($records as $record){
                if (!in_array($record->user_id,$userIds)){
                    $users[] = $record->user;
                    $userIds[] = $record->user_id;
                }
            }
        }else{
            $records = [];
        }







//        dd($users);

        return $users;




    }


    public function get_relation_hl_account($userId)
    {

        $users = [];

        $addresses = [];
        //2.获取间接转过账的tts账户,即我转到hl,hl再转到tts的tts账户/我收到的hl地址中,像该hl地址转过账的tts地址
        $records = CoinTradeOrder::query()->where(['user_id'=>$userId,'transfer_type'=>3,'order_type'=>1])->select(['order_trade_to'])->groupBy('order_trade_to')->get();//我转过账的hl地址

        foreach ($records as $record){
            if (!in_array($record->order_trade_to,$addresses)){
                $addresses[] = $record->order_trade_to;
            }
        }

        $records = CoinTradeOrder::query()->where(['user_id'=>$userId,'transfer_type'=>3,'order_type'=>2])->select(['order_trade_from'])->groupBy('order_trade_from')->get();//像我转过账的hl地址

        foreach ($records as $record){
            if (!in_array($record->order_trade_from,$addresses)){
                $addresses[] = $record->order_trade_from;
            }
        }

        $this->relation_hl_addresses = $addresses;

        $users = (new UserWallet())->getUsersByAddresses($addresses);
//        dd($users);


        return $users;



    }


    //获取累计转出到互链的icic
    public function getTotalICICTransToHl($userId)
    {
        $re = CoinTradeOrder::query()->where(['user_id'=>$userId,'transfer_type'=>3,'order_type'=>1,'coin_id'=>8])->sum('order_trade_money');

        return $re;

    }

    //获取累计收到的互链的icic
    public function getTotalICICFromHl($userId)
    {
        $re = CoinTradeOrder::query()->where(['user_id'=>$userId,'transfer_type'=>3,'order_type'=>2,'coin_id'=>8])->sum('order_trade_money');

        return $re;

    }

    //累计转出icic
    public function getTotalWIcic(array $userIds)
    {
        $re = CoinTradeOrder::query()->where(['order_type'=>1,'coin_id'=>8])->whereIn('user_id',$userIds)->sum('order_trade_money');

        return $re;
    }


    //累计转入icic
    public function getTotalRIcic(array $userIds)
    {
        $re = CoinTradeOrder::query()->where(['order_type'=>2,'coin_id'=>8])->whereIn('user_id',$userIds)->sum('order_trade_money');

        return $re;
    }

    //累计买入usdt
    public function getC2cTotalBuy($userId)
    {
       $re = C2CTrade::where(['user_id'=>$userId,'trade_type'=>1,'trade_status'=>3])->sum('trade_number');
       return $re;
    }


    //累计卖出usdt
    public function getC2cTotalSell($userId)
    {
        $re = C2CTrade::where(['user_id'=>$userId,'trade_type'=>2,'trade_status'=>3])->sum('trade_number');
        return $re;
    }

    //获取一些tts账号累计转出到hl的数量icic
    public function get_some_tts_out_to_hl_num(array $users)
    {
        $userIds = [];
        foreach ($users as $user){
            $userIds[] = $user->user_id;
        }
        if ($userIds == []){
            $re = 0;
        }else{
            $re = CoinTradeOrder::query()->where(['order_type'=>1,'transfer_type'=>3,'coin_id'=>8])->whereIn('user_id',$userIds)->sum('order_trade_money');
        }


        return $re;

    }


    //获取一些tts账号累计收到从hl转入的数量
    public function get_some_tts_from_hl_num(array $users)
    {
        $userIds = [];
        foreach ($users as $user){
            $userIds[] = $user->user_id;
        }
        if ($userIds == []){
            $re = 0;
        }else{
            $re = CoinTradeOrder::query()->where(['order_type'=>2,'transfer_type'=>3,'coin_id'=>8])->whereIn('user_id',$userIds)->sum('order_trade_money');
        }


        return $re;

    }

    //获取一些tts账号累计转出icic
    public function get_some_tts_withdraw(array $users)
    {
        $userIds = [];
        foreach ($users as $user){
            $userIds[] = $user->user_id;
        }
        if ($userIds == []){
            $re = 0;
        }else{
            $re = $this->getTotalWIcic($userIds);
        }


        return $re;

    }


    //获取一些tts账号累计转入icic的数量
    public function get_some_tts_recharge(array $users)
    {
        $userIds = [];
        foreach ($users as $user){
            $userIds[] = $user->user_id;
        }
        if ($userIds == []){
            $re = 0;
        }else{
            $re = $this->getTotalRIcic($userIds);
        }


        return $re;

    }

    //获取一些tts账号累计c2c买入
    public function get_some_tts_c2c_buy(array $users)
    {
        $userIds = [];
        foreach ($users as $user){
            $userIds[] = $user->user_id;
        }
        if ($userIds == []){
            $re = 0;
        }else{
            $re = C2CTrade::where(['trade_type'=>1,'trade_status'=>3])->whereIn('user_id',$userIds)->sum('trade_number');
        }


        return $re;

    }


    //获取一些tts账号累计c2c卖出
    public function get_some_tts_c2c_sell(array $users)
    {
        $userIds = [];
        foreach ($users as $user){
            $userIds[] = $user->user_id;
        }
        if ($userIds == []){
            $re = 0;
        }else{
            $re = C2CTrade::where(['trade_type'=>2,'trade_status'=>3])->whereIn('user_id',$userIds)->sum('trade_number');
        }


        return $re;

    }



}