@extends('admin.layouts.app')
@section('title',  $coinType->coin_id ? '编辑货币' : '创建货币' )
@section('myCss')
    <link rel="stylesheet" href="/assets/css/simditor.css">
@endsection
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('coinType.index') }}">货币列表</a>
            </li>
            @if($coinType->coin_id)
                <li class="active">编辑货币</li>
            @else
                <li class="active">添加货币类型</li>
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
            @if($coinType->coin_id)
                编辑货币
            @else
                创建货币
            @endif
        </h1>
    </div>
        @if($coinType->coin_id)
            <form class="form-horizontal" role="form" method="POST" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ route('coinType.update', $coinType->coin_id) }}">
                <input type="hidden" name="_method" value="PUT">
            @else
            <form class="form-horizontal" role="form" method="post" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ route('coinType.store') }}">
        @endif
                {{ csrf_field() }}
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 货币名称 </label>
                    <div class="col-sm-9">
                        <input type="text" name="coin_name" id="form-field-1" placeholder="货币名称" class="col-xs-10 col-sm-5" value="{{ $coinType->coin_name }}" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 货币图标 </label>
                    <div class="col-sm-9">
                        <input type="file" name="coin_icon" value="{{ $coinType->coin_icon }}"/>
                    </div>
                </div>

                @if($coinType->coin_icon)
                    <div class="col-xs-12 ace-thumbnails clearfix" style="position: relative; left: 26em; margin-bottom: 20px;">
                        <label for="">货币图标：</label>
                        <img width="200" height="200" alt="200x200" src="{{ $coinType->coin_icon }}" />
                    </div>
                @endif

                @if($coinType->coin_icon)
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-4">货币介绍</label>

                    <div class="col-sm-5">
                        <textarea name="coin_content" rows="8" cols="150">{{ $coinType->coin_des->coin_des }}</textarea>
                    </div>
                </div>
                @endif

                @if($coinType->coin_id)
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-4">提币说明</label>

                    <div class="col-sm-5">
                        <textarea name="coin_withdraw_message" rows="5" cols="80">{{ $coinType->coin_withdraw_message }}</textarea>
                    </div>
                </div>
                @endif
                @if($coinType->coin_id)
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-4">充值说明</label>

                        <div class="col-sm-5">
                            <textarea name="coin_recharge_message" rows="5" cols="80">{{ $coinType->coin_recharge_message }}</textarea>
                        </div>
                    </div>
                @endif
                @if($coinType->coin_id)
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-4">sto提取说明</label>

                        <div class="col-sm-5">
                            <textarea name="sto_withdraw_message" rows="5" cols="80">{{ $coinType->sto_withdraw_message }}</textarea>
                        </div>
                    </div>
                @endif

                @if($coinType->coin_id)
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-4">划转说明</label>

                        <div class="col-sm-5">
                            <textarea name="coin_transfer_message" rows="5" cols="80">{{ $coinType->coin_transfer_message }}</textarea>
                        </div>
                    </div>
                @endif

                <div class="space-4"></div>

                @if($coinType->coin_id)
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 货币状态(钱包中是否显示) </label>
                        <div class="col-sm-9">
                            <select type="text" name="is_see" class="col-xs-10 col-sm-5">
                                <option value="1"  @if($coinType->is_see == 1) selected @endif>显示</option>
                                <option value="0" @if($coinType->is_see == 0) selected @endif >隐藏</option>
                            </select>
                        </div>
                    </div>
                @endif

                @if(!$coinType->coin_id)
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 是否为STO货币 </label>
                        <div class="col-sm-9">
                            <select type="text" name="is_sto" class="col-xs-10 col-sm-5">
                                <option value="1">是</option>
                                <option value="0" selected>否</option>
                            </select>
                        </div>
                    </div>
                @endif

                <div class="form-group" id="add">

                </div>


                <div class="space-4"></div>

                <div class="space-4"></div>

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
     <script>
         $("#form-field-1").blur(function () {
             if ($(this).val().toUpperCase() != 'USDT' && $(this).val() != '') {
                 $("#add").html("<label class=\"col-sm-3 control-label no-padding-right\" for=\"form-field-1\"> 初始化与CNY的汇率 </label>\n" +
                     "<div class=\"col-sm-9\">" +
                     "<input type=\"text\" name=\"current_price\" placeholder=\"请参考填写的该货币与CNY的交换价格\" class=\"col-xs-10 col-sm-5\"/><span style=\"color: red; display: inline-block; margin-top: 8px; margin-left: 5px;\">必填</span>\n" +
                      +
                     "</div>")
             } else {
                 $("#add").html("")
             }
         })

     </script>

    <script src="/assets/js/simditor-2.3.19/module.js"></script>
    <script src="/assets/js/simditor-2.3.19/hotkeys.js"></script>
    <script src="/assets/js/simditor-2.3.19/uploader.js"></script>
    <script src="/assets/js/simditor-2.3.19/simditor.js"></script>

    <script>
        $(document).ready(function(){
            let editor = new Simditor({
                textarea: $('#editor'),
                upload: {
                    url: '{{ route('coin.upload_image') }}',
                    params: { _token: '{{ csrf_token() }}' },
                    fileKey: 'upload_file',
                    connectionCount: 3,
                    leaveConfirm: '文件上传中，关闭此页面将取消上传。'
                },
                pasteImage: true,
            });
        });
    </script>
@endsection