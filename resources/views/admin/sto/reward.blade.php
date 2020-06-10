@extends('admin.layouts.app')
@section('title', 'Sto推荐奖励记录')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                Sto推荐奖励记录
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
                <input type="text" placeholder="上级用户..." class="nav-search-input" id="userphone" name="userphone" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
            <span>
                <input type="text" placeholder="下级用户..." class="nav-search-input" id="suserphone" name="suserphone" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            Sto推荐奖励记录
        </h1>
        <!-- add reset s -->
        {{--<div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">--}}

            {{--<a href="{{ route('stoList.create') }}" class="btn btn-success">--}}
                {{--<i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>--}}
                {{--新增--}}
            {{--</a>--}}

        {{--</div>--}}
        <!-- add reset e -->
    </div>


    {{--引入信息提示页面--}}
    @include('admin.layouts._message')

    <div>
        合计奖励数量为:
        <h4 style="color: red; display: inline-block">
            {{ $sum }}
        </h4>
    </div>

    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">上级用户</th>
            <th class="center">下级用户</th>
            <th class="center">币种</th>
            <th class="center">奖励上级数量</th>
            <th class="center">下级购买数量</th>
            <th class="center">时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($records as $record)
        <tr>
            <td class="center">{{ $record->id }}</td>
            <td class="center">{{ $record->user->user_phone }}</td>
            <td class="center">{{ $record->s_user->user_phone }}</td>
            <td class="center">{{ $record->coin->coin_name }}</td>
            <td class="center">{{ $record->flow_amount }}</td>
            <td class="center">{{ $record->s_user_buy_amount }}</td>
            <td class="center">{{ $record->created_at }}</td>
            <td class="center">
                <div>

                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    {{ $records->appends(\Request::except('page'))->render() }}
@endsection