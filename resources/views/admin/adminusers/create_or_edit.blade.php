@extends('admin.layouts.app')
@section('title', $adminuser->id ? '编辑后台角色' : '创建后台角色')
@section('myCss')
    <style>
        ul,li {
            list-style: none;
        }

        .powers {

        }
        .powers li {

        }
        li>ol, li>ul {
            margin-top: 10px;
        }
        .powers .powers_1 {
            float: left;
            position: relative;
            width:156px;
        }
        .powers .powers_1 .parents {
            width: 100px;
            position: absolute;
            top: 0px;
            left: 30px;
        }
        .powers .powers_1 input {
            margin: 0 !important;
            width: 20px;
            height: 20px;
            display: inline-block;
        }
        .s1 {
            height: 40px;
            width: auto;
            position: relative;
        }
        .s1 span {
            left: 30px;
            position: absolute;
            top: -4px;
        }
    </style>
@endsection
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('adminuser.index') }}">用户列表</a>
            </li>
            @if($adminuser->id)
                <li class="active">编辑角色</li>
            @else
                <li class="active">添加角色</li>
            @endif
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入报错信息页面--}}
    @include('admin.layouts._error')
    @include('admin.layouts._message')
    <div class="page-header">
        <h1>
            @if($adminuser->id)
                编辑角色
            @else
                创建角色
            @endif
        </h1>
    </div>
    @if($adminuser->id)
        <form class="form-horizontal" role="form" method="post" action="{{ route('adminuser.update', $adminuser->id) }}">
            <input type="hidden" name="_method" value="PUT">
            @else
                <form class="form-horizontal" role="form" method="post" action="{{ route('adminuser.store') }}">
                    @endif

                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 账号名称 </label>
                        <div class="col-sm-9">
                            <input type="text" name="username" id="form-field-1" placeholder="分类标题" class="col-xs-10 col-sm-5" value="{{ $adminuser->username }}" />
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 绑定手机号 </label>
                        <div class="col-sm-9">
                            <input type="text" name="phone" id="form-field-1" placeholder="分类标题" class="col-xs-10 col-sm-5" value="{{ $adminuser->phone }}" />
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 密码 </label>
                        <div class="col-sm-9">
                            <input type="password" name="password" id="form-field-1" placeholder="重新输入密码" class="col-xs-10 col-sm-5" value="{{ $adminuser->password }}"/>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-4">用户描述</label>

                        <div class="col-sm-9">
                            <textarea name="description" rows="3" cols="50">{{ $adminuser->description }}</textarea>
                        </div>
                    </div>

                    <div class="form-group" style="margin-left: 2rem">
                        <div class="control-group">
                            <label class="control-label bolder blue" style="margin-bottom: 10px;">
                                @if($adminuser->id)
                                    <font style="vertical-align: inherit;">重新分配权限</font>
                                @else
                                    <font style="vertical-align: inherit;">分配权限</font>
                                @endif
                            </label>
                            <ul class="powers">\
                                @foreach($permissions->where('parents_id', 0) as $v)
                                    <li class="powers_1">
                                        <input type="checkbox" name="permission_id[]" {{ in_array($v->id, $data) ? 'checked' : '' }} value="{{ $v->id }}"/>
                                        <span class="parents">
                                        {{ $v->name }}
                                    </span>
                                        <ul>
                                            @foreach($permissions->where('parents_id', $v->id) as $p)
                                                <li class="s1">
                                                    <input type="checkbox" name="permission_id[]" {{ in_array($p->id, $data) ? 'checked' : '' }} value="{{ $p->id }}" />
                                                    <span>
                                                    {{ $p->name }}
                                                </span>
                                                </li>
                                                @foreach($permissions->where('parents_id', $p->id) as $i)
                                                    <li class="s1">
                                                        <input type="checkbox" name="permission_id[]" {{ in_array($i->id, $data) ? 'checked' : '' }} value="{{ $i->id }}" />
                                                        <span>
                                                    {{ $i->name }}
                                                </span>
                                                    </li>
                                                @endforeach
                                            @endforeach
                                        </ul>

                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>



                    <div class="clearfix form-actions">

                        <label class="block clearfix" style="margin-left: 23%">
                            {{$adminPhone}}
                            <div style="height: 30px;">

                                <input type="text" name="code" class="code" placeholder="请输入管理员验证码" style="display: inline-block; height: 42px; width:150px;">
                                <button class="btn btn-primary" id="seed" type="button" style="position: relative; width:135px;">发送验证码</button>
                            </div>
                        </label>
                        <div class="space-4"></div>
                        <div class="space-4"></div>
                        <div class="space-4"></div>
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn btn-info submit">
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
                <script src="http://code.jquery.com/jquery-1.9.0.min.js"></script>
                <script type="text/javascript">
                    $(function () {
                        $(".powers input").click(function () {
                            let lists = $(this).next();
                            let onlists = $(this).prop("checked");

                            // 父类的全选和反选
                            if (lists) {
                                if (onlists) {
                                    lists.parent().find("input").prop("checked","checked");
                                } else {
                                    lists.parent().find("input").removeProp("checked");
                                }
                            }

                            // 同级分类当前选中的个数
                            let checkCount = $(this).parent().parent().find(">li>input:checked").length;
                            // 同级分类总的个数
                            let count = $(this).parent().parent().find(">li>input").length;

                            // 子选中父跟着选中，子取消父跟着取消
                            if (checkCount) {
                                $(this).parent().parent().parent().find(">input").prop("checked","checked");
                            }else {
                                $(this).parent().parent().parent().find(">input").removeProp("checked");
                            }
                        })
                    })
                </script>

                <script>
                    $(function () {
                        var btnDisable = false;//发送按钮默认禁用

                        if (btnDisable) {
                            return;
                        }

                        $("#seed").click(function () {
                            $.get("/admin/sendCodeSMS?username=" + 'admin' + '&czuser=' + '{{ \Auth::guard('web')->user()->username }}' + '&des=修改后台用户的权限', function (result) {
                                if (result.status_code != 200){
                                    alert(result.message);
                                }else {
                                    timeWait(300);
                                    btnDisable = true;
                                }

                            })
                        });

                        function timeWait(time) {
                            setTimeout(function() {
                                if (time >= 0) {
                                    $("#seed").html(time + "s后重试");
                                    time--;
                                    timeWait(time);
                                    $("#seed").attr("disabled", true)
                                } else {
                                    $("#seed").html("发送");
                                    $("#seed").removeAttr("disabled")
                                    btnDisable = false;
                                }
                            }, 1000)
                        }

                        $('.submit').click(function () {
                            if ($('.code').val() == ''){alert('请输入验证码');return false;}
                        })
                    })
                </script>
@endsection