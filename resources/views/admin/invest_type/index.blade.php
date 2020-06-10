@extends('admin.layouts.app')
@section('title', '理财类型')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
               理财类型列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>


    <div class="page-header">
        <h1>
            理财类型列表
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

{{--            <a href="{{ route('InvestmentType.create') }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>--}}

        </div>
        <!-- add reset e -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>



    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">理财类型</th>
            <th class="center">货币名称</th>
            <th class="center">状态</th>
{{--            <th class="center">操作</th>--}}
        </tr>
        </thead>

        <tbody>
        @foreach($investmentType as $item)
            <tr>
                <td class="center" style="line-height: 100px;">{{ $item['invest_id'] }}</td>
                <td class="center" style="line-height: 100px;">{{ $item['invest_type_name'] }}</td>
                <td class="center" style="line-height: 100px;">{{ $item['get_coin_names']['coin_name'] }}</td>
                <td class="center" style="line-height: 100px;">@if($item['is_usable']) <font color="red;">激活 </font> @else 暂停 @endif</td>
 {{--               <td class="center" style="line-height: 100px;">
                    <div>
                        <a href="{{ route('InvestmentType.edit',$item['invest_id']) }}" class="btn btn-xs btn-info" title="编辑">
                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                        </a>
                    </div>
                </td>--}}
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection