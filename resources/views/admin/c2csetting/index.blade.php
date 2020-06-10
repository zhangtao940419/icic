@extends('admin.layouts.app')
@section('title', 'c2c交易设置')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>

            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            c2c交易设置
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('c2csetting.create') }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>

        </div>
        <!-- add reset e -->
    </div>


    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">交易货币</th>
            <th class="center">买单价格</th>
            <th class="center">卖单的价格</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($c2csetting as $value)
            <tr>
                <td class="center">{{ $value->id }}</td>
                <td class="center">{{ $value->coin->coin_name }}</td>
                <td class="center">{{ $value->buy_price }}</td>
                <td class="center">{{ $value->sell_price }}</td>
                <td class="center">
                    <div>
                        <a href="{{ route('c2csetting.show', $value->id) }}" class="btn btn-xs btn-info">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                        <a href="{{ route('c2csetting.edit', $value->id) }}" class="btn btn-xs btn-warning">
                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $c2csetting->render() }}
@endsection