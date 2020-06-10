@extends('admin.layouts.app')
@section('title', '提币详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('coinorder.index') }}">提币详细信息</a>
            </li>

            <li>
                提币详细信息
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    @include('admin.layouts._message')
    <table id="simple-table" class="table table-bordered table-hover" style="width:60%; margin-left:15em; font-size: 20px;">
        <tbody>
        <tr>
            <th class="center">ID</th>
            <th class="center">{{ $coinorder->order_id }}</th>
        </tr>
        <tr>
            <th class="center">发起人</th>
            <th class="center">{{ $coinorder->user->user_name }}</th>
        </tr>
        <tr>
            <th class="center">订单的类型</th>
            <th class="center">{{ $coinorder->order_type == 1 ? '转出' : '转入' }}</th>
        </tr>
        <tr>
            <th class="center">虚拟货币区块交易认证哈希值</th>
            <th class="center">{{ $coinorder->order_trade_hash }}</th>
        </tr>
        <tr>
            <th class="center">虚拟货币交易发起地址</th>
            <th class="center">{{ $coinorder->order_trade_from }}</th>
        </tr>
        <tr>
            <th class="center">虚拟货币交易接受地址</th>
            <th class="center">{{ $coinorder->order_trade_to }}</th>
        </tr>
        <tr>
            <th class="center">交易的金额</th>
            <th class="center">{{ $coinorder->order_trade_money }}个</th>
        </tr>
        <tr>
            <th class="center">交易的费用</th>
            <th class="center">{{ $coinorder->order_trade_fee }}个</th>
        </tr>
        <tr>
            <th class="center">虚拟货币类型名称</th>
            <th class="center">{{ $coinorder->coinName->coin_name }}</th>
        </tr>
        <tr>
            <th class="center">订单审核状态</th>
            <th class="center">{{ $coinorder->getStatus()[$coinorder->order_check_status] }}</th>
        </tr>
        <tr>
            <th class="center">订单状态</th>
            <th class="center">{{ $coinorder->order_status == 1 ? '已被2个或2个以上区块网络节点接受确认' : '发起并记录了记录' }}</th>
        </tr>
        <tr>
            <th class="center">交易创建时间</th>
            <th class="center">{{ $coinorder->created_at->diffForHumans() }}</th>
        </tr>
        </tbody>
    </table>

@endsection