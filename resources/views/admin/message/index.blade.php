@extends('admin.layouts.app')
@section('title', '交易信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                交易信息
            </li>
        </ul>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="position: absolute; top: 6.2em; right: 20em">
        <form class="form-search">
            <span>
                <input type="text" placeholder="发起人..." class="nav-search-input" name="username">
                <select class="nav-search-input" id="trade_type" name="trade_type">
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
            场外交易信息
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
            <th class="center">发起人</th>
            <th class="center">交易订单</th>
            <th class="center">货币类型</th>
            <th class="center">出售数量</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($outsideTrades as $outsideTrade)
            <tr>
                <td class="center">{{ $outsideTrade->trade_id}}</td>
                <td class="center">{{ $outsideTrade->getUserinfo->user_name }}
                    &nbsp;<span class="label label-sm {{ $outsideTrade->trade_type ? 'label-success' : 'label-danger'}}">{{ $outsideTrade->trade_type ? '买家' : '卖家'}}</span>
                </td>
                <td class="center">{{ $outsideTrade->order_number }}</td>
                <td class="center">{{ $outsideTrade->getCoin->coin_name }}
                </td>
                <td class="center">{{ $outsideTrade->trade_number }}</td>
                <td class="center">
                    <div>
                        <a href="{{ route('message.show', $outsideTrade->trade_id) }}" class="btn btn-xs btn-info" title="查看交易详细">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $outsideTrades->appends(\Request::except('page'))->render() }}
@endsection
