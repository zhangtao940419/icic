@extends('admin.layouts.app')
@section('title', '问题类型管理')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                问题类型管理
            </li>
        </ul>
    </div>
        <!-- /.breadcrumb -->
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>


    <div class="page-header">
        <h1>
            问题类型管理
        </h1>
    </div>


    <span class="label label-xlg label-primary" style="margin-left: 85%;margin-bottom: 1%;cursor: pointer" onclick=""><a href="{{ route('question_type.add') }}" style="color: white"> 新增 </a></span>

    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">id</th>
            <th class="center">类型名</th>
            <th class="center">创建时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        @foreach($records as $record)
        <tbody>
            <tr>
                <td class="center">{{ $record->id }}</td>
                <td class="center">{{ $record->type }}</td>
                <td class="center">{{ $record->created_at }}</td>
                <td class="center">
                    <div>
                        <a style="margin-right: 2px;" href="{{ route('question_type.detail',['id' => $record->id]) }}" title="详情" class="btn btn-xs btn-info">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>
                        <a style="margin-right: 2px;" href="{{ route('question_type.delete',['id' => $record->id]) }}" title="删除" class="btn btn-xs btn-danger">
                            <i class="ace-icon glyphicon glyphicon-remove-circle"></i>
                        </a>
                    </div>

                </td>
            </tr>
        </tbody>
            @endforeach
    </table>
    {{--分页--}}
    {{ $records->links() }}
@endsection
