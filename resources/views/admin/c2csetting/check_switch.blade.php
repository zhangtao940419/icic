@extends('admin.layouts.app')
@section('title', '挂单审核设置')
@section('content')
<div class="breadcrumbs ace-save-state" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ url('/admin') }}">首页</a>
        </li>

        <li>
            挂单审核设置
        </li>
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
        挂单审核设置
    </h1>
</div>

<form class="form-horizontal" role="form" method="post" action="{{ route('c2c_check_switch.update') }}">
    {{ csrf_field() }}
    <input type="hidden" name="type" value="2">
    <div class="form-group">

        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 审核开关 </label>
        <div class="col-sm-9">
            <input type="radio" name="switch" @if($switch ==1) checked @endif value="1">开
            <input type="radio" name="switch" @if($switch ==0) checked @endif value="0">关
            <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
        </div>
        <div class="col-sm-9">
            卖出需审核的数量:<input type="number" name="low_number" value="{{ $num }}">
        </div><br/>
    </div>
</form>
<hr>

<form class="form-horizontal" role="form" method="post" action="{{ route('c2c_check_switch.update') }}">
    {{ csrf_field() }}
    <input type="hidden" name="type" value="1">
    <div class="form-group">

        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 审核开关 </label>
        <div class="col-sm-9">
            <input type="radio" name="switch" @if($c2CSetting['buy_order_check_switch'] ==1) checked @endif value="1">开
            <input type="radio" name="switch" @if($c2CSetting['buy_order_check_switch'] ==0) checked @endif value="0">关
            <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
        </div>
        <div class="col-sm-9">
            买入需审核的数量:<input type="number" name="low_number" value="{{ $c2CSetting['buy_order_need_check_num'] }}">
        </div><br/>
    </div>
</form>
@endsection