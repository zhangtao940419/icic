@extends('admin.layouts.app')
@section('title', '会员列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                商家列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            商家列表
        </h1>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    @include('admin.layouts._error')
    <div>
        合计用户数量为:
        <h4 style="color: red; display: inline-block">

        </h4>
    </div>
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">会员Id</th>
            <th class="center">会员名</th>
            <th class="center">会员电话</th>
            <th class="center">会员头像</th>
            <th class="center">注册时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($users as $user)
            <tr>
                <td class="center">{{ $user->user_id }}</td>
                <td class="center">
                    {{ $user->userIdentify->identify_name }}
                    <span class="label label-sm {{ $user->is_business ? 'label-danger' : ''}}">{{ $user->is_business ? '商家用户' : ''}}</span>
                </td>
                <td class="center">{{ $user->user_phone }}</td>
                <td class="center">
                    <img src="{{ $user->user_headimg }}" style="height: 40px; width: 40px;">
                </td>
                <td class="center">
                    {{ $user->created_at }}
                </td>
                <td class="center">
                    <div>
                        <a style="margin-right: 2px;" href="{{ route('business.show',['id'=>$user->user_id]) }}" title="查看详细" class="btn btn-xs btn-info">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

{{--分页--}}
    {{ $users->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')

    <script>
        $(function () {

        })
    </script>
@endsection
