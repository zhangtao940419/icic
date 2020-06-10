@extends('admin.layouts.app')
@section('title', '场内买单信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                场内买单信息
            </li>
        </ul>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="position: absolute; top: 5.9em; right: 20em">
        <form class="form-search">
            <span>
                <input type="text" placeholder="会员名..." class="nav-search-input" name="username">
                <select class="nav-search-input" id="trade_statu" name="trade_statu">
                    <option value="">交易状态</option>
                    <option value="0">撤销订单</option>
                    <option value="1">挂单状态</option>
                    <option value="2">已完成</option>
                </select>
                <select class="nav-search-input" id="price" name="price">
                    <option value="">价格排序</option>
                    <option value="1" @if(request('price') == 1) selected @endif>升序</option>
                    <option value="2" @if(request('price') == 2) selected @endif>降序</option>
                </select>
                <select class="nav-search-input" id="base_coin_id" name="base_coin_id">
                    <option value="">选择交易底货币</option>
                    @foreach($coins as $coin)
                        <option value="{{ $coin->coin_id }}" @if(request('base_coin_id')==$coin->coin_id) selected @endif>{{ $coin->coin_name }}</option>
                    @endforeach
                </select>
                <select class="nav-search-input" id="exchange_coin_id" name="exchange_coin_id">
                    <option value="">选择需要兑换的货币</option>
                    @foreach($coins as $coin)
                        <option value="{{ $coin->coin_id }}" @if(request('exchange_coin_id')==$coin->coin_id) selected @endif>{{ $coin->coin_name }}</option>
                    @endforeach
                </select>
                <div class="jeitem" style="display: inline-block">
                    <div class="jeinpbox">
                        <input type="text" class="jeinput nav-search-input" id="test04" name="begin_time" placeholder="开始时间">
                    </div>
                </div>
                <div class="jeitem" style="display: inline-block">
                    <div class="jeinpbox">
                        <input type="text" class="jeinput nav-search-input" id="test05" name="end_time" placeholder="结束时间">
                    </div>
                </div>
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
            场内买单信息
        </h1>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div>
        合计数量为:
        <h4 style="color: red; display: inline-block">
            {{ $wantSum }}
        </h4>
        &nbsp &nbsp已交易的数量:
        <h4 style="color: red; display: inline-block">
            {{ $transactionSum }}
        </h4>
        &nbsp &nbsp剩余的数量:
        <h4 style="color: red; display: inline-block">
            {{ $totalSum }}
        </h4>
    </div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">订单号</th>
            <th class="center">发起人</th>
            <th class="center">交易对</th>
            <th class="center">交易数额</th>
            <th class="center">价格</th>
            <th class="center">总价值</th>
            <th class="center">状态</th>
            <th class="center">创建时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($insideTrades as $insideTrade)
            <tr>
                <td class="center">{{ $insideTrade->buy_id}}</td>
                <td class="center">{{ $insideTrade->order_number}}</td>
                <td class="center">{{ $insideTrade->getUser->user_name }} ({{ $insideTrade->getUser->userIdentify ? $insideTrade->getUser->userIdentify->identify_name  : '--'}})</td>
                <td class="center">
                    <span class="label label-xlg label-primary">
                        {{ $insideTrade->getBaseCoin['coin_name'] . '/' . $insideTrade->getExchangeCoin['coin_name'] }}
                    </span>
                </td>
                <td class="center">{{ $insideTrade->want_trade_count }}@if($insideTrade->trade_statu!=2 && $insideTrade->hasTrade > 0) <br/><i style="color: red">已成交{{ $insideTrade->hasTrade }}</i> @endif</td>
                <td class="center">{{ $insideTrade->unit_price }}</td>
                <td class="center">{{ $insideTrade->unit_price * $insideTrade->want_trade_count . $insideTrade->getBaseCoin['coin_name'] }}</td>
                <td class="center">{{ $insideTrade->getStatus()[$insideTrade->trade_statu] }}@if($insideTrade->trade_statu!=2 && $insideTrade->hasTrade > 0) <br/><i style="color: red">部分完成</i> @endif</td>
                <td class="center">{{ $insideTrade->created_at }}</td>
                <td class="center">
                    <div>
                        <a href="{{ route('insideTradebuy.show', $insideTrade->buy_id) }}" class="btn btn-xs btn-info" title="查看交易详细">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $insideTrades->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script src="/assets/js/jedate.js"></script>
    <script src="/assets/js/demo.js"></script>
    <script>
        var where = {!! json_encode($where) !!};
        var  trade_statu = {!! json_encode($trade_statu) !!};
        var time = {!! json_encode($time) !!};
        $(function () {
            $('#test04').val(time[0]);
            $('#test05').val(time[1]);
            $("#base_coin_id").val(where.base_coin_id);
            $("#exchange_coin_id").val(where.exchange_coin_id);
            $("#trade_type").val(trade_type.trade_type);
            $("#trade_statu").val(trade_statu.trade_statu);
        })
    </script>
@endsection
