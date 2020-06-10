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
                行为记录
            </li>
        </ul>
    </div>
        <!-- /.breadcrumb -->
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search">
        <form class="form-search">
            <select class="nav-search-input" name="type">
                <option value="">请选择类型</option>
                @foreach($types as $type)
                <option value="{{ $type['type_des'] }}">{{ $type['type_des'] }}</option>
                    @endforeach
            </select>

            <span>
                <input type="text" placeholder="会员手机" class="nav-search-input" id="nav-search-input" name="user_phone" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            行为列表
        </h1>
    </div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">行为id</th>
            <th class="center">所属后台用户</th>
            <th class="center">会员手机</th>
            <th class="center">行为类别</th>
            <th class="center">行为详情</th>
            <th class="center">行为时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($records as $record)
            <tr>
                <td class="center">{{ $record->id }}</td>
                <td class="center">{{ $record->user->username }}</td>
                <th class="center">@if($record->user_id == 0) 无 @else {{ $record->uuser->user_phone }} @endif</th>
                <td class="center">{{ $record->type_des }}</td>
                <td class="center">{{ $record->behavior_des }}</td>
                <td class="center">{{ $record->created_at }}</td>
                <td class="center"></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--分页--}}
    {{ $records->links() }}
@endsection
