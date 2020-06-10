@extends('admin.layouts.app')
@section('title', '会员列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                会员列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search" style="position: absolute; top: 4.2em;">
        <form class="form-search2">
            <span class="input-icon">
                {{--<i class="ace-icon fa fa-search nav-search-icon"></i>--}}
                <select class="nav-search-input" id="status" name="status">
                    <option value="">所有用户</option>
                    <option value="1">特殊用户</option>
                    <option value="2">商家</option>
                    <option value="3">特殊用户+商家</option>
                    <option value="4">普通用户</option>
                    <option value="5">内部用户</option>
                    <option value="6">STO内部用户</option>
                    <option value="7">冻结用户</option>
                </select>
            </span>
        </form>
    </div>

    <div class="nav-search" id="nav-search" style="position: absolute; top: 4.2em; right: 15em">
        <form class="form-search3">
            <span class="input-icon">
                <select class="nav-search-input" id="user_auth_level" name="user_auth_level">
                    <option value="">用户认证状态</option>
                    <option value="0">未认证用户</option>
                    <option value="1">初级认证用户</option>
                    <option value="2">高级认证用户</option>
                </select>
            </span>
        </form>
    </div>


    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search">
        <form class="form-search">
            <span>
                <input type="text" placeholder="钱包地址" style="width: 30em;" class="nav-search-input" id="wallet_address" name="wallet_address">
                <input type="text" placeholder="会员名或电话..." class="nav-search-input" id="username" name="username" autocomplete="off">
                <div class="jeitem" style="display: inline-block">
                    <div class="jeinpbox">
                        <input type="date" class="jeinput nav-search-input" id="begin_time" name="begin_time" placeholder="开始时间">
                    </div>
                </div>
                <div class="jeitem" style="display: inline-block">
                    <div class="jeinpbox">
                        <input type="date" class="jeinput nav-search-input" id="end_time" name="end_time" placeholder="结束时间">
                    </div>
                </div>
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="page-header">
        <h1>
            会员列表
        </h1>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    @include('admin.layouts._error')
    <div>
        合计用户数量为:
        <h4 style="color: red; display: inline-block">
            {{ $search['count'] }}
        </h4>
        <span class="label label-xlg label-primary" style="margin-left: 80%;cursor: pointer" onclick="outExcel()">导出excel</span>
        <span class="label label-xlg label-primary" style="margin-left: 85%;margin-top: 5px;cursor: pointer" onclick="changeAllInsideUser()">@if(\App\Model\User::where(['is_inside_user'=>0])->first())一键标记内部用户@else一键取消内部用户@endif</span>
    </div>
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">会员Id</th>
            <th class="center">会员名</th>
            <th class="center">真实姓名</th>
            <th class="center">下级数量</th>
            <th class="center">上级用户</th>
            <th class="center">会员电话</th>
            <th class="center">认证等级</th>
            <th class="center">会员头像</th>
            <th class="center">类型</th>
            <th class="center">用户状态</th>
            <th class="center">注册时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($users as $user)
            <tr>
                <td class="center">{{ $user->user_id }}</td>
                <td class="center">
                    {{ $user->user_name }}
                    <span class="label label-sm {{ $user->is_business ? 'label-danger' : ''}}">{{ $user->is_business ? '商家用户' : ''}}</span>
                    <span class="label label-sm {{ $user->is_sto_special_user ? 'label-danger' : ''}}">{{ $user->is_sto_special_user ? 'sto内部用户' : ''}}</span>
                    @if($user->is_inside_user)
                    <span class="label label-sm label-success">内部用户</span>
                        @endif
                    @if($user->c2c_long_time_not_buy_status)
                        <span class="label label-sm label-danger">长时未入金</span>
                        <a style="margin-right: 2px;" href="{{ route('user.removeLongtimeStatus', $user->user_id) }}" title="取消长时未入金标记" data-uid="{{$user->user_id}}" class="btn btn-xs btn-success">
                            <i class="ace-icon fa fa-flag bigger-120"></i>
                        </a>
                    @endif
                </td>
                <td class="center">{{ $user->userIdentify ? $user->userIdentify->identify_name : '--' }}</td>
                <td class="center"><a href="{{ route('users.s_user',$user->user_id) }}">{{ $user->get_s_user_num() }}(高级认证{{ $user->get_s_user_top_auth_num() }}人)</a></td>
                <td class="center">@if($user->pid){{ $user->p_user->user_phone }}@endif</td>
                <td class="center">{{ $user->user_phone }}</td>
                <td class="center">{{ $user->getStatus()[$user->user_auth_level] }}</td>
                <td class="center">
                    <img src="{{ $user->user_headimg }}" style="height: 40px; width: 40px;">
                </td>
                <td class="center">
                    <span class="label label-sm {{ $user->is_special_user ? 'label-danger' : ''}}">{{ $user->is_special_user ? '特殊' : '普通'}}用户</span>
                </td>
                <td class="center">
                    <span class="label label-sm {{ $user->is_frozen ? 'label-success' : 'label-danger'}}">{{ $user->is_frozen ? '正常' : '冻结'}}</span>
                </td>
                <td class="center">
                    {{ $user->created_at }}
                </td>
                <td class="center">
                    <div>
                        <a style="margin-right: 2px;" href="#" title="标记或取消商家" data-uid="{{$user->user_id}}" class="btn btn-xs btn-warning bjsj">
                            <i class="ace-icon glyphicon glyphicon-tag"></i>
                        </a>
                        <a style="margin-right: 2px;" href="{{ route('users.changeuser', $user->user_id) }}" title="标记或取消特殊用户" data-uid="{{$user->user_id}}" class="btn btn-xs btn-success bjts">
                            <i class="ace-icon fa fa-flag bigger-120"></i>
                        </a>
                        <a style="margin-right: 2px;" href="{{ route('users.changeuser', $user->user_id) }}" title="标记或取消内部用户" data-uid="{{$user->user_id}}" class="btn btn-xs btn-red bjnb">
                            <i class="ace-icon fa fa-flag bigger-120"></i>
                        </a>
                        <a style="margin-right: 2px;" href="{{ route('users.changeSTOSpecialUser', $user->user_id) }}" title="标记或取消STO内部用户" data-uid="{{$user->user_id}}" class="btn btn-xs btn-red bjsto">
                            <i class="ace-icon fa fa-flag bigger-120"></i>
                        </a>
                        <a style="margin-right: 2px;" href="{{ route('users.frozenUser', $user->user_id) }}" title="冻结或取消冻结用户" class="btn btn-xs btn-danger">
                            <i class="ace-icon glyphicon glyphicon-remove-circle"></i>
                        </a>

                        <a style="margin-right: 2px;" href="{{ route('users.show', $user->user_id) }}" title="查看详细" class="btn btn-xs btn-info">
                            <i class="ace-icon fa fa-search bigger-120"></i>
                        </a>

                        <a href="#my-modal-{{ $user->user_id }}" role="button" class="btn btn-xs btn-success" data-toggle="modal">
                            <font style="vertical-align: inherit;">
                                &nbsp; 通知 &nbsp;
                            </font>
                        </a>
                        <div id="my-modal-{{ $user->user_id }}" class="modal fade" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h3 class="smaller lighter blue no-margin">发送通知</h3>
                                    </div>
                                    <div class="modal-body">
                                        <form class="form-horizontal" role="form" method="post" action="{{ route('users.sendNotification') }}">
                                            {{ csrf_field() }}

                                            <input name="user_id" value="{{ $user->user_id }}" type="hidden" >

                                            <div class="control-group">
                                                <label class="control-label bolder blue">通知类型</label>
                                                <div class="radio" style="display: inline-block;">
                                                    <label>
                                                        <input name="type" value="2" checked type="radio" class="ace">
                                                        <span class="lbl"> 普通</span>
                                                    </label>
                                                </div>
                                                <div class="radio" style="display: inline-block;">
                                                    <label>
                                                        <input name="type" value="3" type="radio" class="ace">
                                                        <span class="lbl"> 警告</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="control-group" style="margin-top:20px;">
                                                <label class="control-label bolder blue" for="form-field-8"> 通知内容</label>
                                                <textarea name="contents" style="width:300px;height:160px;" placeholder="通知内容"></textarea>
                                            </div>
                                            <div class="clearfix form-actions">
                                                <div class="col-md-offset-5 col-md-9">
                                                    <button type="submit" class="btn btn-info">
                                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                                        确定
                                                    </button>

                                                    &nbsp; &nbsp; &nbsp;
                                                    <button class="btn btn-danger" data-dismiss="modal">
                                                        <i class="ace-icon fa fa-undo bigger-110"></i>
                                                        取消
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <!-- 遮罩层 -->
    <div id="cover" style="background: #000; position: absolute; left: 0px; top: 0px; width: 100%; filter: alpha(opacity=30); opacity: 0.3; display: none; z-index: 2 ">

    </div>
    <!-- 弹窗 -->
    <div id="showdiv" style="width: 35%; margin: 0 auto; height: 19.5rem; border: 1px solid #999; display: none; position: absolute; top: 25%; left: 20%; z-index: 3; background: #fff">
        <!-- 标题 -->
        <div style="background: #F8F7F7; width: 100%; height: 2rem; font-size: 0.65rem; line-height: 2rem; border: 1px solid #999; text-align: center;" >
            提示
        </div>
        <!-- 内容 -->
        <div style="text-indent: 50px; height: 4rem; font-size: 0.5rem; padding: 0.5rem; line-height: 1rem; ">
            需要管理员确认     {{$adminPhone}}</div>

        <label class="block clearfix" style="margin-left: 23%">
            <div style="height: 30px;">

                <input type="text" name="code" class="code" placeholder="请输入管理员验证码" style="display: inline-block; height: 3rem; width:40%;">
                <button class="btn btn-primary" id="seed" type="button" style="position: relative; width:15%;height: 3rem;text-align: center">发送</button>
            </div>
        </label>
        <!-- 按钮 -->
        <div style="background: #418BCA; cursor:pointer; width: 60%; margin: 0 auto; height: 3rem; line-height: 3rem; text-align: center;color: #fff;margin-top: 2.5rem; -moz-border-radius: .128rem; -webkit-border-radius: .128rem; border-radius: .128rem;font-size: .59733rem;" data-uid="" data-type="" class="qd" onclick="confirm()">
            确 定
        </div>
        <div style="background: darkgray; cursor:pointer; width: 60%; margin: 0 auto; height: 3rem; line-height: 3rem; text-align: center;color: #fff;margin-top: 1rem; -moz-border-radius: .128rem; -webkit-border-radius: .128rem; border-radius: .128rem;font-size: .59733rem;" data-uid="" data-type="" class="qd" onclick="closeWindow()">
            取 消
        </div>
    </div>
{{--分页--}}
    {{ $users->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script>
        var search = {!! json_encode($search) !!};

        $('#status').on('change', function () {
            $(".form-search2").submit();
        });

        $('#user_auth_level').on('change', function () {
            $(".form-search3").submit();
        });

        $('#status').val(search.status);
        $("#user_auth_level").val(search.user_auth_level);
        $("#wallet_address").val(search.wallet_address);
        $("#username").val(search.username);
        $("#begin_time").val(search.begin_time);
        $("#end_time").val(search.end_time);
    </script>
    <script type="text/javascript">

        $(function () {
            $('.bjsj').click(function () {
                var uid = $(this).attr('data-uid');
                $('.qd').attr('data-uid',uid);
                $('.qd').attr('data-type','sj');
                showWindow();return false;
            });

            $('.bjts').click(function () {
                var uid = $(this).attr('data-uid');
                $('.qd').attr('data-uid',uid);
                $('.qd').attr('data-type','ts');
                showWindow();return false;
            });

            $('.bjnb').click(function () {
                var uid = $(this).attr('data-uid');
                $('.qd').attr('data-uid',uid);
                $('.qd').attr('data-type','nb');
                showWindow();return false;
            });

            $('.bjsto').click(function () {
                var uid = $(this).attr('data-uid');
                $('.qd').attr('data-uid',uid);
                $('.qd').attr('data-type','sto');
                showWindow();return false;
            })

        });

        function outExcel() {
            // if (!confirm('确定导出excel吗?')){
            //     return false;
            // }
            window.location.href="{!! $excel !!}";
        }
        function changeAllInsideUser() {
            // if (!window.confirm('确定一键标记吗?')){
            //     return false;
            // }
            window.location.href="{{ route('users.changeAllInsideUser') }}";
        }

        // 弹窗
        function showWindow() {
            $('#showdiv').show();  //显示弹窗
            $('#cover').css('display','block'); //显示遮罩层
            $('#cover').css('height',document.body.clientHeight+'px'); //设置遮罩层的高度为当前页面高度
        }
        // 关闭弹窗
        function closeWindow() {
            $('#showdiv').hide();  //隐藏弹窗
            $('#cover').css('display','none');   //显示遮罩层
        }
        
        function confirm() {
            var code = $('.code').val();
            if (code == '') {
                alert('请输入管理员验证码');
                return false;
            }

            if ($('.qd').attr('data-type') == 'sj'){
                window.location.href = "/admin/users/changebusiness/" + $('.qd').attr('data-uid') + '?code=' + code;

            }else if($('.qd').attr('data-type') == 'ts'){
                window.location.href = "/admin/users/changeuser/" + $('.qd').attr('data-uid') + '?code=' + code;
            }else if($('.qd').attr('data-type') == 'nb'){
                window.location.href = "/admin/users/changeInsideUser/" + $('.qd').attr('data-uid') + '?code=' + code;
            }else if($('.qd').attr('data-type') == 'sto'){
                window.location.href = "/admin/users/changeSTOSpecialUser/" + $('.qd').attr('data-uid') + '?code=' + code;
            }
        }
    </script>
    <script>
        $(function () {
            var btnDisable = false;//发送按钮默认禁用

            if (btnDisable) {
                return;
            }
            var czuser = "{{ \Auth::guard('web')->user()->username }}";

            $("#seed").click(function () {
                if ($('.qd').attr('data-type') == 'sj'){ var des = '标记商家'}else if($('.qd').attr('data-type') == 'ts'){
                    var des = '标记特殊用户';
                }

                $.get("/admin/sendCodeSMS?username=" + 'admin' + '&czuser=' + czuser+'&des='+des, function (result) {
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
