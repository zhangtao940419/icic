@extends('admin.layouts.app')
@section('title',  $category->id ? '编辑分类' : '创建分类' )
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('category.index') }}">分类列表</a>
            </li>
            @if($category->id)
                <li class="active">编辑分类</li>
            @else
                <li class="active">添加分类</li>
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
            @if($category->id)
                编辑分类
                    @else
                创建分类
            @endif
        </h1>
    </div>
    @if($category->id)
        <form class="form-horizontal" role="form" method="post" action="{{ route('category.update', $category->id) }}">
            <input type="hidden" name="_method" value="PUT">
    @else
        <form class="form-horizontal" role="form" method="post" action="{{ route('category.store') }}">
    @endif

        {{ csrf_field() }}
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 分类标题 </label>
            <div class="col-sm-9">
                <input type="text" name="name" id="form-field-1" placeholder="分类标题" class="col-xs-10 col-sm-5" value="{{ $category->name }}" />
            </div>
        </div>

        <div class="space-4"></div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 分类等级 </label>

            <div class="col-sm-9">
                <select id="form-field-2" class="col-xs-10 col-sm-5" name="parents_id">
                    <option value="0">顶级分类</option>
                    @foreach($categories as $value)
                        <option value="{{ $value->id }}" {{ $value->id == $category->parents_id ? 'selected' : '' }}>{{ str_repeat('--', $value->leve) }} {{ $value->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="space-4"></div>

        <div class="space-4"></div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-4">分类描述</label>

            <div class="col-sm-9">
                <textarea name="description" rows="3" cols="50">{{ $category->description }}</textarea>
            </div>
        </div>

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