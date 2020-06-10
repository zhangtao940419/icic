@extends('admin.layouts.app')
@section('title', '统计表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                统计表
            </li>
        </ul>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="position: absolute; right: 18em; top: 5.9em;">
        <form class="form-search2">
            <span>
                <input type="text" placeholder="开盘充值内部撮合数量" class="nav-search-input" id="s_num" name="s_num" autocomplete="off" @if($hid) value="{{ $snum }}" @endif>
                <button type="submit" style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            统计表
        </h1>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div>
    </div>
    @if($hid)

    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center" colspan="7">日期 : {{ date('Y-m-d') }}</th>
            {{--<th class="center">交易号</th>--}}
            {{--<th class="center">买卖类型</th>--}}
            {{--<th class="center">货币类型</th>--}}
            {{--<th class="center">交易数量</th>--}}
            {{--<th class="center">单价</th>--}}
            {{--<th class="center">价值</th>--}}
            <th class="center"> </th>

        </tr>
        </thead>

        <tbody>
            <tr>
                <td class="center"></td>
                <td class="center">当天场内ICIC数量</td>
                <td class="center">当天可提ICIC数量</td>
                <td class="center">当天冻结icic数量</td>
                <td class="center">当天场内QC数量</td>
                <td class="center">当天可提QC数量</td>
                <td class="center">当天冻结QC数量</td>
                <td class="center">待对冲icic</td>
            </tr>
            <tr>
                <td class="center">内部撮合人员</td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">东13078670552</td>
                <td class="center">{{ $wi1->wallet_usable_balance }}</td>
                <td class="center">{{ $wi1->wallet_withdraw_balance }}</td>
                <td class="center">{{ $wi1->wallet_freeze_balance }}</td>
                <td class="center">{{ $wq1->wallet_usable_balance }}</td>
                <td class="center">{{ $wq1->wallet_withdraw_balance }}</td>
                <td class="center">{{ $wq1->wallet_freeze_balance }}</td>
                <td class="center">{{ $wi1->wallet_withdraw_balance + $wi1->wallet_freeze_balance }}</td>
            </tr>
            <tr>
                <td class="center">清13929771840</td>
                <td class="center">{{ $wi2->wallet_usable_balance }}</td>
                <td class="center">{{ $wi2->wallet_withdraw_balance }}</td>
                <td class="center">{{ $wi2->wallet_freeze_balance }}</td>
                <td class="center">{{ $wq2->wallet_usable_balance }}</td>
                <td class="center">{{ $wq2->wallet_withdraw_balance }}</td>
                <td class="center">{{ $wq2->wallet_freeze_balance }}</td>
                <td class="center">{{ $wi2->wallet_withdraw_balance + $wi2->wallet_freeze_balance }}</td>
            </tr>
            <tr>
                <td class="center">苏18211558731</td>
                <td class="center">{{ $wi3->wallet_usable_balance }}</td>
                <td class="center">{{ $wi3->wallet_withdraw_balance }}</td>
                <td class="center">{{ $wi3->wallet_freeze_balance }}</td>
                <td class="center">{{ $wq3->wallet_usable_balance }}</td>
                <td class="center">{{ $wq3->wallet_withdraw_balance }}</td>
                <td class="center">{{ $wq3->wallet_freeze_balance }}</td>
                <td class="center">{{ $wi3->wallet_withdraw_balance + $wi3->wallet_freeze_balance }}</td>
            </tr>
            <tr>
                <td class="center">统计总量</td>
                <td class="center" colspan="3">{{ $tui }}</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center" colspan="3">{{ $tuq }}</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">投放市场</td>
                <td class="center" colspan="6">{{ $snum - $tui }}(约{{ $team['current_price'] * ($snum - $tui) }}qc)</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center">盘口价格 : {{ $team['current_price'] }}</td>
            </tr>
            <tr>
                <td class="center">入金总量(0928始）</td>
                <td class="center" colspan="6">{{ $c2c_t_b - $c2c_t_s }}</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">当天入金量</td>
                <td class="center" colspan="6">{{ $c2c_d_t_b - $c2c_d_t_s }}</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">流通总量ICIC</td>
                <td class="center" colspan="6">{{ $user_total_icic - $tui }}  (约 {{ ($user_total_icic - $tui) * $team['current_price'] }}  qc )</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">市场流通QC</td>
                <td class="center" colspan="6">{{ $user_total_qc - $tuq }}</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">市场价值总资产QC</td>
                <td class="center" colspan="6">{{ ($user_total_icic - $tui) * $team['current_price'] + $user_total_qc - $tuq }}</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">日产ICIC</td>
                <td class="center" colspan="6">{{ $user_total_icic - $last_day_total_icic }}</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">会员场内可提QC</td>
                <td class="center" colspan="6">{{ $totalInside - ($wq1->wallet_usable_balance +$wq2->wallet_usable_balance+$wq3->wallet_usable_balance ) }}</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">会员场外可提QC</td>
                <td class="center" colspan="6">{{ $totalWithdraw - ($bTotalWithdraw+$wq1->wallet_withdraw_balance +$wq2->wallet_withdraw_balance+$wq3->wallet_withdraw_balance) }}</td>
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                <td class="center"></td>
            </tr>
        </tbody>
    </table>
@endif
@endsection
@section('myJs')
    <script>

    </script>
@endsection
