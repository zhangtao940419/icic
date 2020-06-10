@extends('admin.layouts.app')
@section('title', '会员qc及icic明细表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                会员qc及icic明细表
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
            会员qc及icic明细表
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
            <th class="center">手机号</th>
            <th class="center" colspan="5">场内qc明细</th>
            {{--<th class="center">总入金量</th>--}}
            {{--<th class="center">总出金量</th>--}}
            {{--<th class="center">盈亏</th>--}}
            {{--<th class="center">场内QC</th>--}}
            <th class="center" colspan="5">可提QC明细</th>
            {{--<th class="center">冻结QC</th>--}}
            {{--<th class="center">场内ICIC</th>--}}
            {{--<th class="center">可提ICIC</th>--}}
            {{--<th class="center">冻结ICIC</th>--}}

        </tr>
        </thead>

        <tbody>
            <tr>
                <td class="center" rowspan="10">{{ $user->user_phone }}({{ $user->userIdentify->identify_name }})</td>
                <td class="center">场内qc现有数量</td>
                <td class="center">场内qc来源</td>
                <td class="center">数量/明细</td>
                <td class="center">场内qc去向（记录）</td>
                <td class="center">数量/明细</td>
                <td class="center">可提现qc现有数量</td>
                <td class="center">可提qc来源</td>
                <td class="center">数量/明细</td>
                <td class="center">可提qc去向（记录）</td>
                <td class="center">数量/明细</td>
            </tr>
            <tr>
                {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>
            <tr>
                {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
                <td class="center" rowspan="8">{{ $qc_wallet->wallet_usable_balance + $qc_cn_freeze }}</td>
                <td class="center">1.C2C购买</td>
                <td class="center">{{ $qc_cn_ly_1 }}</td>
                <td class="center">1.购买ICIC</td>
                <td class="center">{{ $qc_cn_qx_1 - $qc_cn_chedan - $qc_cn_freeze }}</td>
                <td class="center" rowspan="8">{{ $qc_wallet->wallet_withdraw_balance + $qc_kt_freeze }}</td>
                <td class="center">1.场内出售ICIC获得</td>
                <td class="center">{{ $qc_kt_ly_1 }}</td>
                <td class="center">1.出售qc</td>
                <td class="center">{{ $qc_kt_qx_1 }}</td>
            </tr>

            <tr>
                {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
                {{--<td class="center">{{ $user->user_phone }}</td>--}}
                <td class="center">2.后台增加</td>
                <td class="center">{{ $qc_cn_ly_2 }}</td>
                <td class="center">2.后台减少</td>
                <td class="center">{{ $qc_cn_qx_2 }}</td>
                {{--<td class="center"></td>--}}
                <td class="center">2.后台增加</td>
                <td class="center">{{ $qc_kt_ly_2 }}</td>
                <td class="center">2.后台减少</td>
                <td class="center">{{ $qc_kt_qx_2 }}</td>
            </tr>
            <tr>
                {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
                {{--<td class="center">{{ $user->user_phone }}</td>--}}
                <td class="center">3.内部用户从可提划转</td>
                <td class="center">{{ $qc_cn_ly_3 }}</td>
                <td class="center">3.sto买入</td>
                <td class="center">{{ $qc_cn_qx_3 }}</td>
                {{--<td class="center"></td>--}}
                <td class="center">3.商家接会员卖单</td>
                <td class="center">{{ $qc_kt_ly_3 }}</td>
                <td class="center">3.商家接会员买单</td>
                <td class="center">{{ $qc_kt_qx_3 }}</td>
            </tr>
            <tr>
                {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
                {{--<td class="center">{{ $user->user_phone }}</td>--}}
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                {{--<td class="center"></td>--}}
                <td class="center">未知来源（重点标记）</td>
                <td class="center">{{ $qc_kt_qx_1+$qc_kt_qx_2+$qc_kt_qx_3+$qc_kt_qx_4 + $qc_wallet->wallet_withdraw_balance + $qc_kt_freeze - ($qc_kt_ly_1+$qc_kt_ly_2+$qc_kt_ly_3) }}</td>
                <td class="center">4.用户划转到场内</td>
                <td class="center">{{ $qc_kt_qx_4 }}</td>
            </tr>
            <tr>
                {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
                {{--<td class="center">{{ $user->user_phone }}</td>--}}
                <td class="center">未知来源（比对计算）</td>
                <td class="center">{{ $qc_cn_qx_1 - $qc_cn_chedan - $qc_cn_freeze+$qc_cn_qx_2+$qc_cn_qx_3 + $qc_wallet->wallet_usable_balance + $qc_cn_freeze - ($qc_cn_ly_1+$qc_cn_ly_2+$qc_cn_ly_3) }}</td>
                <td class="center"></td>
                <td class="center"></td>
                {{--<td class="center"></td>--}}
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>
            <tr>
                {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
                {{--<td class="center">{{ $user->user_phone }}</td>--}}
                <td class="center">总数量</td>
                <td class="center">{{ $qc_cn_ly_1+$qc_cn_ly_2+$qc_cn_ly_3 + ($qc_cn_qx_1+$qc_cn_qx_2+$qc_cn_qx_3 + $qc_wallet->wallet_usable_balance + $qc_cn_freeze - ($qc_cn_ly_1+$qc_cn_ly_2+$qc_cn_ly_3)) }}</td>
                <td class="center">总数量</td>
                <td class="center">{{ $qc_cn_qx_1+$qc_cn_qx_2+$qc_cn_qx_3 - $qc_cn_chedan - $qc_cn_freeze }}</td>
                {{--<td class="center"></td>--}}
                <td class="center">总数量</td>
                <td class="center">{{ $qc_kt_ly_1+$qc_kt_ly_2+$qc_kt_ly_3 + ($qc_kt_qx_1+$qc_kt_qx_2+$qc_kt_qx_3+$qc_kt_qx_4 + $qc_wallet->wallet_withdraw_balance + $qc_kt_freeze - ($qc_kt_ly_1+$qc_kt_ly_2+$qc_kt_ly_3)) }}</td>
                <td class="center">总数量</td>
                <td class="center">{{ $qc_kt_qx_1+$qc_kt_qx_2+$qc_kt_qx_3+$qc_kt_qx_4 }}</td>
            </tr>
            <tr>
                {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
                {{--<td class="center">{{ $user->user_phone }}</td>--}}
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                {{--<td class="center"></td>--}}
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>
            <tr>
                {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
                {{--<td class="center">{{ $user->user_phone }}</td>--}}
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                {{--<td class="center"></td>--}}
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>

        </tbody>
    </table>
        <hr>
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">手机号</th>
            <th class="center" colspan="5">场内icic明细</th>
            {{--<th class="center">总入金量</th>--}}
            {{--<th class="center">总出金量</th>--}}
            {{--<th class="center">盈亏</th>--}}
            {{--<th class="center">场内QC</th>--}}
            <th class="center" colspan="5">可提icic明细</th>
            {{--<th class="center">冻结QC</th>--}}
            {{--<th class="center">场内ICIC</th>--}}
            {{--<th class="center">可提ICIC</th>--}}
            {{--<th class="center">冻结ICIC</th>--}}

        </tr>
        </thead>

        <tbody>
        <tr>
            <td class="center" rowspan="10">{{ $user->user_phone }}({{ $user->userIdentify->identify_name }})</td>
            <td class="center">场内icic现有数量</td>
            <td class="center">场内icic来源</td>
            <td class="center">数量/明细</td>
            <td class="center">场内icic去向（记录）</td>
            <td class="center">数量/明细</td>
            <td class="center">可提现icic现有数量</td>
            <td class="center">可提icic来源</td>
            <td class="center">数量/明细</td>
            <td class="center">可提icic去向（记录）</td>
            <td class="center">数量/明细</td>
        </tr>
        <tr>
            {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
        </tr>
        <tr>
            {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
            <td class="center" rowspan="8">{{ $icic_wallet->wallet_usable_balance + $icic_cn_freeze }}</td>
            <td class="center">1.TTS其他帐号转入</td>
            <td class="center">{{ $icic_cn_ly_1 }}</td>
            <td class="center">1.场内出售ICIC</td>
            <td class="center">{{ $icic_cn_qx_1 - $icic_cn_freeze - $icic_cn_chedan }}</td>
            <td class="center" rowspan="8">{{ $icic_wallet->wallet_withdraw_balance + $icic_kt_freeze }}</td>
            <td class="center">1.场内购买ICIC</td>
            <td class="center">{{ $icic_kt_ly_1 }}</td>
            <td class="center">1.提币到外部</td>
            <td class="center">{{ $icic_kt_qx_1 }}</td>
        </tr>

        <tr>
            {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
            {{--<td class="center">{{ $user->user_phone }}</td>--}}
            <td class="center">2.后台增加</td>
            <td class="center">{{ $icic_cn_ly_2 }}</td>
            <td class="center">2.参与STO</td>
            <td class="center">{{ $icic_cn_qx_2 }}</td>
            {{--<td class="center"></td>--}}
            <td class="center">2.后台增加</td>
            <td class="center">{{ $icic_kt_ly_2 }}</td>
            <td class="center">2.后台减少</td>
            <td class="center">{{ $icic_kt_qx_2 }}</td>
        </tr>
        <tr>
            {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
            {{--<td class="center">{{ $user->user_phone }}</td>--}}
            <td class="center">3.自己帐号从可提划转</td>
            <td class="center">{{ $icic_cn_ly_3 }}</td>
            <td class="center">3.后台减少</td>
            <td class="center">{{ $icic_cn_qx_3 }}</td>
            {{--<td class="center"></td>--}}
            <td class="center"></td>
            <td class="center"></td>
            <td class="center">3.提币到其他帐号</td>
            <td class="center">{{ $icic_kt_qx_3 }}</td>
        </tr>
        <tr>
            {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
            {{--<td class="center">{{ $user->user_phone }}</td>--}}
            <td class="center">4.区块转入</td>
            <td class="center">{{ $icic_cn_ly_4 }}</td>
            <td class="center"></td>
            <td class="center"></td>
            {{--<td class="center"></td>--}}
            <td class="center">未知来源（重点标记）</td>
            <td class="center">{{ $icic_kt_qx_1+$icic_kt_qx_2+$icic_kt_qx_3+$icic_kt_qx_4 +  $icic_wallet->wallet_withdraw_balance - ($icic_kt_ly_1+$icic_kt_ly_2) }}</td>
            <td class="center">4.划转到场内</td>
            <td class="center">{{ $icic_kt_qx_4 }}</td>
        </tr>
        <tr>
            {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
            {{--<td class="center">{{ $user->user_phone }}</td>--}}
            <td class="center">5.矿池释放</td>
            <td class="center">{{ $icic_cn_ly_5 }}</td>
            <td class="center"></td>
            <td class="center"></td>
            {{--<td class="center"></td>--}}
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
        </tr>
        <tr>
            {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
            {{--<td class="center">{{ $user->user_phone }}</td>--}}
            <td class="center">6.下级奖励</td>
            <td class="center">{{ $icic_cn_ly_6 }}</td>
            <td class="center"></td>
            <td class="center"></td>
            {{--<td class="center"></td>--}}
            <td class="center">总数量</td>
            <td class="center">{{ $icic_kt_ly_1+$icic_kt_ly_2  + ($icic_kt_qx_1+$icic_kt_qx_2+$icic_kt_qx_3+$icic_kt_qx_4 +  $icic_wallet->wallet_withdraw_balance - ($icic_kt_ly_1+$icic_kt_ly_2))}}</td>
            <td class="center">总数量</td>
            <td class="center">{{ $icic_kt_qx_1+$icic_kt_qx_2+$icic_kt_qx_3+$icic_kt_qx_4 - $icic_kt_freeze }}</td>
        </tr>
        <tr>
            {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
            {{--<td class="center">{{ $user->user_phone }}</td>--}}
            <td class="center">未知来源（比对计算）</td>
            <td class="center">{{ $icic_cn_qx_1 - $icic_cn_freeze - $icic_cn_chedan+$icic_cn_qx_2+$icic_cn_qx_3 + $icic_wallet->wallet_usable_balance+$icic_cn_freeze - ($icic_cn_ly_1+$icic_cn_ly_2+$icic_cn_ly_3+$icic_cn_ly_4+$icic_cn_ly_5+$icic_cn_ly_6) }}</td>
            <td class="center"></td>
            <td class="center"></td>
            {{--<td class="center"></td>--}}
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
        </tr>
        <tr>
            {{--<td class="center">{{ $user->userIdentify->identify_name }}</td>--}}
            {{--<td class="center">{{ $user->user_phone }}</td>--}}
            <td class="center">总数量</td>
            <td class="center">{{ $icic_cn_ly_1+$icic_cn_ly_2+$icic_cn_ly_3+$icic_cn_ly_4+$icic_cn_ly_5+$icic_cn_ly_6 + ($icic_cn_qx_1 - $icic_cn_freeze - $icic_cn_chedan+$icic_cn_qx_2+$icic_cn_qx_3 + $icic_wallet->wallet_usable_balance+$icic_cn_freeze - ($icic_cn_ly_1+$icic_cn_ly_2+$icic_cn_ly_3+$icic_cn_ly_4+$icic_cn_ly_5+$icic_cn_ly_6)) }}</td>
            <td class="center">总数量</td>
            <td class="center">{{ $icic_cn_qx_1 - $icic_cn_freeze - $icic_cn_chedan+$icic_cn_qx_2+$icic_cn_qx_3 }}</td>
            {{--<td class="center"></td>--}}
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
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
