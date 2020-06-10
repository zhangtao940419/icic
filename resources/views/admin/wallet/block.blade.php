@extends('admin.layouts.app')
@section('title', '中心钱包')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li class="active">区块钱包</li>
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
                <input type="text" placeholder="会员手机号" class="nav-search-input" name="username" value="{{ request('username') }}">
                <select class="nav-search-input" id="coin" name="coin_id">
                    <option value="">请选择货币类型</option>
                    @foreach($coins as $coin)
                        <option value="{{ $coin->coin_id }}" @if(request('coin_id')==$coin->coin_id) selected @endif>{{ $coin->coin_name }}</option>
                    @endforeach
                </select>

                <button type="submit" style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>
    <div class="page-header">
        <h1>
            区块钱包
        </h1>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div>
        可交易的余额:
        <table id="simple-table" class="table  table-bordered table-hover">
            <thead>
            <tr>
                <th class="center">钱包id</th>
                <th class="center">所属会员</th>
                <th class="center">货币类型</th>
                <th class="center">地址</th>
                <th class="center">余额</th>
                {{--<th class="center">操作</th>--}}
            </tr>
            </thead>

            <tbody>
            @foreach($centerWallets as $value)
                <tr>
                    <td class="center">{{ $value->center_wallet_id }}</td>
                    <td class="center">中央钱包</td>
                    <td class="center">{{ $value->coin->coin_name }}</td>
                    <td class="center" ><a style="color: #2A91D8;" href="{{(new \App\Model\WalletDetail())->getExplorer($value->center_wallet_address,$value->coin->coin_name) }}" target="_blank">{{ $value->center_wallet_address }}</a></td>
                    <td class="center">{{ (new \App\Model\WalletDetail())->getBlockBalance($value->center_wallet_address,$value->coin->coin_name) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">钱包id</th>
            <th class="center">所属会员</th>
            <th class="center">真实姓名</th>
            <th class="center">货币类型</th>
            <th class="center">地址</th>
            <th class="center">余额</th>
            {{--<th class="center">操作</th>--}}
        </tr>
        </thead>

        <tbody>
        @foreach($wallets as $wallet)
            <tr>
                <td class="center">{{ $wallet->wallet_id }}</td>
                <td class="center">{{ $wallet->user->user_name }}</td>
                <td class="center">{{ $wallet->user->userIdentify ? $wallet->user->userIdentify->identify_name : '--' }}</td>
                <td class="center">{{ $wallet->coin->coin_name }}</td>
                <td class="center" ><a style="color: #2A91D8;" href="{{ $wallet->getExplorer($wallet->wallet_address,$wallet->coin->coin_name) }}" target="_blank">{{ $wallet->wallet_address }}</a></td>
                <td class="center">{{ $wallet->getBlockBalance($wallet->wallet_address,$wallet->coin->coin_name) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $wallets->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script>
        var order = '';
        $(function () {
            $("#order").val(order)
        })
    </script>
@endsection