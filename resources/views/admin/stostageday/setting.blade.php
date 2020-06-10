@extends('admin.layouts.app')
@section('title', 'sto设置')
@section('content')
<div class="breadcrumbs ace-save-state" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ url('/admin') }}">首页</a>
        </li>

        <li>
            sto设置
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
        sto设置
    </h1>
</div>

<form class="form-horizontal" role="form" method="post" action="{{ route('stoStageDay.update_setting',$id) }}">
    {{ csrf_field() }}
    <input type="hidden" name="type" value="1">
    <div class="form-group">

        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 进度100% </label>
        <div class="col-sm-9">
            <input type="radio" name="jd_switch" @if($jd_switch ==1) checked @endif value="1">开
            <input type="radio" name="jd_switch" @if($jd_switch ==0) checked @endif value="0">关
            <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
        </div>
    </div>
</form>
<hr>

<form class="form-horizontal" role="form" method="post" action="{{ route('stoStageDay.update_setting',$id) }}">
    {{ csrf_field() }}
    <input type="hidden" name="type" value="2">
    <div class="form-group">

        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 虚拟订单设置 </label>
        <div class="col-sm-9">
            数量:<input type="number" placeholder="虚拟订单数量" name="order_num" value="{{ $order_num }}">
            <input type="radio" name="xn_switch" @if($xn_switch ==1) checked @endif value="1">开
            <input type="radio" name="xn_switch" @if($xn_switch ==0) checked @endif value="0">关
            <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
        </div>
    </div>
</form>

<hr>

<form class="form-horizontal" role="form" method="post" action="{{ route('stoStageDay.update_setting',$id) }}">
    {{ csrf_field() }}
    <input type="hidden" name="type" value="3">
    <div class="form-group">

        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 是否开启限制STO内部用户购买 </label>
        <div class="col-sm-9">
            <input type="radio" name="special_user" @if($special_user ==1) checked @endif value="1">开
            <input type="radio" name="special_user" @if($special_user ==0) checked @endif value="0">关
            <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
        </div>
    </div>
</form>
@endsection