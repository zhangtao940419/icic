@extends('admin.layouts.app')
@section('title', 'c2c交易配置详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('c2csetting.index') }}">c2c交易配置</a>
            </li>

            <li>
                c2c交易配置详细信息
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
            <th class="center">{{ $c2csetting->id }}</th>
        </tr>
        <tr>
            <th class="center">货币类型</th>
            <th class="center">{{ $c2csetting->coin->coin_name }}</th>
        </tr>
        <tr>
            <th class="center">买单价格</th>
            <th class="center">{{$c2csetting->buy_price}}</th>
        </tr>
        <tr>
            <th class="center">卖单的价格</th>
            <th class="center">{{ $c2csetting->sell_price }}</th>
        </tr>
        <tr>
            <th class="center">用户同时挂买单单数量</th>
            <th class="center">{{ $c2csetting->user_buy_order_limit }}</th>
        </tr>
        <tr>
            <th class="center">用户同时挂卖单数量</th>
            <th class="center">{{ $c2csetting->user_sell_order_limit }}</th>
        </tr>
        <tr>
            <th class="center">用户挂买单单笔最小值</th>
            <th class="center">{{ $c2csetting->user_buy_num_min }}个</th>
        </tr>
        <tr>
            <th class="center">用户挂买单单笔最大值</th>
            <th class="center">{{ $c2csetting->user_buy_num_max }}个</th>
        </tr>
        <tr>
            <th class="center">用户挂卖单单笔最小值</th>
            <th class="center">{{ $c2csetting->user_sell_num_min }}</th>
        </tr>
        <tr>
            <th class="center">用户挂卖单单笔最大值</th>
            <th class="center">{{ $c2csetting->user_sell_num_max }}</th>
        </tr>
        <tr>
            <th class="center" style="color: red">用户单日挂卖单累计上限(0h-24h)</th>
            <th class="center" style="color: red">{{ $c2csetting->user_sell_day_max }}</th>
        </tr>
        <tr>
            <th class="center">商家同时能接的买单最大数量</th>
            <th class="center">{{ $c2csetting->business_buy_order_limit }}</th>
        </tr>
        <tr>
            <th class="center">商家同时能接卖单的最大数量</th>
            <th class="center">{{ $c2csetting->business_sell_order_limit }}</th>
        </tr>
        <tr>
            <th class="center">商家接买单的时间间隔(分钟)</th>
            <th class="center">{{ $c2csetting->business_buy_order_time_space }}</th>
        </tr>
        <tr>
            <th class="center">商家确认买单已收款的最低时间(分钟)</th>
            <th class="center">{{ $c2csetting->business_buy_order_confirm_time }}</th>
        </tr>
        <tr>
            <th class="center">商家确认卖单已付款的最低时间(分钟)</th>
            <th class="center">{{ $c2csetting->business_sell_order_confirm_time }}</th>
        </tr>
        <tr>
            <th class="center">商家接买单自动撤销时间(小时)</th>
            <th class="center">{{ $c2csetting->buy_order_auto_handle }}</th>
        </tr>

        </tbody>
    </table>

@endsection