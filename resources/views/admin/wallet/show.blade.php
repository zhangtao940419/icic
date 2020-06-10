@extends('admin.layouts.app')
@section('title', '用户钱包详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('userwallet.index') }}">用户钱包管理</a>
            </li>

            <li>
                用户钱包详细信息
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    @include('admin.layouts._message')
    {{--<table id="simple-table" class="table table-bordered table-hover" style="width:60%; margin-left:15em; font-size: 20px;">--}}
        {{--<tbody>--}}
        {{--<tr>--}}
            {{--<th class="center">ID</th>--}}
            {{--<th class="center">{{ $userwallet->user_id }}</th>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<th class="center">用户名</th>--}}
            {{--<th class="center">{{ $userwallet->user_name }}</th>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<th class="center">用户类型</th>--}}
            {{--<th class="center"><span class="label label-xlg {{ $userwallet->is_special_user ? 'label-danger' : ''}}">{{ $userwallet->is_special_user ? '特殊' : '普通'}}用户</span></th>--}}
        {{--</tr>--}}
        {{--@foreach($userwallet->userWallet as $v)--}}
            {{--<tr>--}}
                {{--<th class="center">钱包名</th>--}}
                {{--<th class="center">{{ !empty($v->wallet_account) ? $v->wallet_account : '暂无名称~_~'  }}</th>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<th class="center">货币名称</th>--}}
                {{--<th class="center">{{ $v->coin->coin_name }}</th>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<th class="center">余额</th>--}}
                {{--<th class="center">--}}
                    {{--{{ $v->wallet_usable_balance }}--}}
                    {{--<a href="{{ route('userwallet.edit', $v->wallet_id) }}" title="修改余额" class="btn btn-xs btn-danger">--}}
                        {{--<i class="ace-icon fa fa-pencil bigger-120"></i>--}}
                    {{--</a>--}}
                {{--</th>--}}
            {{--</tr>--}}
        {{--@endforeach--}}
        {{--<tr>--}}
            {{--<th class="center">创建时间</th>--}}
            {{--<th class="center">{{ $userwallet->created_at }}</th>--}}
        {{--</tr>--}}
        {{--</tbody>--}}
    {{--</table>--}}
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">会员</th>
            <th class="center">真实姓名</th>
            <th class="center">用户类型</th>
            <th class="center">货币名称</th>
            <th class="center">可交易余额</th>
            <th class="center">可提现余额</th>
            <th class="center">冻结数量</th>
            <th class="center">矿池余额</th>
            <th class="center">场内交易锁定余额</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($userwallet->userWallet as $v)
            <tr>
                <td class="center">{{ $userwallet->user_name }}</td>
                <td class="center">{{ $userwallet->userIdentify ? $userwallet->userIdentify->identify_name : '--' }}</td>
                <td class="center">
                    <span class="label label-sm {{ $userwallet->is_special_user ? 'label-danger' : ''}}">{{ $userwallet->is_special_user ? '特殊' : '普通'}}用户</span>
                </td>
                <td class="center">{{ $v->coin->coin_name }}</td>
                <td class="center">{{ $v->wallet_usable_balance }}</td>
                <td class="center">{{ $v->wallet_withdraw_balance }}</td>
                <td class="center">{{ $v->wallet_freeze_balance }}</td>
                <td class="center">{{ $v->ore_pool_balance }}</td>
                <td class="center">{{ $v->transfer_lock_balance }}</td>
                <td class="center">
                    <div>
                        <a style="margin-right: 2px;" href="{{ route('userwallet.edit', $v->wallet_id) }}" title="编辑用户钱包" class="btn btn-xs btn-danger">
                            <i class="ace-icon fa fa-edit bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection