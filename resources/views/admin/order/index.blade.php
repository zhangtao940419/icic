@extends('admin.layouts.app')
@section('title', '已完成的交易')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                已完成的交易信息
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="position: absolute; top: 6.2em; right: 20em">
        <form class="form-search">
            <span>
                <input type="text" placeholder="发起人..." class="nav-search-input" name="username">
                <select class="nav-search-input" name="order_type">
                    <option value="">请选择</option>
                    <option value="1">买单</option>
                    <option value="0">卖单</option>
                </select>
                <select class="nav-search-input" name="coin_id">
                    <option value="">选择货币类型</option>
                    @foreach($coins as $coin)
                        <option value="{{ $coin->coin_id }}">{{ $coin->coin_name }}</option>
                    @endforeach
                </select>
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search">
        <form class="form-search">
            <span>
                <input type="text" placeholder="订单号..." class="nav-search-input" id="nav-search-input" name="order_number" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            场外订单
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
            <th class="center">发起方</th>
            <th class="center">交易方</th>
            <th class="center">交易订单</th>
            <th class="center">货币类型</th>
            <th class="center">出售数量</th>
            <th class="center">订单状态</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($orders as $order)
            <tr>
                <td class="center">{{ $order->order_id}}</td>
                <td class="center">{{ $order->getUserInfo->user_name }}
                    &nbsp;<span class="label label-sm {{ $order->order_type ? 'label-success' : 'label-danger'}}">{{ $order->order_type ? '买家' : '卖家'}}</span>
                </td>
                <td class="center">{{ $order->getOrderInfo->user_name }}
                    &nbsp;<span class="label label-sm {{ $order->order_type ? 'label-danger' : 'label-success'}}">{{ $order->order_type ? '卖家' : '买家'}}</span>
                </td>
                <td class="center">{{ $order->order_number }}</td>
                <td class="center">{{ $order->getCoin->coin_name }}</td>
                <td class="center">{{ $order->trade_coin_num }}</td>
                <td class="center">{{ $order->getOrderStatus()[$order->trade_statu] }}</td>
                <td class="center">
                    <div>
                        @if($order->trade_statu == 2)
                            <a href="#" data-url="{{ route('seedGoods', ['order_number' => $order->order_number, 'order_number' => $order->order_number]) }}" class="btn btn-xs btn-success seed" title="强制发货">
                                <i class="ace-icon glyphicon glyphicon-ok bigger-120"></i>
                            </a>
                        @endif
                        @if($order->trade_statu == 1)
                        <a href="#" data-url="{{ route('cancelOrder', ['order_number' => $order->order_number, 'order_number' => $order->order_number]) }}" class="btn btn-xs btn-danger seed" title="强制取消订单">
                            <i class="ace-icon glyphicon glyphicon-remove bigger-120"></i>
                        </a>
                        @endif
                        <a href="{{ route('order.show', $order->order_id) }}" class="btn btn-xs btn-info" title="查看交易详细">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                        {{--<a href="{{ route('showMsg', $order->order_id) }}" class="btn btn-xs btn-inverse" id="showmsg" title="查看聊天记录">--}}
                            {{--<i class="ace-icon fa fa-comments bigger-120"></i>--}}
                        {{--</a>--}}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $orders->appends(\Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script>
        $(".seed").click(function () {
            var url = $(this).data('url');
            if (confirm('你确认执行该操作?')) {
                location.href=url;
            }
        })

        // $("#showmsg").click(function () {
        //     var user_id1 = $(this).attr('user_id1');
        //     var user_id2 = $(this).attr('user_id2');
        // })
    </script>
@endsection
