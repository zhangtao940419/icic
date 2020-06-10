@extends('admin.layouts.app')
@section('title', 'c2c用户列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                c2c用户列表
            </li>
        </ul>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="margin-top: 10px;">
        <form class="form-search6">
            <span>
                <input type="text" placeholder="用户名或电话..." class="nav-search-input" name="username" autocomplete="off">

                {{--<select name="order" id="order" class="nav-search-input">--}}
                    {{--<option value="">排序方式</option>--}}
                    {{--<option value="trade_number_desc">数量从高到低</option>--}}
                    {{--<option value="trade_number_asc">数量从低到高</option>--}}
                {{--</select>--}}
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            c2c用户列表
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
{{ $userList->total() }}
        </h4>
        <span class="label label-xlg label-primary" style="margin-left: 80%;cursor: pointer" onclick="outExcel()">导出excel</span>
    </div>
    <table id="simple-table" class="table table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">userId</th>
            <th class="center">用户手机</th>
            <th class="center">认证姓名</th>
            <th class="center">累计买入</th>
            <th class="center">累计卖出</th>
            <th class="center">卖出-买入</th>
            <th class="center">状态</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($userList as $user)
            <tr>
                <td class="center">{{ $user->userMsg->user_id }}</td>
                <td class="center">{{$user->userMsg->user_phone}}</td>
                <td class="center">{{$user->userIdentify->identify_name}}</td>
                <td class="center">{{$user->getUserTotalBuy()}}</td>
                <td class="center">{{$user->getUserTotalSell()}}</td>
                <td class="center">{{$user->getUserTotalSell() - $user->getUserTotalBuy()}}</td>
                <td class="center">@if($user->checkUserStatus() == 1)<span class="label label-sm label-danger">异常</span>@else <span class="label label-sm label-success">正常</span> @endif</td>
                <td class="center">

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $userList->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script src="/assets/js/jedate.js"></script>
    <script src="/assets/js/demo.js"></script>
    <script>

        $(function () {
            $('#test04').val(time[0]);
            $('#test05').val(time[1]);
            $("#order").val(order)
        });


        function outExcel() {
            // if (!confirm('确定导出excel吗?')){
            //     return false;
            // }
            window.location.href="{!! $excel !!}";
        }
    </script>
@endsection
