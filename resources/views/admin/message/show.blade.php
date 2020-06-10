@extends('admin.layouts.app')
@section('title', '交易详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('message.index') }}">交易信息</a>
            </li>

            <li>
                交易详细信息
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
            <th class="center">{{ $outsideTrade->trade_id }}</th>
        </tr>
        <tr>
            <th class="center">发起人</th>
            <th class="center">{{ $outsideTrade->getUserInfo->user_name }}</th>
        </tr>
        <tr>
            <th class="center">买卖类型</th>
            <th class="center"><span class="label label-xlg {{ $outsideTrade->trade_type ? 'label-success' : 'label-danger'}}">{{ $outsideTrade->trade_type ? '买' : '卖'}}货币</span></th>
        </tr>
        <tr>
            <th class="center">价格</th>
            <th class="center">{{ $outsideTrade->trade_ideality_price }}</th>
        </tr>
        <tr>
            <th class="center">交易订单</th>
            <th class="center">{{ $outsideTrade->order_number }}</th>
        </tr>
        <tr>
            <th class="center">货币类型</th>
            <th class="center">{{ $outsideTrade->getCoin->coin_name }}</th>
        </tr>
        <tr>
            <th class="center">出售数量</th>
            <th class="center">{{ $outsideTrade->trade_number }}个</th>
        </tr>
        <tr>
            <th class="center">最小交易数量</th>
            <th class="center">{{ $outsideTrade->trade_number }}个</th>
        </tr>
        <tr>
            <th class="center">地区</th>
            <th class="center">{{ $outsideTrade->location_id }}</th>
        </tr>
        <tr>
            <th class="center">定价方式</th>
            <th class="center">{{ $outsideTrade->trade_price_type ? '自定义价格' : '溢价出售' }}</th>
        </tr>
        <tr>
            <th class="center">收付类型</th>
            <th class="center">{{ $outsideTrade->get_money_type }}</th>
        </tr>
        <tr>
            <th class="center">交易单状态</th>
            <th class="center">{{ $outsideTrade->getTradeStatus()[$outsideTrade->trade_statu] }}</th>
        </tr>
        <tr>
            <th class="center">场外交易描述</th>
            <th class="center">{{ $outsideTrade->trade_des }}</th>
        </tr>
        <tr>
            <th class="center">交易创建时间</th>
            <th class="center">{{ $outsideTrade->created_at->diffForHumans() }}</th>
        </tr>
        </tbody>
    </table>

@endsection
