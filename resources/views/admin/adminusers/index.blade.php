@extends('admin.layouts.app')
@section('title', '后台用户列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                后台用户列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            后台用户列表
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('adminuser.create') }}" class="btn btn-success">
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
            <th class="center">用户名</th>
            <th class="center">手机号</th>
            <th class="center">说明</th>
            <th class="center">创建时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($users as $user)
            <tr>
                <td class="center">{{ $user->id }}</td>
                <td class="center">{{ $user->username }}</td>
                <td class="center">{{ $user->phone }}</td>
                <td class="center">{{ $user->description }}</td>
                <td class="center">{{ $user->created_at }}</td>
                <td class="center">
                    <div>
                        <a href="{{ route('adminuser.edit', $user->id) }}" class="btn btn-xs btn-info">
                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                        </a>
                        <button type="button" class="btn btn-xs btn-danger btn-del" data-url="{{ route('adminuser.destroy', $user->id) }}" style="margin-left: 2px">
                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection