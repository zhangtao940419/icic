<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/20
 * Time: 11:35
 */

namespace App\Logic;

use App\Model\ContractActivity;
use App\Model\ContractPriceAccount;
use App\Model\ContractPriceFloat;
use App\Model\ContractSetting;
use App\Model\ContractUserBuyRecords;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use Illuminate\Support\Facades\DB;


class ContractLogic
{

    protected $contractSetting,$contractActivity,$contractPriceAccount,$walletDetail,$contractUserBuyRecords,$contractPriceFloat;


    public function __construct(ContractSetting $contractSetting,ContractActivity $contractActivity,ContractPriceAccount $contractPriceAccount,
                                WalletDetail $walletDetail,ContractUserBuyRecords $contractUserBuyRecords,ContractPriceFloat $contractPriceFloat)
    {
        $this->contractSetting = $contractSetting;
        $this->contractActivity = $contractActivity;
        $this->contractPriceAccount = $contractPriceAccount;
        $this->walletDetail = $walletDetail;
        $this->contractUserBuyRecords = $contractUserBuyRecords;
        $this->contractPriceFloat = $contractPriceFloat;

    }


    public function getContractMsg()
    {

        $activity = $this->contractActivity->getNewest();

        if (!$activity) return api_response()->zidingyi('合约交易尚未开始');

        $user = current_user();

        $balance = $this->walletDetail->getCoinUsableBalance1($activity->coin_id,$user->user_id);

        $jg_times = strtotime($activity->jg_time);

        $t_times = $jg_times - (2 * 60);
        $k_status = 1;
        if (time() > $t_times) $k_status = 0;

        $djs = get_daojishi1($jg_times - time());

        return api_response()->successWithData(['activity' => $activity,'k_status'=>$k_status,'balance' => $balance,'djs' => $djs]);

    }


    //盘面
    public function getContractMarket($activityId)
    {
        $activity = $this->contractActivity->find($activityId);

//        $b_market = $this->contractPriceAccount->getBuyMarket();
//
//        $s_market = $this->contractPriceAccount->getSellMarket();

        $newest_price = $this->contractPriceFloat->getNewestPrice();

        $x_s = [
            ['price' => round((1+(rand(9,10)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)],
            ['price' => round((1+(rand(7,8)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)],
            ['price' => round((1+(rand(5,6)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)],
            ['price' => round((1+(rand(3,4)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)],
            ['price' => round((1+(rand(1,2)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)]
        ];
        $x_b = [
            ['price' => round((1-(rand(1,2)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)],
            ['price' => round((1-(rand(3,4)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)],
            ['price' => round((1-(rand(5,6)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)],
            ['price' => round((1-(rand(7,8)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)],
            ['price' => round((1-(rand(9,10)*0.01))*$newest_price,4),'number'=>(int)getRandFloatNumber(10000,-99,100)]
        ];

        if ($activity->jg_status == 0){
            $jg_price = $newest_price;
        }else{
            $jg_price = $activity->now_price;
        }

        return api_response()->successWithData(['price'=>$newest_price,'jg_price' => $jg_price,'sell_market' => $x_s,'buy_market'=>$x_b]);


    }

    //购买
    public function buy($activityId,$amount,$type)
    {

        $activity = $this->contractActivity->find($activityId);
        if (!$activity) return api_response()->error();

        $jg_times = strtotime($activity->jg_time);
        $t_times = $jg_times - (2 * 60);
        if (time() > $t_times) return api_response()->zidingyi('已停仓');
        $user = current_user();

        $wallet = $this->walletDetail->getOneRecord($user->user_id,$activity->coin_id);

        if (!$wallet || ($wallet->wallet_usable_balance < $amount)) return api_response()->zidingyi('余额不足');

        DB::beginTransaction();

        $r1 = $wallet->reduceUsableBalance($activity->coin_id,$user->user_id,$amount);
        $r2 = $this->contractUserBuyRecords->insertOne($user->user_id,$activityId,$type,$amount);
        $r3 = (new WalletFlow())->insertOne($user->user_id,$wallet->wallet_id,$activity->coin_id,$amount,27,2,'合约交易',1);

        if ($r1 && $r2 && $r3){
            DB::commit();return api_response()->success();
        }

        DB::rollBack();
        return api_response()->error();



    }

    //购买记录
    public function getBuyRecords()
    {
        $user = current_user();


        $records = $this->contractUserBuyRecords->getUserBuyRecords($user->user_id);


        return api_response()->successWithData(['records' => $records]);


    }





}