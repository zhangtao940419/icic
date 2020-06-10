@extends('admin.layouts.app')
@section('title', '商家信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('business.index') }}">商家列表</a>
            </li>

            <li>
                商家信息
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    @include('admin.layouts._message')
    <table id="simple-table" class="table table-bordered table-hover" style="width:60%; margin-left:15em; font-size: 20px;">
        <tbody>
        <tr>
            <th class="center">ID</th>
            <th class="center">
                {{ $user->user_id }}
            </th>
        </tr>
        <tr>
            <th class="center">真实姓名</th>
            <th class="center">
                {{ $user->userIdentify->identify_name}}
            </th>
        </tr>
        <tr>
            <th class="center">手机号</th>
            <th class="center">
                {{ $user->user_phone }}
            </th>
        </tr>
        <tr>
            <th class="center">认证等级</th>
            <th class="center">{{ $user->getStatus()[$user->user_auth_level] }}</th>
        </tr>
        <tr>
            <th class="center" style="line-height: 50px;">头像</th>
            <th class="center">
                <img style="width: 50px; height: 50px;" src="{{ $user->user_headimg }}">
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 50px;">总收入</th>
            <th class="center">
                {{ $data['zIn'] }}
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 50px;">总支出</th>
            <th class="center">
                {{ $data['zOut'] }}
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 50px;">今日收入</th>
            <th class="center">
                {{ $data['dIn'] }}
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 50px;">今日支出</th>
            <th class="center">
                {{ $data['dOut'] }}
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 50px;">本周收入</th>
            <th class="center">
               {{ $data['wIn'] }}
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 50px;">本周支出</th>
            <th class="center">
                {{ $data['wOut'] }}
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 50px;">本月收入</th>
            <th class="center">
                {{ $data['mIn'] }}
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 50px;">本月支出</th>
            <th class="center">
                {{ $data['mOut'] }}
            </th>
        </tr>

        </tbody>
    </table>
@endsection