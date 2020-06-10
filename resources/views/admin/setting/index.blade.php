@extends('admin.layouts.app')
@section('title', '关于我们')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                关于我们
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="page-header">
        <h1>
            关于我们
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">



        </div>
        <!-- add reset e -->
    </div>
    @include('admin.layouts._message')
    <div>

        <form class="form-horizontal" role="form" method="post" action="{{ route('user.setting.update') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 关于我们 </label>
                <div class="col-sm-9">
                    <textarea rows="30" cols="180" name="about_us"> {{ $about_us->setting_value }} </textarea>
                    <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
                </div>
            </div>
        </form>

        <hr>

        <form class="form-horizontal" role="form" method="post" action="{{ route('user.setting.update') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 隐私政策 </label>
                <div class="col-sm-9">
                    <textarea rows="30" cols="180" name="privacy_policy"> {{ $privacy_policy->setting_value }} </textarea>
                    <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
                </div>
            </div>
        </form>





    </div>
@endsection