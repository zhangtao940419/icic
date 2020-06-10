@extends('admin.layouts.app')
@section('title',  $banner->id ? '编辑轮播图' : '创建轮播图' )
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
                            <a href="#">轮播图列表</a>
                        </li>
                        <li class="active">
                            @if($banner->banner_id)
                                编辑轮播图
                            @else
                                创建轮播图
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
                        @if($banner->banner_id)
                            编辑轮播图
                        @else
                            创建轮播图
                        @endif
                    </h1>
                </div>
                <div class="space-4"></div>
                <div class="space-4"></div>
                <div class="space-4"></div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    @if($banner->banner_id)
                    <form method="post" action="{{ route('banner.update', $banner->banner_id) }}" accept-charset="UTF-8" enctype="multipart/form-data" style="padding-left: 30%;">
                        <input type="hidden" name="_method" value="PUT">
                    @else
                    <form method="post" action="{{ route('banner.store') }}" accept-charset="UTF-8" enctype="multipart/form-data" style="padding-left: 30%;">
                    @endif
                        {{ csrf_field() }}


                        <div class="col-xs-12 ace-thumbnails">
                            <label for="" style="display: inline-block;text-align: right;width: 138px; margin-left: 10px;">
                                @if($banner->banner_id)
                                修改轮播图：
                                    @else
                                创建轮播图:
                                @endif
                            </label>
                            <input style="display: inline-block;" name="banner_imgurl" type="file"/>
                        </div>

                        @if($banner->banner_imgurl)
                        <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 10px; margin-left: 85px;">
                            <label for="">轮播图：</label>
                            <a href="{{ $banner->banner_imgurl }}" title="Photo Title" data-rel="colorbox">
                                <img width="200" height="200" alt="200x200" src="{{ $banner->banner_imgurl }}" />
                            </a>
                        </div>
                        @endif


                        <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 10px;">
                            <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 150px;">轮播图跳转链接：</label>
                            <input type="text" name="banner_tourl" value="{{ $banner->banner_tourl }}">
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

