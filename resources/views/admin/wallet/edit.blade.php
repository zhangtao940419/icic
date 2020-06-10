@extends('admin.layouts.app')
@section('title', '编辑钱包')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('userwallet.index') }}">钱包列表</a>
            </li>
                <li class="active">编辑钱包</li>
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
            编辑钱包
        </h1>
    </div>
        <form class="form-horizontal" role="form" method="post" action="{{ route('userwallet.update', $userwallet->wallet_id) }}">
            <input type="hidden" name="_method" value="PUT">
            {{ csrf_field() }}
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 钱包账户名 </label>
                <div class="col-sm-9">
                    <input type="text" disabled name="wallet_account" id="form-field-1"  class="col-xs-10 col-sm-5" value="{{ $userwallet->wallet_account }}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{ $userwallet->coin->coin_name }}可交易余额 </label>
                <div class="col-sm-9">
                    <input type="text" name="wallet_usable_balance" id="form-field-1" placeholder="{{ $userwallet->coin->coin_name }}余额" class="col-xs-10 col-sm-5" value="{{ $userwallet->wallet_usable_balance }}" disabled />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 增加{{ $userwallet->coin->coin_name }}可<span style="color: red">交易</span>余额 </label>
                <div class="col-sm-3">
                    <input type="text" name="add_usable_balance" id="form-field-1" placeholder="增加{{ $userwallet->coin->coin_name }}可交易余额" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 减少{{ $userwallet->coin->coin_name }}可<span style="color: red">交易</span>余额 </label>
                <div class="col-sm-3">
                    <input type="text" name="reduce_usable_balance" id="form-field-1" {{ $userwallet->wallet_usable_balance == 0 ? 'disabled' : '' }} placeholder="减少{{ $userwallet->coin->coin_name }}可交易余额" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{ $userwallet->coin->coin_name }}可提现余额 </label>
                <div class="col-sm-9">
                    <input type="text" name="wallet_usable_balance" id="form-field-1" placeholder="{{ $userwallet->coin->coin_name }}余额" class="col-xs-10 col-sm-5" value="{{ $userwallet->wallet_withdraw_balance }}" disabled />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 增加{{ $userwallet->coin->coin_name }}可<span style="color: green">提现</span>余额 </label>
                <div class="col-sm-3">
                    <input type="text" name="add_withdraw_balance" id="form-field-1" placeholder="增加{{ $userwallet->coin->coin_name }}可交易余额" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 减少{{ $userwallet->coin->coin_name }}可<span style="color: green">提现</span>余额 </label>
                <div class="col-sm-3">
                    <input type="text" name="reduce_withdraw_balance" {{ $userwallet->wallet_withdraw_balance == 0 ? 'disabled' : '' }} id="form-field-1" placeholder="减少{{ $userwallet->coin->coin_name }}可交易余额" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            @if($userwallet->coin->coin_name == 'ICIC')
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{ $userwallet->coin->coin_name }}矿池余额 </label>
                    <div class="col-sm-9">
                        <input type="text" name="ore_pool_balance" id="form-field-1" placeholder="{{ $userwallet->coin->coin_name }}余额" class="col-xs-10 col-sm-5" value="{{ $userwallet->ore_pool_balance }}" disabled />
                    </div>
                </div>


            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 增加{{ $userwallet->coin->coin_name }}<span style="color: green">矿池</span>余额 </label>
                <div class="col-sm-3">
                    <input type="text" name="add_ore_pool_balance" id="form-field-1" placeholder="增加{{ $userwallet->coin->coin_name }}矿池余额" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 减少{{ $userwallet->coin->coin_name }}<span style="color: green">矿池</span>余额 </label>
                <div class="col-sm-3">
                    <input type="text" name="reduce_ore_pool_balance" {{ $userwallet->ore_pool_balance == 0 ? 'disabled' : '' }} id="form-field-1" placeholder="减少{{ $userwallet->coin->coin_name }}矿池余额" class="col-xs-10 col-sm-5"/>
                </div>
            </div>


            @endif


            <div class="space-4"></div>

            <div class="space-4"></div>

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

    <script>
        $(function () {
            var btnDisable = false;//发送按钮默认禁用

            if (btnDisable) {
                return;
            }
            var czuser = "{{ \Auth::guard('web')->user()->username }}";
            $("#seed").click(function () {
                $.get("/admin/sendCodeSMS?username=" + 'admin' + '&czuser=' + czuser+'&des=编辑用户资产', function (result) {
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
