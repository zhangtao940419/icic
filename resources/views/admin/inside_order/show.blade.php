@extends('admin.layouts.app')
@section('title', '订单详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('insideTradeorder.index') }}">交易信息</a>
            </li>

            <li>
                订单详细信息
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
            <th class="center">{{ $insideTradeorder->order_id }}</th>
        </tr>
        <tr>
            <th class="center">发起人</th>
            <th class="center">{{ $insideTradeorder->user->user_name }}</th>
        </tr>
        <tr>
            <th class="center">交易人</th>
            <th class="center">{{ $insideTradeorder->tradeUser->user_name }}</th>
        </tr>
        <tr>
            <th class="center">交易类型</th>
            <th class="center">
                <span class="label label-xlg {{ $insideTradeorder->trade_type ? 'label-danger' : 'label-success'}}">
                    {{ $insideTradeorder->trade_type ? '卖单' : '买单'}}
                </span>
            </th>
        </tr>
        <tr>
            <th class="center">单价</th>
            <th class="center">{{ $insideTradeorder->unit_price }}</th>
        </tr>
        <tr>
            <th class="center">交易对</th>
            <th class="center">
                <span class="label label-xlg label-primary">
                    {{ $insideTradeorder->getBaseCoin->coin_name . '/' . $insideTradeorder->getExchangeCoin->coin_name }}
                </span>
            </th>
        </tr>
        <tr>
            <th class="center">总价</th>
            <th class="center">{{ $insideTradeorder->trade_total_money }}</th>
        </tr>
        <tr>
            <th class="center">当前剩余数额</th>
            <th class="center">{{ $insideTradeorder->trade_no_num }}个</th>
        </tr>
        <tr>
            <th class="center">总数量</th>
            <th class="center">{{ $insideTradeorder->trade_has_num + $insideTradeorder->trade_no_num }}个</th>
        </tr>
        <tr>
            <th class="center">交易手续费</th>
            <th class="center">{{ $insideTradeorder->trade_poundage }}</th>
        </tr>
        <tr>
            <th class="center">订单状态</th>
            <th class="center">{{ $insideTradeorder->trade_statu ? '已完成':'' }}</th>
        </tr>

        <tr>
            <th class="center">交易完成时间</th>
            <th class="center">{{ $insideTradeorder->created_at }}</th>
        </tr>
        </tbody>
    </table>

@endsection