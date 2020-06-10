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

{{--<form class="form-horizontal" role="form" method="post" action="{{ route('sto.setting.update') }}">--}}
    {{--{{ csrf_field() }}--}}
    {{--<input type="hidden" name="type" value="1">--}}
    {{--<div class="form-group">--}}

        {{--<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 下级第一次购买奖励上级百分比 </label>--}}
        {{--<div class="col-sm-9">--}}
            {{--百分比:<input type="number" placeholder="虚拟订单数量" name="first_percent" value="{{ $first_percent }}">--}}
            {{--<button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</form>--}}
{{--<hr>--}}

{{--<form class="form-horizontal" role="form" method="post" action="{{ route('sto.setting.update') }}">--}}
    {{--{{ csrf_field() }}--}}
    {{--<input type="hidden" name="type" value="2">--}}
    {{--<div class="form-group">--}}

        {{--<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 下级非第一次购买奖励上级百分比 </label>--}}
        {{--<div class="col-sm-9">--}}
            {{--百分比:<input type="number" placeholder="虚拟订单数量" name="normal_percent" value="{{ $normal_percent }}">--}}
            {{--<button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</form>--}}

<hr>

<form class="form-horizontal" role="form" method="post" action="{{ route('sto.setting.update') }}">
    {{ csrf_field() }}
    <input type="hidden" name="type" value="3">
    <div class="form-group">

        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 会员单笔购买限额 </label>
        <div class="col-sm-9">
            单笔最低:<input type="number" placeholder="单笔最低" name="single_min" value="{{ $single_min }}">
            单笔最高:<input type="number" placeholder="单笔最高" name="single_max" value="{{ $single_max }}">
            <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
        </div>
    </div>
</form>
@endsection