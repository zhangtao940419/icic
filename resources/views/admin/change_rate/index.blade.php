@extends('admin.layouts.app')
@section('title', '挂单手续费')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                挂单手续费
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--<div class="page-header">--}}
        {{--<h1>--}}
            {{--挂单手续费--}}
        {{--</h1>--}}
        {{--<!-- add reset s -->--}}
        {{--<div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">--}}

            {{--@if(@empty($outsiderate['rate']))--}}
                {{--<a href="{{ route('outside-rate.create') }}" class="btn btn-success">--}}
                    {{--<i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>--}}
                    {{--新增--}}
                {{--</a>--}}
            {{--@endif--}}

        {{--</div>--}}
        {{--<!-- add reset e -->--}}
    {{--</div>--}}
    <div>

        {{--<h3>--}}
            {{--当前场内挂单手续费:--}}
            {{--@if(!empty($insiderate['rate']))--}}
                {{--{{ $insiderate['rate']*100 }}%--}}
            {{--@else--}}
                {{--请先创建场内挂单手续费!--}}
            {{--@endif--}}
            {{--<a href="{{ route('inside-rate.create') }}" class="btn btn-sm   btn-info" title="操作">--}}
                {{--<i class="ace-icon fa fa-pencil bigger-120"></i>--}}
            {{--</a>--}}
        {{--</h3>--}}






    </div>
@endsection