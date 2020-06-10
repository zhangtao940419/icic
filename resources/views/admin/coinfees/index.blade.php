@extends('admin.layouts.app')
@section('title', '区块链费率')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>

            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            区块链费率及场内参数设定
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('coinfees.create') }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>

        </div>
        <!-- add reset e -->
    </div>


    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">兑换的货币</th>
            <th class="center">固定的费用</th>
            <th class="center">百分比的费用</th>
            <th class="center">场内交易费用模式</th>
            <th class="center">场内交易所需矿池余额</th>
            <th class="center">提币开关</th>
            <th class="center">充币开关</th>
            <th class="center">提币到Chat开关</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($rates as $rate)
            <tr>
                <td class="center">{{ $rate->id }}</td>
                <td class="center">{{ $rate->coin->coin_name }}</td>
                <td class="center">{{ $rate->fixed_fee }}</td>
                <td class="center">{{ $rate->percent_fee }}%</td>
                <td class="center">{{ $rate->fee_type == 1 ? '固定费用' : '百分比费用' }}</td>
                <td class="center">{{ $rate->ore_pool_min }}</td>
                <td class="center">{{ $rate->withdraw_on_off_status == 1 ? '开启' : '关闭' }}</td>
                <td class="center">{{ $rate->recharge_on_off_status == 1 ? '开启' : '关闭' }}</td>
                <td class="center">@if($rate->to_chat_switch == 1) 开启 @else 关闭 @endif
                    <a href="{{ route('toChatSwitch', $rate->id) }}" title="{{ $rate->to_chat_switch == 0 ? '开启' : '关闭'}}" class="btn btn-xs btn-{{ $rate->to_chat_switch == 0 ? 'success' : 'danger' }}">
                        <i class="ace-icon fa fa-{{ $rate->to_chat_switch == 0 ? 'unlock' : 'lock'}} bigger-120"></i>
                    </a>
                </td>
                <td class="center">
                    <div>
                        <a href="{{ route('coinfees.edit', $rate->id) }}" title="编辑" class="btn btn-xs btn-info">
                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                        </a>
                        <a href="{{ route('takeSwitch', $rate->id) }}" title="提币{{ $rate->withdraw_on_off_status == 1 ? '开启' : '关闭'}}" class="btn btn-xs btn-{{ $rate->withdraw_on_off_status == 1 ? 'success' : 'danger' }}">
                            <i class="ace-icon fa fa-{{ $rate->withdraw_on_off_status == 1 ? 'unlock' : 'lock'}} bigger-120"></i>
                        </a>
                        <a href="{{ route('putSwitch', $rate->id) }}" title="充币{{ $rate->recharge_on_off_status == 1 ? '开启' : '关闭'}}" class="btn btn-xs btn-{{ $rate->recharge_on_off_status == 1 ? 'success' : 'danger' }}">
                            <i class="ace-icon fa fa-{{ $rate->recharge_on_off_status == 1 ? 'unlock' : 'lock'}} bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $rates->render() }}
@endsection