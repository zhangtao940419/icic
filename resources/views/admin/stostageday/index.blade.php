@extends('admin.layouts.app')
@section('title', 'Sto发行阶段天数列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                Sto发行阶段天数列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            Sto发行阶段天数列表
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

{{--
            <a href="{{ route('stoStageDay.create','data_id='.day_id) }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>
--}}

        </div>
        <!-- add reset e -->
    </div>


    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">天数</th>
            <th class="center">每天发行量</th>
            <th class="center">每天剩余发行量</th>
            <th class="center">状态</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($stoStageDayList as $stoStageDayLists)
        <tr>
            <td class="center">{{ $stoStageDayLists['day_id'] }}</td>
            <td class="center" ><font size="3px" color="#4169e1">第{{ $stoStageDayLists['issue_day'] }}天</font></td>
            <td class="center">{{ $stoStageDayLists['stage_issue_number'] }}</td>
            <td class="center">{{ $stoStageDayLists['stage_issue_remain_number']}}</td>
            <td class="center">{!! $stoStageDayLists['issue_status'] !!} </td>
            <td class="center">
                <div>
                    <a href="{{ route('stoStageDay.edit',$stoStageDayLists['day_id']) }}" class="btn btn-xs btn-info"  title="编辑">
                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                    </a>
                    <a href="{{ route('stoStageDay.setting',$stoStageDayLists['day_id']) }}" class="btn btn-xs btn-info"  title="设置">
                        <i class="ace-icon fa fa-flag bigger-120"></i>
                    </a>
{{--                    <button type="button" class="btn btn-xs btn-del btn-danger" data-url="{{ route('stoStageDay.destroy',  $stoStageLists['data_id']) }}" style="margin-left: 2px">
                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                    </button>--}}
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection