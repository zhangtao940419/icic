<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/16
 * Time: 16:29
 */

namespace App\Http\Controllers\Admin;


use App\Events\AdminUserBehavior;
use App\Handlers\ExcelHtml;
use App\Http\Controllers\Controller;
use App\Model\CoinType;
use App\Model\StoCoinStage;
use App\Model\StoRewardFlow;
use App\Model\UserBuyStoCoinRecord;
use App\ModelFilters\StoRewardFlowFilter;
use App\ModelFilters\UserBuyStoCoinRecordFilter;
use App\Traits\RedisTool;
use Illuminate\Http\Request;

class StoController extends Controller
{

    use RedisTool;


    //sto订单
    public function index(Request $request)
    {

        $query = UserBuyStoCoinRecord::filter($request->all(), UserBuyStoCoinRecordFilter::class);

        $sum_base = $query->sum('base_trade_number');
        $sum_exchange = $query->sum('exchange_trade_number');

        if ($request->excel) return $this->outExcel($query->with(['base_coin','exchange_coin','user.userIdentify','stage','day'])->orderBy('exchange_trade_number','desc')->get());

        $records = $query->with(['base_coin','exchange_coin','user.userIdentify','stage','day'])->latest('record_id')->paginate();//dd($records);

        $stoCoinList = CoinType::query()->where(['is_sto'=>1])->get();

        $rateList = StoCoinStage::query()->select(['exchange_rate'])->groupBy('exchange_rate')->get();//dd($rateList->toArray());

        $excel = $request->fullUrl().'&excel=1';
        if (strpos($request->fullUrl(),'?') === false) $excel = $request->fullUrl().'?excel=1';
        return view('admin.sto.index',compact('records','sum_base','sum_exchange','stoCoinList','rateList','excel'));


    }

    public function outExcel($records)
    {
        $header = ['id','会员电话','真实姓名','基币','兑币','阶段','天数','花费基币数量','得到兑币数量','比率(基币--/--兑币)','时间'];
        $list = [];
        foreach($records as $record){//dd($record->base_coin->coin_name);
            $l1 = $record->user->user_id;
            $l2 = $record->user->user_phone;//if (!$record->user->userIdentify) dd($record);
            $l3 = $record->user->userIdentify->identify_name;
            $l4 = $record->base_coin->coin_name;
            $l5 = $record->exchange_coin->coin_name;
            $l6 = $record->stage == null ? '--' :$record->stage->stage_number;
            $l7 = $record->day == null ? '--' :$record->day->issue_day;
            $l8 = $record->base_trade_number;
            $l9 = $record->exchange_trade_number;
            $l10 = trim(trim('1--/--'.$record->exchange_rate,'0'),'.');
            $l11 = $record->created_at;
//            dd(1);
            $list[] = [$l1,$l2,$l3,$l4,$l5,$l6,$l7,$l8,$l9,$l10,$l11];

//            $list[] = [$record->user->user_id,$record->user->user_phone,$record->user->userIdentify->identify_name,$record->base_coin->coin_name,$record->exchange_coin->coin_name,$record->stage == null ? '--' :$record->stage->stage_number,$record->day == null ? '--' :$record->day->issue_day,$record->base_trade_number,$record->exchange_trade_number,'1:'.rtrim(rtrim($record->exchange_rate,'0'),'.'),$record->created_at];
        }
//        dd($list);
        event(new AdminUserBehavior(auth('web')->user()->id,"导出excel:tts_sto购买记录表",'导出excel'));
        return (new ExcelHtml())->ExcelPull('tts_sto购买记录表',$header,$list);

    }

    //sto推荐奖励记录
    public function reward(Request $request)
    {

        $query = StoRewardFlow::filter($request->all(), StoRewardFlowFilter::class)->where(['flow_type'=>3]);

        $sum = $query->sum('flow_amount');


        $records = $query->with(['user','s_user','record','coin'])->latest('id')->paginate();//dd($records);


        return view('admin.sto.reward',compact('records','sum'));


    }


    public function setting()
    {

        $first_percent = $this->stringGet('sto_first_percent');
        $normal_percent = $this->stringGet('sto_normal_percent');

        $single_min = $this->stringGet('sto_single_min');
        $single_max = $this->stringGet('sto_single_max');


        $first_percent = $first_percent == null ? 2 : $first_percent;
        $normal_percent = $normal_percent == null ? 1 : $normal_percent;

        $single_min = $single_min == null ? 100 : $single_min;

        $single_max = $single_max == null ? 100000 : $single_max;

        return view('admin.sto.setting',compact('first_percent','normal_percent','single_max','single_min'));

    }



    public function update(Request $request)
    {


        if ($request->type == 1){

            if ($request->first_percent <= 0 || $request->first_percent >= 100 || !is_numeric($request->first_percent)) return back()->with('danger','无效的数字');

            $this->stringSet('sto_first_percent',$request->first_percent);
            return back()->with('success','操作成功');

        }elseif ($request->type == 2){

            if ($request->normal_percent <= 0 || $request->normal_percent >= 100 || !is_numeric($request->normal_percent)) return back()->with('danger','无效的数字');

            $this->stringSet('sto_normal_percent',$request->normal_percent);
            return back()->with('success','操作成功');


        }elseif ($request->type == 3){

            if (!is_numeric($request->single_min) || !is_numeric($request->single_max)) return back()->with('danger','无效的数字');

            $this->stringSet('sto_single_min',$request->single_min);
            $this->stringSet('sto_single_max',$request->single_max);
            return back()->with('success','操作成功');


        }

    }





}