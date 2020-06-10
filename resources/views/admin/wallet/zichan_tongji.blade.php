@extends('admin.layouts.app')
@section('title', '用户资产统计')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                用户资产统计
            </li>
        </ul>
    </div>
        <!-- /.breadcrumb -->
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search">
        <form class="form-search">
            <span>
                <input type="text" placeholder="会员手机" class="nav-search-input" id="nav-search-input" name="user_phone" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            用户资产统计
        </h1>
    </div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">会员id</th>
            <th class="center">手机</th>
            <th class="center">关联的tts账号</th>
            <th class="center">对应的互链帐号及等级</th>
            <th class="center">互链所有帐号总买卖差(卖减买)</th>
            <th class="center">该帐号转出到互链的ICIC数量</th>
            <th class="center">该帐号接收从互链转入的ICIC数量</th>
            <th class="center">该帐号转入转出ICIC差额(入减出)</th>
            <th class="center">该帐号已买入USDT数量</th>
            <th class="center">该帐号已卖出USDT数量</th>
            <th class="center">该帐号买卖USDT差额(卖减买)</th>
            <th class="center">所有关联TTS帐号转出到互链的ICIC数量</th>
            <th class="center">所有关联TTS帐号接收到互链转入的ICIC数量</th>
            <th class="center">所有关联TTS帐号转入转出ICIC差额(入减出)</th>
            <th class="center">所有关联TTS帐号已买入USDT数量</th>
            <th class="center">所有关联TTS帐号已卖出USDT数量</th>
            <th class="center">所有关联TTS帐号买卖USDT差额(卖减买)</th>


        </tr>
        </thead>

        @if($user)
        <tbody>
            <tr>
                <td class="center">{{ $user->user_id }}</td>
                <td class="center">{{ $user->user_phone }}</td>
                <td class="center">@foreach($ttsLists as $ttsList)
                    {{ $ttsList->user_phone }}<hr/>
                @endforeach
                </td>
                <td class="center">@foreach($hlLists as $k=>$hlList)
                        {{ $hlList->mobile }}---A{{ $hlList->group_id }}--基本流水(充值ICIC：{{ $hlFlows[$k]['r'] }}  卖算力：{{ $hlFlows[$k]['s'] }})<hr/>
                    @endforeach
                </td>
                <td class="center">总买:{{ $hl_total_b }}总卖:{{ $hl_total_s }}/差:{{ $hl_total_s-$hl_total_b }}
                </td>
                <td class="center">{{ $total_icic_trans_to_hl }}</td>
                <td class="center">{{ $total_icic_trans_from_hl }}</td>
                <td class="center">{{ $total_r_icic-$total_w_icic }}</td>
                <td class="center">{{ $total_c2c_buy }}({{$total_c2c_buy*6.5}}元)</td>
                <td class="center">{{ $total_c2c_sell }}({{$total_c2c_sell*6.4}}元)</td>
                <td class="center">{{ $total_c2c_sell-$total_c2c_buy }}({{$total_c2c_sell*6.4 - $total_c2c_buy*6.5}}元)</td>
                <td class="center">{{ $relation_tts_out_to_hl }}</td>
                <td class="center">{{ $relation_tts_from_hl }}</td>
                <td class="center">{{ $relation_tts_recharge-$relation_tts_withdraw }}</td>
                <td class="center">{{ $relation_tts_c2c_buy }}</td>
                <td class="center">{{ $relation_tts_c2c_sell }}</td>
                <td class="center">{{ $relation_tts_c2c_sell - $relation_tts_c2c_buy }}({{ $relation_tts_c2c_sell*6.4 - $relation_tts_c2c_buy*6.5 }})</td>
            </tr>
        </tbody>
            @endif
    </table>
@endsection
