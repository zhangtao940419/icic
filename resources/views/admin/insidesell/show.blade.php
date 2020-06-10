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
                <a href="{{ route('insideTradesell.index') }}">场内卖单详细信息</a>
            </li>

            <li>
                场内卖单详细信息
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    @include('admin.layouts._message')
    <h2 class="center">挂单信息</h2>
    <table id="simple-table" class="table table-bordered table-hover" style="width:60%; margin-left:15em; font-size: 20px;">
        <tbody>
        <tr>
            <th class="center">ID</th>
            <th class="center">{{ $insideTradesell->sell_id }}</th>
        </tr>
        <tr>
            <th class="center">交易单号</th>
            <th class="center">{{ $insideTradesell->order_number }}</th>
        </tr>
        <tr>
            <th class="center">发起人</th>
            <th class="center">{{ $insideTradesell->getUser->user_name }}</th>
        </tr>

        <tr>
            <th class="center">单价</th>
            <th class="center">{{ $insideTradesell->unit_price }}</th>
        </tr>
        <tr>
            <th class="center">交易对</th>
            <th class="center">
                <span class="label label-xlg label-primary">
                    {{ $insideTradesell->getBaseCoin->coin_name . '/' . $insideTradesell->getExchangeCoin->coin_name }}
                </span>
            </th>
        </tr>
        <tr>
            <th class="center">总数量</th>
            <th class="center">{{ $insideTradesell->want_trade_count }}个</th>
        </tr>
        <tr>
            <th class="center">总价值</th>
            <th class="center">{{ $insideTradesell->want_trade_count * $insideTradesell->unit_price . $insideTradesell->getBaseCoin->coin_name }}</th>
        </tr>
        <tr>
            <th class="center">当前剩余数额</th>
            <th class="center">{{ $insideTradesell->trade_total_num }}个</th>
        </tr>
        {{--<tr>--}}
            {{--<th class="center">交易总价</th>--}}
            {{--<th class="center">{{ $insideTradesell->trade_total_money }}</th>--}}
        {{--</tr>--}}
        <tr>
            <th class="center">交易进度</th>
            <th class="center">{{ $insideTradesell->getStatus()[$insideTradesell->trade_statu] }}</th>
        </tr>

        <tr>
            <th class="center">交易创建时间</th>
            <th class="center">{{ $insideTradesell->created_at }}</th>
        </tr>
        </tbody>
    </table>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <h2 class="center" id="show">交易详细订单</h2>
    <table id="simple-table" class="table table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">交易单号</th>
            <th class="center">交易方</th>
            <th class="center">交易数量</th>
            <th class="center">成交价格</th>
            <th class="center">价值</th>
            <th class="center">交易手续费</th>
            <th class="center">订单完成时间</th>
        </tr>
        </thead>

        <tbody>
        @foreach($insideTradesell->insideOrder as $order)
            <tr>
                <td class="center">{{ $order->order_id }}</td>
                <td class="center">{{ $order->sell_order_number }}</td>
                <td class="center">{{ $order->sellUser->user_name }}</td>
                <td class="center">{{ $order->trade_num }}</td>
                <td class="center">{{ $order->unit_price }}</td>
                <td class="center">{{ $order->unit_price * $order->trade_num . $insideTradesell->getBaseCoin->coin_name }}</td>
                <td class="center">{{ $order->trade_poundage }}</td>
                <td class="center">{{ $order->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
