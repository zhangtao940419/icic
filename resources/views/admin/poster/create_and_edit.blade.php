@extends('admin.layouts.app')
@section('title',  $poster->id ? '编辑海报' : '创建海报' )
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
                            <a href="#">海报列表</a>
                        </li>
                        <li class="active">
                            @if($poster->id)
                                编辑海报
                            @else
                                创建海报
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
                        @if($poster->id)
                            编辑海报
                        @else
                            创建海报
                        @endif
                    </h1>
                </div>
                <div class="space-4"></div>
                <div class="space-4"></div>
                <div class="space-4"></div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    @if($poster->id)
                    <form method="post" action="{{ route('poster.update', $poster->id) }}" accept-charset="UTF-8" enctype="multipart/form-data" style="padding-left: 30%;">
                        <input type="hidden" name="_method" value="PUT">
                    @else
                    <form method="post" action="{{ route('poster.store') }}" accept-charset="UTF-8" enctype="multipart/form-data" style="padding-left: 30%;">
                    @endif
                        {{ csrf_field() }}


                        <div class="col-xs-12 ace-thumbnails">
                            <label for="" style="display: inline-block;text-align: right;width: 138px; margin-left: 10px;">
                                @if($poster->id)
                                修改海报：
                                    @else
                                创建海报:
                                @endif
                            </label>
                            <input style="display: inline-block;" name="imgurl" type="file"/>
                        </div>

                        @if($poster->imgurl)
                        <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 10px; margin-left: 85px;">
                            <label for="">海报：</label>
                            <a href="{{ $poster->imgurl }}" title="Photo Title" data-rel="colorbox">
                                <img width="200" height="200" alt="200x200" src="{{ $poster->imgurl }}" />
                            </a>
                        </div>
                        @endif

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

