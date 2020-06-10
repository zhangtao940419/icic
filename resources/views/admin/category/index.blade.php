@extends('admin.layouts.app')
@section('title', '文章分类列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                分类列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            文章分类管理
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('category.create') }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>

        </div>
        <!-- add reset e -->
    </div>


    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">名称</th>
            <th class="center">描述</th>
            <th class="center">分类等级</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($categories as $category)
        <tr>
            <td class="center">{{ $category->id }}</td>
            <td class="center">{{ str_repeat('--', $category->leve) }}{{ $category->name }}</td>
            <td class="center">{{ $category->description }}</td>
            <td class="center">{{ $category->leve }}</td>
            <td class="center">
                <div>
                    <a href="{{ route('category.edit', $category->id) }}" class="btn btn-xs btn-info">
                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                    </a>

                    <button type="button" class="btn btn-xs btn-del btn-danger" data-url="{{ route('category.destroy', $category->id) }}" style="margin-left: 2px">
                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    {{ $categories->render() }}
@endsection