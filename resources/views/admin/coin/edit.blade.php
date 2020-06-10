@extends('admin.layouts.app')
@section('title',  $coinType->coin_id ? '编辑货币' : '创建货币' )
@section('content')

    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('coinType.index') }}">货币列表</a>
            </li>
            @if($coinType->coin_id)
                <li class="active">编辑货币</li>
            @else
                <li class="active">添加货币类型</li>
            @endif
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
            @if($coinType->coin_id)
                编辑货币
            @else
                创建货币
            @endif
        </h1>
    </div>
        @if($coinType->coin_id)
            <form class="form-horizontal" role="form" method="post" action="{{ route('coinType.update', $coinType->coin_id) }}">
                <input type="hidden" name="_method" value="PUT">
            @else
            <form class="form-horizontal" role="form" method="post" action="{{ route('coinType.store') }}">
        @endif
                {{ csrf_field() }}
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 货币名称 </label>
                    <div class="col-sm-9">
                        <input type="text" name="coin_name" id="form-field-1" placeholder="货币名称" class="col-xs-10 col-sm-5" value="{{ $coinType->coin_name }}" />
                    </div>
                </div>

                <div class="space-4"></div>

                @if($coinType->coin_id)
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 货币状态 </label>
                        <div class="col-sm-9">
                            <select type="text" name="is_usable" class="col-xs-10 col-sm-5">
                                <option selected value="{{ $coinType->is_usable }}">{{ $coinType->is_usable ? '使用' : '已废弃' }}</option>
                                <option value="0">废弃</option>
                                <option value="1">显示</option>
                            </select>
                        </div>
                    </div>
                @endif

                <div class="space-4"></div>

                <div class="space-4"></div>

                <div class="space-4"></div>

                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-info">
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            提交
                        </button>

                        &nbsp; &nbsp; &nbsp;
                        <button class="btn" type="reset">
                            <i class="ace-icon fa fa-undo bigger-110"></i>
                            重置
                        </button>
                    </div>
                </div>
            </form>
@endsection