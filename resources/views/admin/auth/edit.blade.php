@extends('admin.layouts.app')
@section('content')

<div class="main-container ace-save-state" id="main-container">
    <div class="main-content">
        <div class="main-content-inner">
            <!-- 内容导航 s -->
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="#">控制中心</a>
                    </li>

                    <li>
                        <a href="{{ route('authentication.index') }}">用户高级认证列表</a>
                    </li>
                    <li class="active">用户认证中心</li>
                </ul>
            </div>
            <!-- 内容导航 e -->

            <div class="space-4"></div>

            <div class="page-header">
                <h1>
                    申请高级认证列表
                </h1>
                <!-- add reset s -->
            </div>
            <div class="space-4"></div>
            <div class="space-4"></div>
            <div class="space-4"></div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <form method="post" action="{{ route('authentication.update', $authentication->identify_id) }}" style="margin-left: 30%;">
                    <input type="hidden" name="_method" value="PUT">
                    {{ csrf_field() }}
                    <div class="col-xs-12" style="margin-bottom: 8px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 138px; margin-top: 5px;">申请高级用户名称：</label>
                        <input disabled type="text" name="user_name" value="{{ $authentication->user->user_name}}">
                    </div>

                    <div class="col-xs-12" style="margin-bottom: 8px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 138px; margin-top: 5px;">所属地区：</label>
                        <input disabled type="text" name="user_name" value="{{ $authentication->user_identify_area->area_name}}">
                    </div>

                    <div class="col-xs-12" style="margin-bottom: 8px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 138px; margin-top: 5px;">证件类型：</label>
                        <input disabled type="text" name="user_name" value="{{ $authentication->user_identify_area->identify_name}}">
                    </div>

                    <div class="col-xs-12" style="margin-bottom: 8px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 138px; margin-top: 5px">证件姓名：</label>
                        <input disabled type="text" name="identify_name" value="{{ $authentication->identify_name}}">
                    </div>

                    <div class="col-xs-12" style="margin-bottom: 8px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 138px; margin-top: 5px">证件号码：</label>
                        <input disabled type="text" name="identify_card" value="{{ $authentication->identify_card}}">
                    </div>

                    <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 10px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 150px;">证件正面：</label>
                        <a href="{{ $authentication->identify_card_z_img }}" title="Photo Title" data-rel="colorbox">
                            <img width="200" height="200" alt="200x200" src="{{ $authentication->identify_card_z_img }}" />
                        </a>
                        <input type="hidden" name="identify_card_z_img" value="{{ $authentication->identify_card_z_img }}">
                    </div>

                    <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 10px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 150px;">证件反面：</label>
                        <a href="{{ $authentication->identify_card_f_img }}" title="Photo Title" data-rel="colorbox">
                            <img width="200" height="200" alt="200x200" src="{{ $authentication->identify_card_f_img }}" />
                        </a>
                        <input type="hidden" name="identify_card_f_img" value="{{ $authentication->identify_card_f_img }}">
                    </div>

                    <div class="col-xs-12 ace-thumbnails clearfix" style="margin-top: 10px;margin-bottom: 30px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 150px;">手持证件照：</label>
                        <a href="{{ $authentication->identify_card_h_img }}" title="Photo Title" data-rel="colorbox">
                            <img width="200" height="200" alt="200x200" src="{{ $authentication->identify_card_h_img }}" />
                        </a>
                        <input type="hidden" name="identify_card_h_img" value="{{ $authentication->identify_card_h_img }}">
                    </div>

                    <div class="col-xs-12" style="margin-bottom: 8px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 138px; margin-top: 5px;">审核状态：</label>
                        <input disabled type="text" name="user_name" value="{{ $authentication->getstatu()[$authentication->status]}}">
                    </div>
                    @if($authentication->status == 3)
                    <div class="col-xs-12" style="margin-bottom: 8px;">
                        <label for="" style="display: inline-block;text-align: right;vertical-align: top;width: 138px; margin-top: 5px;">驳回理由：</label>
                        <input disabled type="text" name="user_name" value="{{ $authentication->refuse_reason}}">
                    </div>
                    @endif
                    @if($authentication->status == 1)
                    <p style="margin-left: 100px;" >
                        <button class="btn btn-primary">
                            <i class="ace-icon fa fa-check"></i>
                            同意认证
                        </button>
                    </p>
                    @endif
                </form>
                @if($authentication->status == 1)
                <form style="display: inline-block; position: relative; left: 60em; bottom: 4em" action="{{ route('authentication.destroy', $authentication->identify_id) }}" method="post">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}

                    <span class="input-icon nav-search">
                {{--<i class="ace-icon fa fa-search nav-search-icon"></i>--}}
                        <select class="nav-search-input" id="refuse_reason" name="refuse_reason">
                        <option value="0">若拒绝请选择理由</option>
                        <option value="图片不清晰">图片不清晰</option>
                        <option value="信息不符">信息不符</option>
                            <option value="1">自定义</option>
                        </select>
                    </span>
                    <span class="input-icon nav-search">
                        <input name="zdy_reason" hidden id="zdy_reason" class="nav-search-input">
                    </span>

                    <button class="btn btn-danger" id="refuse">
                        <i class="ace-icon glyphicon glyphicon-remove"></i>
                        撤回
                    </button>

                </form>
                @endif
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
    <script src="http://code.jquery.com/jquery-1.9.0.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $("#refuse_reason").click(function () {
                    status = $(this).val();
                    if (status == 1){
                        // alert(status);
                        $('#zdy_reason').removeProp('hidden');
                    }else {
                        $('#zdy_reason').prop('hidden','hidden');
                    }


            })

            $('#refuse').click(function () {
                status = $("#refuse_reason").val();
                if (status == 0){alert('请选择拒绝理由'); return false}

                if (status == 1){
                    if ($('#zdy_reason').val() == ''){
                        alert('请选择理由');return false;
                    }
                }

            })
        })
    </script>
@endsection

