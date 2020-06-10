@extends('admin.layouts.app')
@section('title', empty($rate) ? '创建场外挂单费率' : '修改场外挂单费率')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                初始化场外挂单费率
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
            @if(empty($rate))
                创建初始场外挂单汇率
            @else
                编辑初始场外挂单汇率
            @endif
        </h1>
    </div>

    <form class="form-horizontal" role="form" method="post" action="{{ route('outside-rate.create') }}">
        {{ csrf_field() }}
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 初始场外挂单汇率 </label>
            <div class="col-sm-9">
                @if(empty($outsiderate))
                    <input type="text" name="rate" id="form-field-1" placeholder="初始场外挂单汇率" class="col-xs-10 col-sm-5" />
                @else
                    <input type="text" name="rate" id="form-field-1" placeholder="初始场外挂单汇率" class="col-xs-10 col-sm-5" value="{{ $outsiderate['rate'] }}"/>
                @endif
                <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
            </div>
        </div>
    </form>
@endsection