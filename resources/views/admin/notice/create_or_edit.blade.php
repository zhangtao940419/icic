@extends('admin.layouts.app')
@section('title',  $notice->id ? '编辑公告' : '创建公告' )
@section('content')
    <div class="main-container ace-save-state" id="main-container">
        <div class="main-content">
            <div class="main-content-inner">
                <!-- 内容导航 s -->
                <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                    <ul class="breadcrumb">
                        <li>
                            <i class="ace-icon fa fa-home home-icon"></i>
                            <a href="{{ url('/admin') }}">首页</a>
                        </li>

                        <li>
                            <a href="#">公告列表</a>
                        </li>
                        <li class="active">
                            @if($notice->_id)
                                编辑公告
                            @else
                                创建公告
                            @endif
                        </li>
                    </ul>
                </div>
                <!-- 内容导航 e -->

                <div class="space-4"></div>
                {{--引入信息提示页面--}}
                @include('admin.layouts._error')

                <div class="page-header">
                    <h1>
                        @if($notice->_id)
                            编辑公告
                        @else
                            创建公告
                        @endif
                    </h1>
                    <!-- add reset s -->
                    <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

                        <button class="btn btn-success" @click="add()">
                            <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                            新增
                        </button>
                    </div>
                </div>
                <div class="space-4"></div>
                <div class="space-4"></div>
                <div class="space-4"></div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    @if($notice->id)
                        <form method="post" action="{{ route('notice.update', $notice->id) }}" accept-charset="UTF-8" enctype="multipart/form-data" style="padding-left: 30%;">
                            <input type="hidden" name="_method" value="PUT">
                            @else
                                <form method="post" action="{{ route('notice.store') }}" accept-charset="UTF-8" enctype="multipart/form-data" style="padding-left: 30%;">
                                    @endif
                                    {{ csrf_field() }}


                                    <div class="col-xs-12 ace-thumbnails">
                                        <label for="" style="display: inline-block;text-align: right;width: 138px; margin-left: 10px;">
                                            @if($notice->id)
                                                公告图片：
                                            @else
                                                公告图片:
                                            @endif
                                        </label>
                                        <input style="display: inline-block;" name="notice_img" type="file"/>
                                    </div>

                                    @if($notice->notice_img)
                                        <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 10px; margin-left: 85px;">
                                            <label for="">公告图片：</label>
                                            <a href="{{ $notice->notice_img }}" title="Photo Title" data-rel="colorbox">
                                                <img width="200" height="200" alt="200x200" src="{{ $notice->notice_img }}" />
                                            </a>
                                        </div>
                                    @endif

                                    <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 10px;">
                                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 150px;">公告内容：</label>
                                        <textarea name="notice_content" rows="3" cols="50">{{ $notice->notice_content }}</textarea>
                                    </div>

                                    <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 10px; margin-left: 85px;">
                                        <label for="">是否开启公告：</label>
                                        <select name="switch">
                                            @if($notice->id)
                                                <option value="{{ $notice->switch }}">{{ $notice->switch ? '开启' : '关闭' }}</option>
                                            @endif
                                            <option value="0">关闭</option>
                                            <option value="1">开启</option>
                                        </select>
                                    </div>

                                    <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 50px; padding-left: 150px;">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ace-icon fa fa-check"></i>
                                            确认
                                        </button>

                                        <a href="{{ route('notice.index') }}" class="btn btn-danger">
                                            <i class="ace-icon glyphicon glyphicon-remove"></i>
                                            返回
                                        </a>
                                    </div>
                                </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('myJs')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        jQuery(function ($) {
            var $overflow = '';
            var colorbox_params = {
                rel: 'colorbox',
                reposition: true,
                scalePhotos: true,
                scrolling: false,
                previous: '<i class="ace-icon fa fa-arrow-left"></i>',
                next: '<i class="ace-icon fa fa-arrow-right"></i>',
                close: '&times;',
                current: '{current} of {total}',
                maxWidth: '100%',
                maxHeight: '100%',
                onOpen: function () {
                    $overflow = document.body.style.overflow;
                    document.body.style.overflow = 'hidden';
                },
                onClosed: function () {
                    document.body.style.overflow = $overflow;
                },
                onComplete: function () {
                    $.colorbox.resize();
                }
            };

            $('.ace-thumbnails [data-rel="colorbox"]').colorbox(colorbox_params);
            $("#cboxLoadingGraphic").html("<i class='ace-icon fa fa-spinner orange fa-spin'></i>"); //let's add a custom loading icon


            $(document).one('ajaxloadstart.page', function (e) {
                $('#colorbox, #cboxOverlay').remove();
            });
        })
    </script>
@endsection

