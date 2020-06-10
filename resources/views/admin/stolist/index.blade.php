@extends('admin.layouts.app')
@section('title', 'Sto发行列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                发行列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            Sto发行列表
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('stoList.create') }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>

        </div>
        <!-- add reset e -->
    </div>


    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">基币名称</th>
            <th class="center">兑币名称</th>
            <th class="center">兑币总量</th>
            <th class="center">兑币发行总量</th>
            <th class="center">图片</th>
            <th class="center">描述</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($stoList as $stoLists)
        <tr>
            <td class="center">{{ $stoLists['data_id'] }}</td>
            <td class="center">{{ $stoLists['get_base_coin_names']['coin_name']}}</td>
            <td class="center">{{ $stoLists['get_coin_names']['coin_name'] }}</td>
            <td class="center">{{ $stoLists['total_coin_issuance'] }}</td>
            <td class="center">{{ $stoLists['issue_coin_number'] }}</td>
            <td class="center"><img height="100px" width="200px" src="{{ $stoLists['img'] }}"></td>
            <td class="center">{{$stoLists['des'] }}</td>
            <td class="center">
                <div>
                    <a href="{{ route('stoList.edit',$stoLists['data_id']) }}" class="btn btn-xs btn-info"  title="编辑">
                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                    </a>
                    <a href="{{ route('stoStage.index','data_id='. $stoLists['data_id']) }}" class="btn btn-xs btn-info"  title="发行周期">
                        <i class="ace-icon fa fa-flag bigger-120"></i>
                    </a>
                    <button type="button" class="btn btn-xs btn-del btn-danger" data-url="{{ route('stoList.destroy',  $stoLists['data_id']) }}" style="margin-left: 2px">
                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection