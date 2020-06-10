@extends('admin.layouts.app')
@section('title', '轮播图列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                轮播图列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>


    <div class="page-header">
        <h1>
            轮播图列表
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('banner.create') }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>

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
            <th class="center">轮播图</th>
            <th class="center" >点击跳转地址</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($banners as $banner)
            <tr>
                <td class="center" style="line-height: 100px;">{{ $banner->banner_id }}</td>
                <td class="center">
                    <img style="width: 100px; height: 100px;" src="{{ $banner->banner_imgurl }}">&nbsp;
                </td>
                <td class="center" style="line-height: 100px;">{{ $banner->banner_tourl }}</td>
                <td class="center" style="line-height: 100px;">
                    <div>
                        <a href="{{ route('banner.edit', $banner->banner_id) }}" class="btn btn-xs btn-info" title="编辑">
                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                        </a>
                        <form action="{{ route('banner.destroy', $banner->banner_id) }}" method="post" style="display: inline-block">
                            <button type="submit" class="btn btn-xs btn-danger" style="margin-left: 2px">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <i class="ace-icon fa fa-trash-o bigger-120"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $banners->links() }}
@endsection