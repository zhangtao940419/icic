@extends('admin.layouts.app')
@section('title', '认证高级用户列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                申请高级认证列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search">
        <form class="form-search">
            <span>
                <select class="nav-search-input" name="status" id="status">
                    <option value="">请选择状态</option>
                    <option value="1">待审核</option>
                    <option value="2">通过审核</option>
                    <option value="3">未通过审核</option>
                </select>
                <select class="nav-search-input" name="area" id="area">
                    <option value="">请选择地区</option>
                    <option value="1">中国大陆</option>
                    <option value="2">中国香港</option>
                    <option value="3">中国澳门</option>
                </select>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            申请高级认证列表
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
            <th class="center">请求高级认证用户</th>
            <th class="center">真实姓名</th>
            <th class="center" >当前认证等级</th>
            <th class="center" >当前认证状态</th>
            <th class="center" >所属地区</th>
            <th class="center" >证件类型</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($userIdentifys as $value)
            <tr>
                <td class="center">{{ $value->identify_id}}</td>
                <td class="center">
                    <img style="width: 50px; height: 50px;" src="{{ $value->user->user_headimg }}">
                    <div>{{ $value->user->user_name }}</div>
                </td>
                <td class="center" style="line-height: 50px;">{{ $value->identify_name }}</td>
                <td class="center" style="line-height: 50px;">{{ $value->user->getStatus()[$value->user->user_auth_level] }}</td>
                <td class="center" style="line-height: 50px;">{{ $value->getstatu()[$value->status] }}</td>
                <td class="center" style="line-height: 50px;">{{ $value->user_identify_area->area_name }}</td>
                <td class="center" style="line-height: 50px;">{{ $value->user_identify_area->identify_name }}</td>
                <td class="center" style="line-height: 50px;">
                    <div>
                        <a href="{{ route('authentication.edit', $value->identify_id) }}" class="btn btn-xs btn-info" title="查看详细">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $userIdentifys->appends($data)->render() }}
@endsection
@section('myJs')
    <script>
        var data = {!! json_encode($data) !!};
        $('#status').on('change', function () {
            $(".form-search").submit();
        })

        $('#area').on('change', function () {
            $(".form-search").submit();
        })

        $(function () {
            $('#status').val(data.status)
        })

        $(function () {
            $('#area').val(data.identify_area_id)
        })
    </script>
@endsection