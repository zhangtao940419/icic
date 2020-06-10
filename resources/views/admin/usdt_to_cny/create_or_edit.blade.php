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
                初始化USDT转换为CNY的费率
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入报错信息页面--}}
    @include('admin.layouts._error')
    @include('admin.layouts._message')
    <div class="page-header">
        <h1>
            @if(empty($rate))
                创建初始汇率
            @else
                编辑初始汇率
            @endif
        </h1>
    </div>

        <form class="form-horizontal" role="form" method="post" action="{{ route('usdt-cny.create') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 初始汇率 </label>
                <div class="col-sm-9">
                    @if(empty($rate))
                        <input type="text" name="rate" id="form-field-1" placeholder="初始汇率" class="col-xs-10 col-sm-5" />
                    @else
                        <input type="text" name="rate" id="form-field-1" placeholder="初始汇率" class="col-xs-10 col-sm-5" value="{{ $rate['rate'] }}"/>
                    @endif
                    <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
                </div>
            </div>
        </form>
@endsection