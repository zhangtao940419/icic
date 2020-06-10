@extends('admin.layouts.app')
@section('myCss')
    <link rel="stylesheet" href="/assets/css/simditor.css">
@endsection
@section('title', $article->id ? '编辑文章': '文章列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('category.index') }}">分类列表</a>
            </li>
            @if($article->id)
                <li class="active">编辑文章</li>
            @else
                <li class="active">创建文章</li>
            @endif
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入报错信息页面--}}
    @include('admin.layouts._error')
    <div class="page-header">
        <h1>
            @if($article->id)
                编辑文章
            @else
                创建文章
            @endif
                <a href="{{ route('article.index') }}" class="btn btn-danger btn-sm" style="float: right"><i class="ace-icon fa fa-reply icon-only"></i>
                    返回
                </a>
        </h1>
    </div>
    @if($article->id)
        <form class="form-horizontal" role="form" method="post" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ route('article.update', $article->id) }}">
            <input type="hidden" name="_method" value="PUT">
            @else
                <form class="form-horizontal" role="form" accept-charset="UTF-8" enctype="multipart/form-data" method="post" action="{{ route('article.store') }}">
                    @endif

                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 文章标题 </label>
                        <div class="col-sm-9">
                            <input type="text" name="title" id="form-field-1" placeholder="分类标题" class="col-xs-10 col-sm-5" value="{{ $article->title }}" />
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 所属分类 </label>

                        <div class="col-sm-9">
                            <select id="form-field-2" class="col-xs-10 col-sm-5" name="category_id">
                                <option value="">请选择分类</option>
                                @foreach($categories as $value)
                                    <option value="{{ $value->id }}" {{ $value->id == $article->category_id ? 'selected' : '' }}>{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 封面图片 </label>
                        <div class="col-sm-9">
                            <input type="file" name="cover" id="form-field-1" placeholder="封面图片" class="col-xs-10 col-sm-5" value="{{ $article->cover }}" />
                        </div>
                    </div>

                    @if($article->cover)
                        <div class="col-xs-12 ace-thumbnails clearfix" style="position: relative; left: 26em; margin-bottom: 20px;">
                            <label for="">封面图片：</label>
                            <a href="{{ $article->cover }}" title="Photo Title" data-rel="colorbox">
                                <img width="200" height="200" alt="200x200" src="{{ $article->cover }}" />
                            </a>
                        </div>
                    @endif

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-4">文章内容</label>

                        <div class="col-sm-5">
                            <textarea name="body" id="editor" rows="5" cols="80">{{ $article->body }}</textarea>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="clearfix form-actions">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn btn-info">
                                <i class="ace-icon fa fa-check bigger-110"></i>
                                提交
                            </button>
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                重置
                            </button>
                        </div>
                    </div>
                </form>
@endsection
@section('myJs')
    <script src="/assets/js/simditor-2.3.19/module.js"></script>
    <script src="/assets/js/simditor-2.3.19/hotkeys.js"></script>
    <script src="/assets/js/simditor-2.3.19/uploader.js"></script>
    <script src="/assets/js/simditor-2.3.19/simditor.js"></script>
    <script src="/assets/ueditor/ueditor.config.js"></script>
    <script src="/assets/ueditor/ueditor.all.js"></script>

    <script>
        $(document).ready(function(){
            var ue = UE.getEditor('editor',{'initialFrameHeight':300});
            ue.ready(function(){
                ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');
            });
            {{--let editor = new Simditor({--}}
                {{--textarea: $('#editor'),--}}
                {{--upload: {--}}
                    {{--url: '{{ route('article.upload_image') }}',--}}
                    {{--params: { _token: '{{ csrf_token() }}' },--}}
                    {{--fileKey: 'upload_file',--}}
                    {{--connectionCount: 3,--}}
                    {{--leaveConfirm: '文件上传中，关闭此页面将取消上传。'--}}
                {{--},--}}
                {{--pasteImage: true,--}}
            {{--});--}}
        });
        {{--$(document).ready(function(){--}}
            {{--let editor = new Simditor({--}}
                {{--textarea: $('#editor'),--}}
                {{--upload: {--}}
                    {{--url: '{{ route('article.upload_image') }}',--}}
                    {{--params: { _token: '{{ csrf_token() }}' },--}}
                    {{--fileKey: 'upload_file',--}}
                    {{--connectionCount: 3,--}}
                    {{--leaveConfirm: '文件上传中，关闭此页面将取消上传。'--}}
                {{--},--}}
                {{--pasteImage: true,--}}
            {{--});--}}
        {{--});--}}
    </script>
@endsection
