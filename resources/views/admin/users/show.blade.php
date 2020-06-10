@extends('admin.layouts.app')
@section('title', '会员详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('users.index') }}">会员列表</a>
            </li>

            <li>
                会员列表
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
            <th class="center">会员名</th>
            <th class="center">
                {{ $user->user_name }}
            </th>
        </tr>
        <tr>
            <th class="center">真实姓名</th>
            <th class="center">
                {{ empty($user->userIdentify->identify_name) ? '暂未高级认证' : $user->userIdentify->identify_name }}
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
            <th class="center">是否是商家</th>
            <th class="center">
                <span class="label label-xlg {{ $user->is_business ? 'label-danger' : 'label-default'}}">{{ $user->is_business ? '商家用户' : '普通'}}</span>
            </th>
        </tr>
        <tr>
            <th class="center">是否是特殊用户</th>
            <th class="center">
                <span class="label label-xlg {{ $user->is_special_user ? 'label-danger' : 'label-default'}}">{{ $user->is_special_user ? '特殊用户' : '普通用户'}}</span>
            </th>
        </tr>
        @foreach($user->userWallet as $v)
            <tr>
                <th class="center">钱包地址</th>
                <th class="center">{{ $v->wallet_address }}</th>
            </tr>
        @endforeach
        <tr>
            <th class="center" style="line-height: 200px;">身份证正面</th>
            <th class="center">
                <img style="width: 200px; height: 200px; line-height: 200px;" src="{{ empty($user->userIdentify->identify_card_z_img) ? '' : $user->userIdentify->identify_card_z_img }}" alt="{{ empty($user->userIdentify->identify_name) ? '暂未高级认证' : $user->userIdentify->identify_name }}">
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 200px;">身份证背面</th>
            <th class="center">
                <img style="width: 200px; height: 200px; line-height: 200px;" src="{{ empty($user->userIdentify->identify_card_f_img) ? '' : $user->userIdentify->identify_card_f_img }}" alt="{{ empty($user->userIdentify->identify_name) ? '暂未高级认证' : $user->userIdentify->identify_name }}">
            </th>
        </tr>
        <tr>
            <th class="center" style="line-height: 200px;">身份证手持照</th>
            <th class="center">
                <img style="width: 200px; height: 200px; line-height: 200px;" src="{{ empty($user->userIdentify->identify_card_h_img) ? '' : $user->userIdentify->identify_card_h_img }}" alt="{{ empty($user->userIdentify->identify_name) ? '暂未高级认证' : $user->userIdentify->identify_name }}">
            </th>
        </tr>
        </tbody>
    </table>
@endsection