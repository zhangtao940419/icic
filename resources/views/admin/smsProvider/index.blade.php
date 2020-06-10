@extends('admin.layouts.app')
@section('title', '修改短信接口')
@section('content')
<div class="breadcrumbs ace-save-state" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ url('/admin') }}">首页</a>
        </li>

        <li>
            修改短信接口
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
        修改短信接口
    </h1>
</div>

<form class="form-horizontal" role="form" method="post" action="{{ route('smsProvider.update') }}">
    {{ csrf_field() }}
    <div class="form-group">
        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 修改短信接口 </label>
        <div class="col-sm-9">
            <input type="radio" name="sms_provider" @if($provider ==1) checked @endif value="1">创蓝
            <input type="radio" name="sms_provider" @if($provider ==2) checked @endif value="2">短信宝
            <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
        </div>
    </div>
</form>
@endsection