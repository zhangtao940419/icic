@extends('admin.layouts.app')
@section('title', '下级用户列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                下级用户列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--<div class="nav-search" id="nav-search">--}}
        {{--<form class="form-search">--}}
            {{--<span>--}}
                {{--<select class="nav-search-input" name="status" id="status">--}}
                    {{--<option value="">请选择状态</option>--}}
                    {{--<option value="1">待审核</option>--}}
                    {{--<option value="2">通过审核</option>--}}
                    {{--<option value="3">未通过审核</option>--}}
                {{--</select>--}}
                {{--<select class="nav-search-input" name="area" id="area">--}}
                    {{--<option value="">请选择地区</option>--}}
                    {{--<option value="1">中国大陆</option>--}}
                    {{--<option value="2">中国香港</option>--}}
                    {{--<option value="3">中国澳门</option>--}}
                {{--</select>--}}
            {{--</span>--}}
        {{--</form>--}}
    {{--</div>--}}

    <div class="page-header">
        <h1>
            下级用户列表
        </h1>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">下级用户</th>
            <th class="center">真实姓名</th>
            <th class="center" >认证等级</th>
            <th class="center" >注册时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($sUsers as $sUser)
            <tr>
                <td class="center">{{ $sUser->user_id}}</td>
                <td class="center">
                    {{ $sUser->user_phone}}
                </td>
                <td class="center">
                    {{ $sUser->userIdentify ? $sUser->userIdentify->identify_name : '--'}}
                </td>
                <td class="center" style="line-height: 50px;">{{ $sUser->getStatus()[$sUser->user_auth_level] }}</td>
                <td class="center">{{ $sUser->created_at}}</td>
                <td class="center" style="line-height: 50px;">
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $sUsers->render() }}
@endsection
@section('myJs')
    <script>

        $(function () {
            $('#status').val(data.status)
        })

        $(function () {
            $('#area').val(data.identify_area_id)
        })
    </script>
@endsection