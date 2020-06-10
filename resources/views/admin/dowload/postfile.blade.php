@extends('admin.layouts.app')
@section('title', '上传安装包')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>

            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            上传安装包
        </h1>
    </div>

    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div class="row">
        <div class="col-xs-12">
            <form method="post" action="{{ route('post.package') }}" accept-charset="UTF-8" enctype="multipart/form-data" style="padding-left: 30%;">
                {{ csrf_field() }}
                <div class="col-xs-12 ace-thumbnails">
                    <label for="" style="padding-right: 50px;">
                        上传android安装包:
                    </label>
                    <input style="display: inline-block;" name="android" type="file"/>
                </div>
                <div class="form-group">
                    <label class="col-sm-2"  for="form-field-1" style="margin-top: 5px; margin-bottom: 1em; width: 100px;"> 安卓版本号: </label>
                    <div class="col-sm-9">
                        <input type="text" name="android_version" id="form-field-1" placeholder="版本号" class="col-xs-10 col-sm-2"/>
                    </div>
                </div>



                <div class="col-xs-12 ace-thumbnails" style="margin-top: 50px;">
                    <label for="" style="padding-right: 50px;">
                        上传ios安装包:
                    </label>
                    <input style="display: inline-block;" name="ios" type="file"/>
                </div>
                <div class="form-group">
                    <label class="col-sm-2"  for="form-field-1" style="margin-top: 5px; margin-bottom: 1em; width: 100px;"> IOS版本号: </label>
                    <div class="col-sm-9">
                        <input type="text" name="ios_version" id="form-field-1" placeholder="版本号" class="col-xs-10 col-sm-2"/>
                    </div>
                </div>

                <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 50px; padding-left: 150px;">
                    <button class="btn btn-primary">
                        <i class="ace-icon fa fa-check"></i>
                        确认
                    </button>

                    <button class="btn btn-danger" @click="back()">
                        <i class="ace-icon glyphicon glyphicon-remove"></i>
                        返回
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection