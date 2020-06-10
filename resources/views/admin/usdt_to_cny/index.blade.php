@extends('admin.layouts.app')
@section('title', 'USDT换成CNY的汇率')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
               USDT转换为CNY的费率
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            挂单手续费
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            @if(@empty($rate['rate']))
                <a href="{{ route('usdt-cny.create') }}" class="btn btn-success">
                    <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                    新增
                </a>
            @endif

        </div>
        <!-- add reset e -->
    </div>

    <div>
        <h3>
            当前USDT换成CNY的费率:
                @if(!empty($rate['rate']))
                    {{ $rate['rate'] }}
                @else
                    请先创建usdt-cny的初始汇率!
                @endif
            <a href="{{ route('usdt-cny.create') }}" class="btn btn-sm   btn-info" title="操作">
                <i class="ace-icon fa fa-pencil bigger-120"></i>
            </a>
        </h3>
    </div>
@endsection