@extends('admin.layouts.app')
@section('title', '提币审核列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                提币审核列表
            </li>
        </ul>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search pull-right" id="nav-search">
        <form class="form-search2">
            <span>
                {{--<input type="text" placeholder="提币订单id" style="width: 100px;" class="nav-search-input" name="order_id">--}}
                <input type="text" placeholder="地址" style="width: 25em;" class="nav-search-input" name="address">
                <input type="text" placeholder="会员名或电话号码..." class="nav-search-input" name="username">
                <select class="nav-search-input" id="order_type" name="order_type">
                        <option value="">请选择状态</option>
                        <option value="2">转入</option>
                        <option value="1">转出</option>
                </select>

                <select class="nav-search-input" id="status" name="order_check_status">
                        <option value="">请选择状态</option>
                        <option value="0">待审核</option>
                        <option value="1">通过审核</option>
                        <option value="2">拒绝</option>
                </select>

                <select class="nav-search-input" id="coin" name="coin_id">
                    <option value="">请选择货币类型</option>
                    @foreach($coins as $coin)
                        <option value="{{ $coin->coin_id }}">{{ $coin->coin_name }}</option>
                    @endforeach
               </select>
            </span>
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
            <select name="order" id="order" class="nav-search-input">
                <option value="">排序方式</option>
                <option value="order_trade_money_desc">提币数量从高到低</option>
                <option value="order_trade_money_asc">提币数量从低到高</option>
            </select>
            <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
        </form>
    </div>

    <div class="page-header">
        <h1>
            提币审核列表
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
            {{ $count }}
        </h4>
        <hr>
        @if($needShow == 2)

        历史转入此地址的互链账号数:
        <h4 style="color: red; display: inline-block">
            {{ $coinorder[0]->getHLRechargeAddressNum($address) }}
        </h4>
            @elseif($needShow == 1)
            历史转出到此互链地址的TTS账号数:
            <h4 style="color: red; display: inline-block">
                {{ $coinorder[0]->getTTSTOHLAddressNum($address) }}
            </h4>
            @endif
    </div>
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">提币ID</th>
            <th class="center">发起人</th>
            <th class="center">真实姓名</th>
            <th class="center">订单类型</th>
            <th class="center">货币类型</th>
            <th class="center">提币数量</th>

            <th class="center">订单状态</th>
            <th class="center">订单审核状态</th>
            <th class="center">路径</th>
            <th class="center">创建时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($coinorder as $value)
            <tr>
                <td class="center">{{ $value->order_id}}</td>
                <td class="center">{{ $value->user->user_name }}</td>
                <td class="center">{{ $value->user->userIdentity ? $value->user->userIdentify->identify_name : '--' }}</td>
                <td class="center">{{ $value->order_type == 1 ? '转出' : '转入' }}({{ $value->getType($value) }})</td>
                <td class="center">{{ $value->coinName->coin_name }}
                </td>
                <td class="center">{{ $value->order_trade_money }}</td>
                <td class="center">{{ $value->order_status ? '已被2个或2个以上区块网络节点接受确认' : '发起并记录' }}</td>
                <td class="center">{{ $value->getStatus()[$value->order_check_status] }}</td>
                <td class="center" style="width: 200px;">
                    出:<span style="color: green">{{ $value->order_trade_from }}</span><br>
                    进:<span style="color: red">{{ $value->order_trade_to }}</span>
                </td>
                <td class="center">{{ $value->created_at }}</td>
                <td class="center">
                    <div>
                        <a href="{{ route('coinorder.show', $value->order_id) }}" class="btn btn-xs btn-info" title="查看交易详细">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                        @if($value->order_type == 1 && $value->order_check_status == 0)
                        <button href="#" data-url="{{ route('checkWithdraw',['order_id' => $value->order_id, 'check_status' => 1]) }}" class="btn btn-xs btn-success seed" title="允许提币" disabled>
                            <i class="ace-icon glyphicon glyphicon-ok bigger-120"></i>
                        </button>
                        <button href="#" data-url="{{ route('checkWithdraw',['order_id' => $value->order_id, 'check_status' => 2]) }}" class="btn btn-xs btn-danger seed" title="拒绝提币" disabled>
                            <i class="ace-icon glyphicon glyphicon-remove bigger-120"></i>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $coinorder->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script src="/assets/js/jedate.js"></script>
    <script src="/assets/js/demo.js"></script>
    <script>
        var data = {!! json_encode($data) !!};
        var where = {!! json_encode($where) !!};
        var time = {!! json_encode($time) !!};
        var order = {!! json_encode($order) !!};

        $(function () {
            $(".seed").click(function () {
                var url = $(this).data('url');
                if (confirm('你确认该操作吗?')) {
                    location.href = url;
                }
            });

            $("#order").val(order)
            $('#coin').val(where.coin_id);
            $('#test05').val(time[1]);
            $('#test04').val(time[0]);
            $('#order_check_status').val(data.order_type);

            function removeDisabled() {
                $(".seed").removeAttr('disabled');
            }

            setInterval(removeDisabled, 2000);
        })
    </script>

@endsection
