@extends('admin.layouts.app')
@section('title', '场内交易信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                场内交易订单
            </li>
        </ul>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="position: absolute; top: 5.9em; right: 20em">
        <form class="form-search">
            <span>
                <input type="text" placeholder="发起人..." class="nav-search-input" name="username">
                <select class="nav-search-input" id="trade_type" name="trade_type">
                    <option value="">请选择</option>
                    <option value="0">买入</option>
                    <option value="1">卖出</option>
                </select>
                <select class="nav-search-input" id="base_coin_id" name="base_coin_id">
                    <option value="">选择交易底货币</option>
                    @foreach($coins as $coin)
                        <option value="{{ $coin->coin_id }}">{{ $coin->coin_name }}</option>
                    @endforeach
                </select>
                <select class="nav-search-input" id="exchange_coin_id" name="exchange_coin_id">
                    <option value="">选择需要兑换的货币</option>
                    @foreach($coins as $coin)
                        <option value="{{ $coin->coin_id }}">{{ $coin->coin_name }}</option>
                    @endforeach
                </select>
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="nav-search" id="nav-search" style="margin-top: 10px;">
        <form class="form-search">
            <span>
                <input type="text" placeholder="订单号..." class="nav-search-input" id="order_number" name="order_number" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            订单列表
        </h1>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">发起人</th>
            <th class="center">交易方</th>
            <th class="center">订单号</th>
            <th class="center">交易对</th>
            <th class="center">订单总数额</th>
            <th class="center">创建时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($insideTradeOrders as $insideTradeOrder)
            <tr>
                <td class="center">{{ $insideTradeOrder->order_id}}</td>
                <td class="center">{{ $insideTradeOrder->user->user_name }}</td>
                <td class="center">{{ $insideTradeOrder->tradeUser->user_name }}</td>
                <td class="center">
                    {{ $insideTradeOrder->order_number }}
                    <span class="label label-sm {{ $insideTradeOrder->trade_type ? 'label-danger' : 'label-success'}}">{{ $insideTradeOrder->trade_type ? '卖单' : '买单'}}</span>
                </td>
                <td class="center">
                    <span class="label label-xlg label-primary">
                        {{ $insideTradeOrder->getBaseCoin->coin_name . '/' . $insideTradeOrder->getExchangeCoin->coin_name }}
                    </span>
                </td>
                <td class="center">{{ $insideTradeOrder->want_trade_count + $insideTradeOrder->trade_no_num }}</td>
                <td class="center">{{ $insideTradeOrder->created_at }}</td>
                <td class="center">
                    <div>
                        <a href="{{ route('insideTradeorder.show', $insideTradeOrder->order_id) }}" class="btn btn-xs btn-info" title="查看订单详细">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $insideTradeOrders->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script>
        var where = {!! json_encode($where) !!};
        var  trade_type = {!! json_encode($trade_type) !!};
        $(function () {
            $("#base_coin_id").val(where.base_coin_id);
            $("#exchange_coin_id").val(where.exchange_coin_id);
            $("#trade_type").val(trade_type.trade_type);
        })
    </script>
@endsection
