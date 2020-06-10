<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 14:56
 */

namespace App\Http\Controllers\Web\Api\V1;

use App\Http\Controllers\Web\BaseController;
use App\Model\C2CTrade;
use App\Model\C2CTradeOrder;
use App\Model\WalletDetail;
use App\Model\CoinType;
use App\Model\CoinTradeOrder;
use App\Model\EthToken;
use App\Model\Admin\coinTransaction;
use Illuminate\Http\Request;
use App\Traits\Tools;
use App\Traits\RedisTool;
use App\Server\CoinServer;
use App\Model\CoinFees;
use Illuminate\Support\Facades\DB;


class WalletController extends BaseController
{
    use Tools,RedisTool;
    private $coinTransaction;

    public $coinSetting;

    public function __construct(CoinFees $coinFees)
    {
        $this->coinSetting = $coinFees;
    }

    //返回钱包首页数据
    public function index(WalletDetail $walletDetail,Request $request,coinTransaction $coinTransaction)
    {//dd($this->changeTo_Other_Coin(1));
        $this->coinTransaction = $coinTransaction;

        //1.货币的列表
        $walletList = $walletDetail->getUserWallet($request->input('user_id'));//dd($walletList);
        foreach ($walletList as $key=>$value){
            $walletList[$key]['wallet_usable_balance'] = bcadd($value['wallet_usable_balance'],$value['wallet_withdraw_balance'],8);
        }
        $totalBalance = $this->getTotalBalance($walletList);
        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>['walletList'=>$walletList,'totalBalance'=>$totalBalance[0],'totalCNY'=>$totalBalance[1]]]);
    }

    /*计算总的余额近似值,全部换算成btc*/
    public function getTotalBalance($walletList)
    {
//        dd($this->changeTo_Other_Coin(1));
        $bitcoinId = $this->getCoinId('BTC');
        if ($bitcoinId == 0) return [0,0];

        $btcExchange = $this->changeTo_Other_Coin($bitcoinId);
        $totalBtc = 0;
        foreach ($walletList as $wallet){
            switch ($wallet['coin_name']['coin_name']){
                case 'BTC':
                    $totalBtc = bcmul(bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),$btcExchange,6);
                    break;
                case 'ETH':
                    $ethExchangeBtc = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
                    $totalBtc = bcadd($ethExchangeBtc,$totalBtc,6);
                    break;
                default:
                    $exchangeBtc = bcmul($this->changeTo_Other_Coin($wallet['coin_id']),bcadd(bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8),$wallet['wallet_freeze_balance'],8),6);
                    $totalBtc = bcadd($exchangeBtc,$totalBtc,6);
            }
        }
        return [$totalBtc,$totalBtc];

    }


    /*充值信息接口*/
    public function getRechargeMsg(Request $request,WalletDetail $walletDetail)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);
        $data = $walletDetail->getRechargeMsg($request->input('wallet_id'));
        if ($data['coin_fees']['recharge_on_off_status'])
        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>$data]);

        return response()->json(['status_code'=>self::STATUS_CODE_CANNOT_HANDLE,'message'=>'暂时不可操作']);

    }

    /*提币信息接口*/
    public function getWithdrawMsg(Request $request,WalletDetail $walletDetail)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
//            'test' => 'required|regex:/^[0-9]{1,10}\.[0-9]{1,6}$/'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        $data = $walletDetail->getWithdrawMsg($request->input('wallet_id'));
        $data['wallet_usable_balance'] = $data['wallet_withdraw_balance'];

        if ($data['coin_fees']['withdraw_on_off_status'])
            return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>$data]);

        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'USDT提币暂不开放','data'=>$data]);

    }

    /*获取指定账户的余额详情*/
    public function getWalletDetail(Request $request,WalletDetail $walletDetail)
    {
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        if (! ($result = $walletDetail->getRecordById($request->input('wallet_id'),$request->input('user_id')))) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        $result['wallet_total_balance'] = bcadd($result['wallet_usable_balance'],$result['wallet_freeze_balance'],8);
        //$result['wallet_total_balance'] = (string)number_format(($result['wallet_usable_balance'] + $result['wallet_freeze_balance']),8,'.','');
        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>['walletDetail'=>$result]]);
    }


    /*获取正在处理中的转账*/
    public function getWithdrawOrders(Request $request,CoinTradeOrder $coinTradeOrder,WalletDetail $walletDetail)
    {
        if ($this->verifyField($request->all(),[
            'coin_id' => 'required|integer'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        $wallet = $walletDetail->getOneRecord($request->input('user_id'),$request->input('coin_id'));
        $wallet = $wallet? $wallet : [];
        if ($wallet){
            $wallet['total_usable_balance'] = bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8);
        }

        $records = $coinTradeOrder->getUserOrders($request->input('user_id'),$request->input('coin_id'));
        $records = $records ? $records->toArray() : [];
        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>['wallet'=>$wallet,'orders'=>$records]]);

    }

    /*获取usdt交易记录*/
    public function getUSDTOrders(Request $request,C2CTradeOrder $c2CTradeOrder,CoinTradeOrder $coinTradeOrder,WalletDetail $walletDetail)
    {
        if ($this->verifyField($request->all(),[
            'coin_id' => 'required|integer'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        $Urecords = C2CTrade::select('trade_number','updated_at','trade_type','created_at','trade_price')->where(['user_id'=>$request->input('user_id'),'coin_id'=>5,'trade_status'=>3])->orderByDesc('updated_at')->get()->toArray();
        $Brecords = $c2CTradeOrder->getBOrders($request->input('user_id'));
        foreach ($Brecords as $key=>$value){
            if ($value['trade_msg']['trade_type'] == 1){
                $Brecords[$key]['trade_msg']['trade_type'] = 2;
            }else{
                $Brecords[$key]['trade_msg']['trade_type'] = 1;
            }
            $Urecords[] =  $Brecords[$key]['trade_msg'];
        }
        foreach ($Urecords as $key=>$value){
            if ($value['trade_type'] == 1){
                $Urecords[$key]['trade_type'] = 2;
            }else{
                $Urecords[$key]['trade_type'] = 1;
            }
            $Urecords[$key]['way'] = 2;
        }
        $records = $coinTradeOrder->getUserOrders($request->input('user_id'),$request->input('coin_id'));
        $records = $records ? $records->toArray() : [];
        foreach ($records as $key=>$value){
            $value['way'] = 1;
            $Urecords[] = $value;
        }
        foreach ($Urecords as $key=>$urecord){
            $Urecords[$key]['time'] = strtotime($urecord['updated_at']);
//            $Urecords[$key]['order_type'] = $urecord['trade_type'];

        }
        foreach ($Urecords as $key=>$value){
            $volume[$key]  = $value['time'];
        }
        if ($Urecords){
            array_multisort($volume,SORT_DESC,$Urecords);
        }

        $wallet = $walletDetail->getOneRecord($request->input('user_id'),$request->input('coin_id'));
        $wallet = $wallet? $wallet : [];
        if ($wallet){
            $wallet['total_usable_balance'] = bcadd($wallet['wallet_usable_balance'],$wallet['wallet_withdraw_balance'],8);
        }
        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>['wallet'=>$wallet,'orders'=>$Urecords]]);


    }


//////////////////////////////////////////////////////////////////////////////////////
    /*
     * 用户发起提币
     * */
    public function withdrawCoin(WalletDetail $walletDetail,Request $request,CoinServer $coinServer)
    {
        //dd(0.000000000-1);
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
            'to_address' => 'required',
            'amount' => 'required'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        $result = $walletDetail->getRecordById($request->input('wallet_id'),$request->input('user_id'));
        if (! $result) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);//检查账户和userid对不对的上

        if ($result['wallet_address'] == $request->input('to_address'))
            return response()->json(['status_code'=>self::STATUS_CODE_CANNOT_TRANSFER_SELF,'message'=>'不能给自己转账']);

        if (!$result['coin_fees']['withdraw_on_off_status'])
            return response()->json(['status_code'=>self::STATUS_CODE_CANNOT_HANDLE,'message'=>'USDT提币暂不开放']);

        if (
            !is_numeric($request->input('amount'))
            || (bccomp($result['coin_fees']['withdraw_min'],$request->input('amount'),8)==1)
            || (bccomp($request->input('amount'),$result['coin_fees']['withdraw_max'],8)==1)
        ){
            return response()->json(['status_code'=>self::STATUS_CODE_AMOUNT_ERROR,'message'=>'请输入'.$result['coin_fees']['withdraw_min'].'到'.$result['coin_fees']['withdraw_max'].'之间的数量']);
        }

        //检查数据库的可用余额是否大于提取的金额amount
        if (bccomp($request->input('amount'),$result['wallet_withdraw_balance'],8) == 1)
            return response()->json(['status_code'=>self::STATUS_CODE_NOTSUFFICIENT_FUNDS,'message'=>'余额不足']);

        switch ($result['coin_name']['coin_name']){
            case 'BTC':
                $result = $coinServer->withdrawCoin($result['wallet_account'],$result['wallet_id'],$request->input('to_address'),(string)$request->input('amount'),$result['coin_id'],'BTC',$request->input('user_id'));
                break;
            case 'ETH':
                $result = $coinServer->withdrawCoin('',$result['wallet_id'],$request->input('to_address'),(string)$request->input('amount'),$result['coin_id'],'ETH',$request->input('user_id'));
                break;
            default:
                //$token = (new EthToken())->getRecordByCoinId($result['coin_id']);dd($token);
                $result = $coinServer->withdrawCoin('',$result['wallet_id'],$request->input('to_address'),(string)$request->input('amount'),$result['coin_id'],'TOKEN',$request->input('user_id'));
        }

        return $this->withdrawResponse($result);

    }

    public function getWithdrawFee(Request $request)
    {
        if ($this->verifyField($request->all(),[
            'coin_id' => 'required|integer',
            'amount' => 'required'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        $coinFees = CoinFees::where(['coin_id'=>$request->input('coin_id'),'is_usable'=>1])->first()->toArray();

        switch ($coinFees['fee_type']){
            case 1:
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>['fee'=>$coinFees['fixed_fee']]]);
                break;
            case 2:
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'获取成功','data'=>['fee'=>bcmul($coinFees['percent_fee']/100,$request->input('amount'),6)]]);
                break;
        }



    }



    /*提币定义返回数据*/
    public function withdrawResponse(int $result)
    {
        switch ($result){
            case 0:
                return response()->json(['status_code'=>self::STATUS_CODE_UNKNOWN_ERROR,'message'=>'未知的错误,请联系客服']);
                break;
            case 1:
                return response()->json(['status_code'=>self::STATUS_CODE_NOTSUFFICIENT_FUNDS,'message'=>'余额不足']);
                break;
            case 2:
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
                break;
            default:

        }
    }

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*转账 可提转交易*/
    function transferAccounts(Request $request,WalletDetail $walletDetail){
        if ($this->verifyField($request->all(),[
            'wallet_id' => 'required|integer',
            'amount' => 'required|numeric'
        ])) return response()->json(['status_code'=>self::STATUS_CODE_PARAMETER_ERROR,'message'=>'参数错误']);

        switch ($walletDetail->transferAccount($request->input('wallet_id'),$request->input('user_id'),$request->input('amount'))){
            case 0:
                return response()->json(['status_code'=>self::STATUS_CODE_NOTSUFFICIENT_FUNDS,'message'=>'余额不足']);
                break;
            case 1:
                return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'操作成功']);
                break;
            case 2:
                return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
                break;
            case 3:
                return response()->json(['status_code'=>self::STATUS_CODE_HANDLE_FAIL,'message'=>'操作失败']);
                break;

        }


    }

    /*查询usdt余额*/
    function getUSDTBalance(Request $request)
    {
        $usdt = CoinType::where('coin_name','USDT')->first();
        $balance = WalletDetail::where(['coin_id'=>$usdt->coin_id,'user_id'=>$request->input('user_id')])->select('wallet_usable_balance','wallet_withdraw_balance')->first()->toArray();

        return response()->json(['status_code'=>self::STATUS_CODE_SUCCESS,'message'=>'查询成功','data'=>['balance'=>$balance]]);


    }



}