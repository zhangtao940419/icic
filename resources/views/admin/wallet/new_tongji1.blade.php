@extends('admin.layouts.app')
@section('title', '会员走向统计表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                会员走向统计表
            </li>
        </ul>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="position: absolute; right: 18em; top: 5.9em;">
        <form class="form-search2">
            <span>
                <input type="text" placeholder="用户手机" class="nav-search-input" id="user_phone" name="user_phone" autocomplete="off">
                <button type="submit" style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            会员走向统计表
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
            <th class="center">会员姓名</th>
            <th class="center">会员账号</th>
            <th class="center">总入金量</th>
            <th class="center">总出金量</th>
            <th class="center">盈亏</th>
            <th class="center">场内QC</th>
            <th class="center">可提QC</th>
            <th class="center">冻结QC</th>
            <th class="center">场内ICIC</th>
            <th class="center">可提ICIC</th>
            <th class="center">冻结ICIC</th>
            <th class="center">总资产</th>
            <th class="center">矿池总量</th>

        </tr>
        </thead>

        <tbody>
            <tr>
                <td class="center">{{ $user->userIdentify->identify_name }}</td>
                <td class="center">{{ $user->user_phone }}</td>
                <td class="center">{{ $userTotalBuy }}</td>
                <td class="center">{{ $userTotalSell }}</td>
                <td class="center">{{ $userTotalSell - $userTotalBuy }}</td>
                <td class="center">{{ $qcWallet->wallet_usable_balance }}</td>
                <td class="center">{{ $qcWallet->wallet_withdraw_balance }}</td>
                <td class="center">{{ $qcWallet->wallet_freeze_balance }}</td>
                <td class="center">{{ $icicWallet->wallet_usable_balance }}</td>
                <td class="center">{{ $icicWallet->wallet_withdraw_balance }}</td>
                <td class="center">{{ $icicWallet->wallet_freeze_balance }}</td>
                <td class="center">{{ ($qcWallet->wallet_usable_balance+$qcWallet->wallet_withdraw_balance+$qcWallet->wallet_freeze_balance) + ($icicWallet->wallet_usable_balance+$icicWallet->wallet_withdraw_balance+$icicWallet->wallet_freeze_balance)*$team['current_price'] }}(qc)</td>
                <td class="center">{{ $icicWallet->ore_pool_balance * $team['current_price'] }}(qc)</td>
            </tr>
            {{--<tr>--}}
                {{--<td class="center">内部撮合人员</td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
                {{--<td class="center"></td>--}}
            {{--</tr>--}}

        </tbody>
    </table>
    @endif
@endsection
@section('myJs')
    <script>

    </script>
@endsection
