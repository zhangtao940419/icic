@extends('admin.layouts.app')
@section('title', '理财购买记录列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
               理财购买记录列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="page-header">
        <h1>
            理财购买记录列表
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

{{--            <a href="{{ route('InvestmentRule.create') }}" class="btn btn-success">
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
            <th class="center">订单号</th>
            <th class="center">理财类型</th>
            <th class="center">货币名称</th>
            <th class="center">时长</th>
            <th class="center">投资本金</th>
            <th class="center">利率</th>
            <th class="center">状态</th>
       {{--     <th class="center">操作</th>--}}
        </tr>
        </thead>
        <tbody>
        @foreach($userInvestFlow as $item)
            <tr>
                <td class="center" style="line-height: 100px;">{{ $item['id'] }}</td>
                <td class="center" style="line-height: 100px;">{{ $item['invest_order'] }}</td>
                <td class="center" style="line-height: 100px;">{{ $item['getInvestTypeName']->invest_type_name }}</td>
                <td class="center" style="line-height: 100px;">{{ $item['getCoinName']->coin_name }}</td>
                <td class="center" style="line-height: 100px;">{{ $item['invest_time']/86400 }}天</td>
                <td class="center" style="line-height: 100px;">{{ $item['invest_money'] }}{{ $item['getCoinName']->coin_name }}</td>
                <td class="center" style="line-height: 100px;">{{ $item['rate_of_return_set'] }}%</td>
                <td class="center" style="line-height: 100px;">@if($item['invest_status']==1)
                        <font color="#8b008b;"> 托管中 </font>
                          @elseif(($item['invest_status']==2))  <font color="#ff8c00"> 可提取</font>
                          @elseif(($item['invest_status']==3)) <font color="red;"> 未到期提取 <font>
                          @elseif(($item['invest_status']=4)) <font color="#4169e1;"> 到期提取 </font>
                    @endif</td>
                {{--    <td class="center" style="line-height: 100px;">
                            <div>
                               <a href="{{ route('InvestmentRule.edit',$item['invest_id']) }}" class="btn btn-xs btn-info" title="编辑">
                                                <i class="ace-icon fa fa-pencil bigger-120"></i>
                                            </a>
                                         <button type="button" class="btn btn-xs btn-del btn-danger" data-url="{{ route('InvestmentRule.destroy',$item['id']) }}" style="margin-left: 2px">
                                                <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                            </button>
                   </div>--}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {!! $userInvestFlow->render() !!}
@endsection