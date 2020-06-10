@extends('admin.layouts.app')
@section('title',  '查看历史记录')
@section('myCss')
    <link rel="stylesheet" href="/assets/css/laydate.css">
@endsection
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('order.index') }}">场外订单列表</a>
            </li>
            <li class="active">查看历史记录</li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入报错信息页面--}}
    @include('admin.layouts._error')
    <div class="page-header">
        <h1>
          查看历史记录
        </h1>
    </div>
        <form class="form-horizontal" role="form" method="post" action="{{ route('postMsg') }}">

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 发起方 </label>
                <div class="col-sm-9">
                    <select type="text" disabled name="user_id1" id="user_id1" class="col-xs-10 col-sm-5" >
                        <option value="{{ $order->user_id }}">{{ $order->getUserInfo->user_name }}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 交易方 </label>
                <div class="col-sm-9">
                    <select type="text" disabled name="user_id2" id="user_id2" class="col-xs-10 col-sm-5" >
                        <option value="{{ $order->trade_user_id }}">{{ $order->getOrderInfo->user_name }}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 显示条数 </label>
                <div class="col-sm-9">
                    <input type="text" name="count" id="count" placeholder="显示条数" class="col-xs-10 col-sm-5" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 开始时间 </label>
                <div class="col-sm-9">
                    <input type="text" name="begin" id="begin" placeholder="开始时间" class="col-xs-10 col-sm-5 demo-input" />
                </div>
            </div>

            <div class="space-4"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 结束时间 </label>
                <div class="col-sm-9">
                    <input type="text" name="end" id="end" placeholder="结束时间" class="col-xs-10 col-sm-5 demo-input" />
                </div>
            </div>

            <div class="space-4"></div>

            <div class="clearfix form-actions">
                <div class="col-md-offset-3 col-md-9">
                    <button type="button" id="seed" class="btn btn-info">
                        <i class="ace-icon fa fa-check bigger-110"></i>
                        提交
                    </button>

                    &nbsp; &nbsp; &nbsp;
                    <button class="btn" type="reset">
                        <i class="ace-icon fa fa-undo bigger-110"></i>
                        重置
                    </button>
                </div>
            </div>
        </form>
@endsection
@section('myJs')
    <script>
        $("#seed").click(function () {
            var data = ({
                user_id1 : $('#user_id1').val(),
                user_id2 : $('#user_id2').val(),
                count : $('#count').val(),
                // begin : new Date($('#begin').val()).getTime(),
                // end : new Date($('#end').val()).getTime(),
                begin : $('#begin').val(),
                end : $('#end').val(),
            });
            console.log(new Date($('#begin').val()).valueOf());
            console.log(new Date($('#end').val()).valueOf());

            axios.post("{{ route('postMsg') }}", data)
                .then(function (data) {
                    console.log(data);
                });
        })
    </script>
@endsection