<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Handlers\ExcelHtml;
use App\Model\CenterWallet;
use App\Model\CenterWalletRecord;
use App\Model\CoinType;
use App\Model\InsideTradeOrder;
use App\Model\WalletDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CenterWalletController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        if ($request->coin_id != null) {
            $data = ['coin_id' => $request->coin_id];
        }
        $search = [];
        if (!empty($request->username)) {
            $search = [['user_name', 'like', '%' . $request->username . '%']];
        };

        $coins = CoinType::all();
        $builder = walletDetail::query()->with('coin', 'user','userIdentify')
            ->where($data)
//            ->where(function ($query) {
//                $query->where('wallet_usable_balance', '>', 0)
//                    ->orWhere('wallet_freeze_balance', '>', 0)
//                    ->orWhere('wallet_withdraw_balance', '>', 0);
//            })
            ->whereHas('user', function ($query) use($search) {
                $query->where($search);
            });


        //可交易
        $canChange = trim($builder->sum('wallet_usable_balance'),'0');

        //可提现
        $canPut = trim($builder->sum('wallet_withdraw_balance'),'0');

        //冻结
        $freeze = trim($builder->sum('wallet_freeze_balance'),'0');
        $ore = trim($builder->sum('ore_pool_balance'),'0');
        $inside_lock = trim($builder->sum('transfer_lock_balance'),'0');

        //排序
        if ($order = $request->get('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                $builder->orderBy($m[1], $m[2]);
            }
        }//dd($builder->latest()->get());
        if ($request->px){
            $colum = [1=>'wallet_usable_balance',2=>'wallet_usable_balance',3=>'wallet_withdraw_balance',4=>'wallet_withdraw_balance',5=>'wallet_freeze_balance',6=>'wallet_freeze_balance',7=>'ore_pool_balance',8=>'ore_pool_balance',9=>'transfer_lock_balance',10=>'transfer_lock_balance'];
            $sc = [1=>'asc',2=>'desc',3=>'asc',4=>'desc',5=>'asc',6=>'desc',7=>'asc',8=>'desc',9=>'asc',10=>'desc'];

            $builder->orderBy($colum[$request->px],$sc[$request->px]);

        }

        if ($request->excel) return $this->outExcel($builder->latest()->get());
        $walletDetails = $builder->latest()->paginate(15);

        $excel = $request->fullUrl().'&excel=1';
        if (strpos($request->fullUrl(),'?') === false) $excel = $request->fullUrl().'?excel=1';

        return view('admin.centerwallet.index', compact('walletDetails', 'coins', 'data', 'canPut', 'canChange', 'order','freeze','excel','ore','inside_lock'));
    }

    public function outExcel($wallets)
    {
        $header = ['用户电话','真实姓名','货币类型','场内余额','可提余额','冻结余额','矿池余额','场内交易锁定余额','是否商家'];
        $list = [];
        foreach($wallets as $wallet){
            $name = $wallet->user->userIdentify ? $wallet->user->userIdentify->identify_name : '-';
            $type2 = ['普通','商家'][$wallet->user->is_business];
            $list[] = [$wallet->user->user_phone,$name,$wallet->coin->coin_name,$wallet->wallet_usable_balance,$wallet->wallet_withdraw_balance,$wallet->wallet_freeze_balance,$wallet->ore_pool_balance,$wallet->transfer_lock_balance,$type2];
        }
        event(new AdminUserBehavior(auth('web')->user()->id,"导出excel:tts_用户钱包信息",'导出excel'));
        return (new ExcelHtml())->ExcelPull('tts_用户钱包信息',$header,$list);

    }


    public function centerWalletFlow(Request $request,CenterWalletRecord $centerWalletRecord,CenterWallet $centerWallet,InsideTradeOrder $insideTradeOrder)
    {
        if (!$request->only('coin_id'))
            return redirect(route($request->route()->getName(),['coin_id'=>CoinType::where('coin_name',env('COIN_SYMBOL','ICIC'))->value('coin_id')]));

        if (!$time = $request->time) $time = 0;

        $condition = $request->input('coin_id') ? $request->only('coin_id') :[];
        $coins = CoinType::all()->toArray();

        $flows1 = DB::table('inside_trade_order')->select(['order_id as id','sell_user_id as user_id','base_coin_id as coin_id',DB::raw('trade_poundage*unit_price as total_money'),'sell_order_number as content','created_at','updated_at'])->where('base_coin_id',$request->coin_id);//dd($flows1);
        $flows2 = DB::table('inside_trade_order')->select(['order_id as id','buy_user_id as user_id','exchange_coin_id as coin_id','trade_poundage as total_money','buy_order_number as content','created_at','updated_at'])->where('exchange_coin_id',$request->coin_id);//dd($flows1);
        $flows = DB::table('real_coin_center_wallet_record')->where($condition);

        switch ($time){
            case 0://历史
                $amount = $centerWallet->where('coin_id',$request->coin_id)->value('total_interest_money');
                break;
            case 1://本日
                $flows1 = $flows1->whereDay('created_at',date('Y-m-d',time()));
                $flows2 = $flows2->whereDay('created_at',date('Y-m-d',time()));
                $flows = $flows->whereDay('created_at',date('Y-m-d',time()));
                $a1 = $flows1->first([DB::raw('sum(trade_poundage*unit_price) as total')]);
                $a1 = $a1 ? $a1->total : 0;
                $amount = $a1 + $flows2->sum('trade_poundage') + $flows->sum('total_money');
                break;
            case 2://本月
                $flows1 = $flows1->whereMonth('created_at',date('m',time()));
                $flows2 = $flows2->whereMonth('created_at',date('m',time()));
                $flows = $flows->whereMonth('created_at',date('m',time()));
                $a1 = $flows1->first([DB::raw('sum(trade_poundage*unit_price) as total')]);//dd($a1);
                $a1 = $a1 ? $a1->total : 0;
                $amount = $a1 + $flows2->sum('trade_poundage') + $flows->sum('total_money');
                break;
        }

        $flows = $flows->unionAll($flows1)->unionAll($flows2);//dd($flows);
        $querySql = $flows->toSql();
        $flows = DB::table(DB::raw("($querySql) as a"))->mergeBindings($flows)
            ->orderBy('created_at','desc')->paginate();//dd($flows);

        return view('admin.centerwallet.center',compact('flows','coins'))->with('amount',$amount);

    }
}
