@extends('admin.layouts.app')
@section('title', 'c2c交易信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                c2c订单信息
            </li>
        </ul>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="margin-top: 10px;">
        <form class="form-search6">
            <span>
                <select class="nav-search-input" id="status" name="status">
                    <option value="">请选择订单状态</option>
                    <option value="-1" @if(request('status')==-1) selected @endif>挂单待审核</option>
                    <option value="1" @if(request('status')==1) selected @endif>待商家接单</option>
                    <option value="2" @if(request('status')==2) selected @endif>已被商家接单,交易中</option>
                    <option value="3" @if(request('status')==3) selected @endif>交易完成</option>
                    <option value="0" @if(request('status')==='0') selected @endif>撤销</option>
                </select>
                <select class="nav-search-input" id="trade_type" name="trade_type">
                    <option value="">请选择买卖类型</option>
                    <option value="1">购买货币</option>
                    <option value="2">出售货币</option>
                </select>
                <input type="text" placeholder="用户名或电话..." class="nav-search-input" name="username" autocomplete="off">
                <input type="text" placeholder="订单号..." class="nav-search-input" id="nav-search-input" name="order_number" autocomplete="off">
                <div class="jeitem" style="display: inline-block">
                    <div class="jeinpbox">
                        <input type="text" class="jeinput nav-search-input" id="test04" name="begin_time" placeholder="开始时间">
                        格式:2016-10-06 10:00:00
                    </div>
                </div>
                <div class="jeitem" style="display: inline-block">
                    <div class="jeinpbox">
                        <input type="text" class="jeinput nav-search-input" id="test05" name="end_time" placeholder="结束时间">
                    </div>
                </div>
                <select name="order" id="order" class="nav-search-input">
                    <option value="">排序方式</option>
                    <option value="trade_number_desc">数量从高到低</option>
                    <option value="trade_number_asc">数量从低到高</option>
                </select>
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            c2c订单信息列表
        </h1>
    </div>
    <div class="space-4"></div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    @include('admin.layouts._error')
    <div>
        合计数量为:
        <h4 style="color: red; display: inline-block">
            {{ $sum }}
        </h4>
        <span class="label label-xlg label-primary" style="margin-left: 80%;cursor: pointer" onclick="window.location.href='{!! $excel !!}'">导出excel</span>
    </div>
    <table id="simple-table" class="table table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">交易ID</th>
            <th class="center">发起人(用户)</th>
            <th class="center">订单号</th>
            <th class="center">买卖类型</th>
            <th class="center">货币类型</th>
            <th class="center">数量</th>
            <th class="center">单价(CNY)</th>
            <th class="center">交易状态</th>
            <th class="center">时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($trades as $trade)
            <tr>
                <td class="center" >{{ $trade->trade_id}}</td>
                <td class="center">{{ $trade->userIdentify->identify_name . '('. $trade->userMsg->user_phone .')'}}<span style="color: #2A91D8">(累计买入:{{$trade->getUserTotalBuy()}}累计卖出:{{$trade->getUserTotalSell()}})</span>
                    @if($trade->checkUserStatus() == 1)<span class="label label-sm label-danger">异常</span>@endif
                    <br/>
                    (累计卖单数量:{!! ($num = $insideTradeSell->getUserAllSellSuccessNum($trade->userMsg->user_id)) > 3 ? '<i>'.$num.'</i>': '<i>'.$num.'</i>' !!}
                    今日卖单数量:{!! ($num = $insideTradeSell->getUserTodaySellSuccessNum($trade->userMsg->user_id)) > 3 ? '<i style="color: red">'.$num.'</i>': '<i>'.$num.'</i>' !!})
                </td>
                <td class="center">{{ $trade->trade_order }}</td>
                <td class="center">
                    <span class="label label-sm {{ $trade->trade_type == 1 ? 'label-success' : 'label-danger'}}">
                        {{ $trade->trade_type == 1 ? '购买货币' : '卖出货币'}}
                    </span>
                </td>
                <td class="center">{{ $trade->coin[0]->coin_name }}
                </td>
                <td class="center">{{ $trade->trade_number }}</td>
                <td class="center">{{ $trade->trade_price }}</td>
                <td class="center">{!! $trade->check_status!=0? $trade->getTradeStatus()[$trade->trade_status] : '<i style="color: #EE7621">挂单待审核</i>' !!}</td>
                <td class="center">{{ $trade->created_at }}</td>
                <td class="center">
                    <div>
                        @if($trade->check_status == 0)
                        <a class="label label-xlg label-success" href="/admin/c2cCheck?trade_id={{$trade->trade_id}}&status=1">通过</a>
                        <a class="label label-xlg label-danger" href="/admin/c2cCheck?trade_id={{$trade->trade_id}}&status=2">撤回</a>
                        @endif
                        <a href="{{ route('c2cmessage.show', $trade->trade_id) }}" class="btn btn-xs btn-info" title="查看交易详细">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $trades->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script src="/assets/js/jedate.js"></script>
    <script src="/assets/js/demo.js"></script>
    <script>
        var order = {!! json_encode($order) !!};
        var time = {!! json_encode($time) !!};
        $(function () {
            $('#test04').val(time[0]);
            $('#test05').val(time[1]);
            $("#order").val(order)
        });
    </script>
@endsection
