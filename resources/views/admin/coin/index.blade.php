@extends('admin.layouts.app')
@section('title', '货币列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                货币列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            虚拟货币列表
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('coinType.create') }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>

        </div>
        <!-- add reset e -->
    </div>

    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">id</th>
            <th class="center">名称</th>
            <th class="center">图标</th>
            <th class="center">使用限制</th>
            <th class="center">状态</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($currencies as $currency)
            <tr>
                <td class="center" style="line-height: 60px;">{{ $currency->coin_id }}</td>
                <td class="center" style="line-height: 60px;">{{ $currency->coin_name }}</td>
                <td class="center"><img src="{{ $currency->coin_icon }}" width="60px" height=60px"></td>
                <td class="center" style="line-height: 60px;">
                    <span class="label label-sm {{ $currency->is_outside ? 'label-success' : 'label-danger'}}">{{ $currency->is_outside ? '允许场外使用' : '不允许场外使用'}}</span>
                </td>
                <td class="center" style="line-height: 60px;">{{ $currency->is_usable ? '正在使用' : '已废弃' }}</td>
                <td class="center" style="line-height: 60px;">
                    <div>
                        <a href="{{ route('coinType.edit',  $currency->coin_id) }}" class="btn btn-xs btn-warning" title="编辑">
                            <i class="ace-icon glyphicon glyphicon-pencil"></i>
                        </a>
                        <a href="{{ route('coinType.open',  $currency->coin_id) }}" class="btn btn-xs btn-{{ $currency->is_outside ? 'success' : 'danger' }}" title="开启或关闭">
                            <i class="ace-icon fa fa-{{ $currency->is_outside ? 'unlock' : 'lock' }} bigger-120"></i>
                        </a>
                        <a href="{{ route('coinType.show',  $currency->coin_id) }}" class="btn btn-xs btn-info" title="查看交易汇率">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $currencies->render() }}
@endsection