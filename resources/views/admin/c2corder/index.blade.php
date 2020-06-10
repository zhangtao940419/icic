@extends('admin.layouts.app')
@section('title', 'c2c订单信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                c2c交易信息
            </li>
        </ul>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="position: absolute; right: 18em; top: 5.9em;">
        <form class="form-search2">
            <span>
                <select class="nav-search-input" name="order_status" id="order_status">
                    <option value="">请选择订单状态</option>
                    <option value="0">商家撤单</option>
                    <option value="1">商家拍下订单,待确认</option>
                    <option value="2">商家已确认,待审核</option>
                    <option value="3">后台确认完毕,交易完成</option>
                    <option value="4">超时自动撤单</option>
                </select>
                <select class="nav-search-input" id="trade_type" name="trade_type">
                    <option value="">请选择订单状态</option>
                    <option value="1">购买货币</option>
                    <option value="2">出售货币</option>
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
                <input type="text" placeholder="买/卖方或电话..." class="nav-search-input" id="username" name="username" autocomplete="off">
                <input type="text" placeholder="交易方或电话..." class="nav-search-input" id="busername" name="business_username" autocomplete="off">
                <select name="order" id="order" class="nav-search-input">
                    <option value="">排序方式</option>
                    <option value="trade_number_desc">数量从高到低</option>
                    <option value="trade_number_asc">数量从低到高</option>
                </select>
                <button type="submit" style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="nav-search" id="nav-search" style="margin-top: 10px;">
        <form class="form-search">
            <span>
                <input type="text" placeholder="订单号..." class="nav-search-input" id="nav-search-input" name="order_number" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            c2c交易列表
        </h1>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div>
        合计数量为:
        <h4 style="color: red; display: inline-block">
            {{ $sum }}
        </h4>
        <span class="label label-xlg label-primary" style="margin-left: 80%;cursor: pointer" onclick="window.location.href='{!! $excel !!}'">导出excel</span>
    </div>

    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">订单ID</th>
            <th class="center" style="color: green;">买/卖名<span class="label label-sm label-success">用户</span></th>
            <th class="center" style="color: red">交易方<span class="label label-sm label-danger">商家</span></th>
            <th class="center">交易号</th>
            <th class="center">买卖类型</th>
            <th class="center">货币类型</th>
            <th class="center">交易数量</th>
            <th class="center">单价</th>
            <th class="center">价值</th>
            <th class="center">订单状态</th>
            <th class="center">时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($orders as $order)
            <tr>
                <td class="center">{{ $order->order_id}}</td>
                <td class="center">
                    {{ $order->tradeMsg->userIdentify->identify_name . '('. $order->tradeMsg->userMsg->user_name .')' }}
                    <span class="label label-sm {{ $order->tradeMsg->trade_type == 1 ? 'label-success' : 'label-danger'}}">
                        {{ $order->tradeMsg->trade_type == 1 ? '买方' : '卖方'}}
                    </span>
                </td>
                <td class="center">
                    {{ $order->user->userIdentify->identify_name . '('. $order->user->user_name .')' }}
                    <span class="label label-sm {{ $order->tradeMsg->trade_type == 1 ? 'label-danger' : 'label-success'}}">
                        {{ $order->tradeMsg->trade_type == 1 ? '卖方' : '买方'}}
                    </span>
                </td>
                <td class="center">{{ $order->order_number }}</td>
                <td class="center">
                    <span class="label label-sm {{ $order->tradeMsg->trade_type == 1 ? 'label-success' : 'label-danger'}}">
                        {{ $order->tradeMsg->trade_type == 1 ? '用户购买货币' : '用户卖出货币'}}
                    </span>
                </td>
                <td class="center">{{ $order->tradeMsg->coin[0]->coin_name }}
                </td>
                <td class="center">{{ $order->tradeMsg->trade_number }}</td>
                <td class="center">{{ $order->tradeMsg->trade_price }}</td>
                <td class="center">{{ $order->tradeMsg->trade_price * $order->tradeMsg->trade_number . 'cny' }}</td>
                <td class="center">{!! $order->getOrderStatus()[$order->order_status] !!}</td>
                <td class="center">{{ $order->created_at }}</td>
                <td class="center">
                    <div>
                        @if($order->order_status == 2)
                        <a href="#" data-url="{{ route('checkTransferImg', ['order_id' => $order->order_id, 'check_status' => 1]) }}" class="btn btn-xs btn-success seed" title="审核通过">
                            <i class="ace-icon glyphicon glyphicon-ok bigger-120"></i>
                        </a>
                        <a href="#" data-url="{{ route('checkTransferImg', ['order_id' => $order->order_id, 'check_status' => 2]) }}" class="btn btn-xs btn-danger seed" title="审核驳回">
                            <i class="ace-icon glyphicon glyphicon-remove bigger-120"></i>
                        </a>
                        @endif
                        <a href="{{ route('c2corder.show', $order->order_id) }}" class="btn btn-xs btn-info" title="查看交易详细">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $orders->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script src="/assets/js/jedate.js"></script>
    <script src="/assets/js/demo.js"></script>
    <script>


        function GetQueryString(name)
        {
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);//search,查询？后面的参数，并匹配正则
            if(r!=null)return  unescape(r[2]).replace('+',' '); return '';
        }


        // $('#test04').val(time[0]);
        var time1 = GetQueryString('begin_time')
        $('#test04').val(time1);

        // $('#test05').val(time[1]);
        var time2 = GetQueryString('end_time')
        $('#test05').val(time2);

        $('#order_status').val(GetQueryString('order_status'));
        $('#trade_type').val(GetQueryString('trade_type'));
        $('#busername').val(GetQueryString('business_username'));


        var time = {!! json_encode($time) !!};
        var order = {!! json_encode($c2corder) !!};
        $(function () {
            // console.log(order.trade_number);
            $(".seed").click(function () {
                var url = $(this).data('url');
                if (confirm('你确认该操作吗?')) {
                    location.href = url;
                }
            });

            // $('#test04').val(time[0]);
            // var time1 = GetQueryString('begin_time')
            // $('#test04').val(time1);

            // $('#test05').val(time[1]);
            // var time2 = GetQueryString('end_time')
            // $('#test05').val(time2);

            $('#order').val(order);
        });
    </script>
@endsection
