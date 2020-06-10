@extends('admin.layouts.app')
@section('title', '内部用户usdt划转记录')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                内部用户usdt划转记录
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
                <input type="text" placeholder="用户手机" class="nav-search-input" id="nav-search-input" name="user_phone" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            内部用户usdt划转记录
        </h1>
    </div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">id</th>
            <th class="center">用户手机</th>
            <th class="center">划转数量</th>
            <th class="center">时间</th>
        </tr>
        </thead>

        <tbody>
        @foreach($records as $record)
            <tr>
                <td class="center">{{ $record->id }}</td>
                <td class="center">{{ $record->user->user_phone }}</td>
                <td class="center">{{ $record->amount }}</td>
                <td class="center">{{ $record->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--分页--}}
    {{ $records->links() }}
@endsection
