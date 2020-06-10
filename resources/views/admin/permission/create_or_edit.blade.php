@extends('admin.layouts.app')
@section('title', $permission->id ? '编辑权限' : '创建权限')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('permission.index') }}">权限列表</a>
            </li>
            @if($permission->id)
                <li class="active">编辑权限</li>
            @else
                <li class="active">添加权限</li>
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
            @if($permission->id)
                编辑权限
            @else
                创建权限
            @endif
        </h1>
    </div>
    @if($permission->id)
        <form class="form-horizontal" role="form" method="post" action="{{ route('permission.update', $permission->id) }}">
            <input type="hidden" name="_method" value="PUT">
            @else
                <form class="form-horizontal" role="form" method="post" action="{{ route('permission.store') }}">
                    @endif

                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 权限名称 </label>
                        <div class="col-sm-9">
                            <input type="text" name="name" id="form-field-1" placeholder="权限名称" class="col-xs-10 col-sm-5" value="{{ $permission->name }}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 路由名 </label>
                        <div class="col-sm-9">
                            <input type="text" name="route" id="form-field-1" placeholder="路由名" class="col-xs-10 col-sm-5" value="{{ $permission->route }}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 中文名 </label>
                        <div class="col-sm-9">
                            <input type="text" name="display_name" id="form-field-1" placeholder="中文名" class="col-xs-10 col-sm-5" value="{{ $permission->display_name }}" />
                        </div>
                    </div>

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