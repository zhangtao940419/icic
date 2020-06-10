@extends('admin.layouts.app')
@section('title', '完成订单详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('order.index') }}">完成订单详细信息</a>
            </li>

            <li>
                完成订单详细信息
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
            <th class="center">{{ $order->order_id }}</th>
        </tr>
        <tr>
            <th class="center">发起方</th>
            <th class="center">{{ $order->getUserInfo->user_name }}
                <span class="label label-sm {{ $order->trade_type ? 'label-success' : 'label-danger'}}">{{ $order->trade_type ? '买家' : '卖家'}}</span>
            </th>
        </tr>
        <tr>
            <th class="center">交易方</th>
            <th class="center">{{ $order->getOrderInfo->user_name }}
                <span class="label label-sm {{ $order->trade_type ? 'label-danger' : 'label-success'}}">{{ $order->trade_type ? '卖家' : '买家'}}</span>
            </th>
        </tr>
        <tr>
            <th class="center">买卖类型</th>
            <th class="center"><span class="label label-xlg {{ $order->trade_type ? 'label-success' : 'label-danger'}}">{{ $order->trade_type ? '买' : '卖'}}货币</span></th>
        </tr>
        <tr>
            <th class="center">价格</th>
            <th class="center">{{ $order->trade_total_money }}</th>
        </tr>
        <tr>
            <th class="center">交易订单</th>
            <th class="center">{{ $order->order_number }}</th>
        </tr>
        <tr>
            <th class="center">货币类型</th>
            <th class="center">{{ $order->getCoin->coin_name }}</th>
        </tr>
        <tr>
            <th class="center">出售数量</th>
            <th class="center">{{ $order->trade_coin_num }}个</th>
        </tr>
        <tr>
            <th class="center">订单状态</th>
            <th class="center">{{ $order->getOrderStatus()[$order->trade_statu] }}</th>
        </tr>
        <tr>
            <th class="center">订单创建时间</th>
            <th class="center">{{ $order->created_at->diffForHumans() }}</th>
        </tr>
        </tbody>
    </table>

@endsection
