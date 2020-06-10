@extends('admin.layouts.app')
@section('title', '用户提问列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ url('/admin/userQuestion') }}">用户提问列表</a>

            </li>
        </ul>
    </div>
        <!-- /.breadcrumb -->
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <span class="label label-xlg label-primary" style="margin-left: 80%;cursor: pointer" onclick=""><a href="{{ route('question_type.index') }}" style="color: white"> 问题类型管理 </a></span><br><br>

    <div class="nav-search" id="nav-search">
        <form class="form-search">
            <span class="input-icon">
                <select class="nav-search-input"  name="type_id">
                    <option value="">问题类型</option>
                    @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->type }}</option>
                        @endforeach
                </select>
            </span>
            <span class="input-icon">
                <select class="nav-search-input"  name="status">
                    <option value="">状态</option>
                        <option value="0" @if(request('status') === '0') selected @endif>未处理</option>
                    <option value="1" @if(request('status') === '1') selected @endif>已处理</option>
                </select>
            </span>
            <span>
                <input type="text" placeholder="用户名..." class="nav-search-input" id="nav-search-input" name="username" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            用户提问列表
        </h1>
    </div>


    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">用户</th>
            <th class="center">真实姓名</th>
            <th class="center">问题类型</th>
            <th class="center">问题</th>
            <th class="center">用户邮箱</th>
            <th class="center">回答</th>
            <th class="center">状态</th>
            <th class="center">提交时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        @foreach($records as $record)
        <tbody>
            <tr>
                <td class="center">{{ $record->user->user_phone }}</td>
                <td class="center">{{ $record->user->userIdentify ? $record->user->userIdentify->identify_name : '--'  }}</td>
                <td class="center">{{ $record->type ==null ? '无' : $record->type->type }}</td>
                <td class="center">
                    {{ $record->question_limit }}
                </td>
                <td class="center">
                    {{ $record->email }}
                </td>
                <td class="center">
                    {{ $record->answer_limit }}
                </td>
                <td class="center" style="color: {{ ['red','green'][$record->status] }}">{{ $record->getStatus() }}</td>
                <td class="center">{{ $record->created_at }}</td>
                <td class="center">
                    <div>
                        <a style="margin-right: 2px;" href="{{ route('user.questionDetail',['id' => $record->id]) }}" title="详情" class="btn btn-xs btn-info">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        </tbody>
            @endforeach
    </table>
    {{--分页--}}
    {{ $records->appends(Request::except('page'))->render() }}
@endsection
