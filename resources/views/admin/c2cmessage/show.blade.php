@extends('admin.layouts.app')
@section('title', 'c2c交易详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('c2cmessage.index') }}">c2c订单信息</a>
            </li>

            <li>
                c2c订单详细信息
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    @include('admin.layouts._message')
    <table id="simple-table" class="table table-bordered table-hover" style="width:60%; margin-left:15em; font-size: 20px;">
        <tbody>
        <tr>
            <th class="center">ID</th>
            <th class="center">{{ $c2cmessage->trade_id }}</th>
        </tr>
        <tr>
            <th class="center">发起人</th>
            <th class="center">{{ $c2cmessage->userMsg->user_name }}</th>
        </tr>
        <tr>
            <th class="center">买卖类型</th>
            <th class="center"><span class="label label-xlg {{ $c2cmessage->trade_type == 1 ? 'label-success' : 'label-danger'}}">{{ $c2cmessage->trade_type == 1 ? '买入' : '卖出'}}货币</span></th>
        </tr>
        <tr>
            <th class="center">单价</th>
            <th class="center">{{ $c2cmessage->trade_price }}</th>
        </tr>
        <tr>
            <th class="center">订单号</th>
            <th class="center">{{ $c2cmessage->trade_order }}</th>
        </tr>
        <tr>
            <th class="center">需求货币类型</th>
            <th class="center">{{ $c2cmessage->coin[0]->coin_name }}</th>
        </tr>
        <tr>
            <th class="center">出售数量</th>
            <th class="center">{{ $c2cmessage->trade_number }}个</th>
        </tr>
        <tr>
            <th class="center">交易时的货币类型</th>
            <th class="center">{{ $c2cmessage->currency->currency_code }}</th>
        </tr>
        <tr>
            <th class="center">交易当前状态</th>
            <th class="center">{!! $c2cmessage->getTradeStatus()[$c2cmessage->trade_status] !!}</th>
        </tr>
        <tr>
            <th class="center">交易创建时间</th>
            <th class="center">{{ $c2cmessage->created_at->diffForHumans() }}</th>
        </tr>
        </tbody>
    </table>

@endsection
