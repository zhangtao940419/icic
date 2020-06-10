@extends('admin.layouts.app')
@section('title', '用户钱包')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                钱包列表
            </li>
        </ul>
    </div>
        <!-- /.breadcrumb -->
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search">
        <form class="form-search">
            <span>
                <input type="text" placeholder="用户名..." class="nav-search-input" id="nav-search-input" name="username" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            钱包列表
        </h1>
    </div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">用户钱包id</th>
            <th class="center">所属会员</th>
            <th class="center">真实姓名</th>
            <th class="center">类型</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($users as $user)
            <tr>
                <td class="center">{{ $user->user_id }}</td>
                <td class="center">{{ $user->user_name }}</td>
                <td class="center">{{ $user->userIdentify ? $user->userIdentify->identify_name : '--' }}</td>
                <td class="center">
                    <img src="{{ $user->user_headimg }}" style="height: 40px; width: 40px;">
                </td>
                <td class="center">
                    <span class="label label-sm {{ $user->is_special_user ? 'label-danger' : ''}}">{{ $user->is_special_user ? '特殊' : '普通'}}用户</span>

                </td>
                <td class="center">
                    <div>
                        <a style="margin-right: 2px;" href="{{ route('userwallet.show', $user->user_id) }}" title="查看用户钱包" class="btn btn-xs btn-info">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--分页--}}
    {{ $users->links() }}
@endsection
