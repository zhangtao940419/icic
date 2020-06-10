@extends('admin.layouts.app')
@section('title', '用户钱包总览')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li class="active">用户钱包总览</li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入报错信息页面--}}
    @include('admin.layouts._error')
    <div class="nav-search pull-right" id="nav-search">
        <form class="form-search">
            <span>
                <input type="text" placeholder="会员名..." class="nav-search-input" name="username">
                <select class="nav-search-input" id="coin" name="coin_id">
                    <option value="">请选择货币类型</option>
                    @foreach($coins as $coin)
                        <option value="{{ $coin->coin_id }}">{{ $coin->coin_name }}</option>
                    @endforeach
                </select>
                {{--<select name="order" id="order" class="nav-search-input">--}}
                    {{--<option value="">排序方式</option>--}}
                    {{--<option value="wallet_usable_balance_desc">数量从高到低</option>--}}
                    {{--<option value="wallet_usable_balance_asc">数量从低到高</option>--}}
                {{--</select>--}}
                <select class="nav-search-input status" name="px" id="status">
                        <option value="">请选择排序方式</option>
                        <option value="1" @if(request('px')==1) selected @endif>可交易余额升序</option>
                        <option value="2" @if(request('px')==2) selected @endif>可交易余额降序</option>
                        <option value="3" @if(request('px')==3) selected @endif>可提现余额升序</option>
                        <option value="4" @if(request('px')==4) selected @endif>可提现余额降序</option>
                        <option value="5" @if(request('px')==5) selected @endif>冻结余额升序</option>
                        <option value="6" @if(request('px')==6) selected @endif>冻结余额降序</option>
                        <option value="7" @if(request('px')==7) selected @endif>矿池余额升序</option>
                        <option value="8" @if(request('px')==8) selected @endif>矿池余额降序</option>
                        <option value="9" @if(request('px')==9) selected @endif>场内交易锁定余额升序</option>
                        <option value="10" @if(request('px')==10) selected @endif>场内交易锁定余额降序</option>
                        </select>

                <button type="submit" style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>
    <div class="page-header">
        <h1>
            用户钱包总览
        </h1>

    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div>
        可交易的余额:
        <h4 style="color: red; display: inline-block">
            {{ $canChange }}
        </h4>
        可提现余额:
        <h4 style="color: red; display: inline-block">
            {{ $canPut }}
        </h4>
        冻结余额:
        <h4 style="color: red; display: inline-block">
            {{ $freeze }}
        </h4>
        <span class="label label-xlg label-primary" style="margin-left: 80%;cursor: pointer" onclick="window.location.href='{!! $excel !!}'">导出excel</span>
    </div>
    {{--<form class="form-search">--}}
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">钱包id</th>
            <th class="center">所属会员</th>
            <th class="center">真实姓名</th>
            <th class="center">货币类型</th>
            <th class="center">可交易余额({{ $canChange }})</th>
            <th class="center">可提现余额({{ $canPut }})</th>
            <th class="center">冻结余额({{ $freeze }})</th>
            <th class="center">矿池余额({{ $ore }})</th>
            <th class="center">场内交易锁定余额({{ $inside_lock }})</th>
            {{--<th class="center">操作</th>--}}
        </tr>
        </thead>

        <tbody>
        @foreach($walletDetails as $walletDetail)
            <tr>
                <td class="center">{{ $walletDetail->wallet_id }}</td>
                <td class="center">{{ $walletDetail->user->user_name }}</td>
                <td class="center">{{ $walletDetail->userIdentify ? $walletDetail->userIdentify->identify_name : '--' }}</td>
                <td class="center">{{ $walletDetail->coin->coin_name }}</td>
                <td class="center">{{ $walletDetail->wallet_usable_balance }}</td>
                <td class="center">{{ $walletDetail->wallet_withdraw_balance }}</td>
                <td class="center">{{ $walletDetail->wallet_freeze_balance }}</td>
                <td class="center">{{ $walletDetail->ore_pool_balance }}</td>
                <td class="center">{{ $walletDetail->transfer_lock_balance }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--</form>--}}
    {{ $walletDetails->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script>
        var order = {!! json_encode($order) !!};
        $(function () {
            $("#order").val(order)
        })
    </script>

    <script>
        $('.status').on('change', function () {
            $(".form-search").submit();
        })

        $('#area').on('change', function () {
            $(".form-search").submit();
        })

    </script>
@endsection