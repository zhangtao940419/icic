@extends('admin.layouts.app')
@section('title', '用户矿池流水')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li class="active">用户矿池流水</li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入报错信息页面--}}
    @include('admin.layouts._error')
    <div class="nav-search pull-right" id="nav-search">
        <form class="form-search">
            <span>
                <input type="text" placeholder="会员名..." class="nav-search-input" name="username">

                <select name="flow_type" id="type" class="nav-search-input">
                    <option value="">类型</option>
                    @foreach($types as $k=>$type)
                    <option value="{{ $k }}">{{ $type }}</option>
                        @endforeach
                </select>

                <button type="submit" style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>
    <div class="page-header">
        <h1>
            用户矿池流水
        </h1>

    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div>
    </div>
    {{--<form class="form-search">--}}
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">id</th>
            <th class="center">所属会员</th>
            <th class="center">下级会员</th>
            <th class="center">货币类型</th>
            <th class="center">数量({{ $total }})</th>
            <th class="center">类型</th>
            <th class="center">时间</th>
            {{--<th class="center">操作</th>--}}
        </tr>
        </thead>

        <tbody>
        @foreach($flows as $flow)
            <tr>
                <td class="center">{{ $flow->id }}</td>
                <td class="center">{{ $flow->user->user_phone }} ({{ $flow->user->userIdentify? $flow->user->userIdentify->identify_name : '--' }}) </td>
                <td class="center">{{ $flow->s_user_id ? $flow->s_user->user_phone : '--' }} ({{ ($flow->s_user && $flow->s_user->userIdentify)? $flow->s_user->userIdentify->identify_name : '--' }}) </td>
                <td class="center">{{ $flow->coin->coin_name }}</td>
                <td class="center">{{ $flow->amount }}</td>
                <td class="center">{{ $flow->getFlowTypeAttribute()  }}</td>
                <td class="center">{{ $flow->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--</form>--}}
    {{ $flows->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script>
    </script>

    <script>
        $('.status').on('change', function () {
            $(".form-search").submit();
        })

        $('#area').on('change', function () {
            $(".form-search").submit();
        })

    </script>
@endsection