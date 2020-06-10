@extends('admin.layouts.app')
@section('title',  $coinfee->id ? '编辑费率' : '创建费率' )
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
                            <a href="#">所有费率列表</a>
                        </li>
                        <li class="active">
                            @if($coinfee->id)
                                编辑费率
                            @else
                                创建费率
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
                        @if($coinfee->id)
                            编辑费率
                        @else
                            创建费率
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
            @if($coinfee->id)
                <form class="form-horizontal" role="form" method="post" action="{{ route('coinfees.update', $coinfee->id) }}">
                    <input type="hidden" name="_method" value="PUT">
                    @else
                        <form class="form-horizontal" role="form" method="post" action="{{ route('coinfees.store') }}">
                            @endif

                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 兑换的货币类型: </label>
                                <div class="col-sm-9">
                                    <select name="coin_id" id="form-field-1" class="col-xs-10 col-sm-5" >
                                        <option value="">请选择</option>
                                        @foreach($coins as $coin)
                                            <option value="{{ $coin->coin_id }}" {{ $coinfee->coin_id == $coin->coin_id ? 'selected' : '' }}>{{ $coin->coin_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 以太坊中央钱包的交易gaslimit: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="eth_gaslimit" id="form-field-1" placeholder="以太坊中央钱包的交易gaslimit" class="col-xs-10 col-sm-5" value="{{ $coinfee->eth_gaslimit }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 以太坊gasprice: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="eth_gasprice" id="form-field-1" placeholder="以太坊gasprice" class="col-xs-10 col-sm-5" value="{{ $coinfee->eth_gasprice }}" />
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 提币的最小值: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="withdraw_min" id="form-field-1" placeholder="提币的最小值" class="col-xs-10 col-sm-5" value="{{ $coinfee->withdraw_min }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 提币的最大值: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="withdraw_max" id="form-field-1" placeholder="提币的最大值" class="col-xs-10 col-sm-5" value="{{ $coinfee->withdraw_max }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 最小的充值数量: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="recharge_min" id="form-field-1" placeholder="最小的充值数量" class="col-xs-10 col-sm-5" value="{{ $coinfee->recharge_min }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 提币手续费/固定费用模式: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="fixed_fee" id="form-field-1" placeholder="固定的费用" class="col-xs-10 col-sm-5" value="{{ $coinfee->fixed_fee }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 提币手续费/百分比费用模式: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="percent_fee" id="form-field-1" placeholder="百分比的费用" class="col-xs-10 col-sm-5" value="{{ $coinfee->percent_fee }}" />(1-100之内的数字)
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 提币手续费模式选择: </label>
                                <div class="col-sm-9">
                                    <select name="fee_type" id="form-field-1" class="col-xs-10 col-sm-5" >
                                        <option value="">请选择</option>
                                        <option value="1" {{ $coinfee->fee_type == 1 ? 'selected' : '' }}>固定费用</option>
                                        <option value="2" {{ $coinfee->fee_type == 2 ? 'selected' : '' }}>百分比费用</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 场内交易所需的矿池余额: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="ore_pool_min" id="form-field-1" placeholder="场内交易所需的矿池余额" class="col-xs-10 col-sm-5" value="{{ $coinfee->ore_pool_min }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 场内交易买单到账锁定时间间隔: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="inside_transfer_lock_time" id="form-field-1" placeholder="场内交易买单到账锁定时间间隔" class="col-xs-10 col-sm-5" value="{{ $coinfee->inside_transfer_lock_time }}" />
                                    (单位:分钟)
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 资金划转手续费(%): </label>
                                <div class="col-sm-9">
                                    <input type="text" name="transfer_fee" id="form-field-1" placeholder="资金划转手续费" class="col-xs-10 col-sm-5" value="{{ $coinfee->transfer_fee }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 场内买单矿池释放比率(%): </label>
                                <div class="col-sm-9">
                                    <input type="text" name="ore_pool_free_rate" id="form-field-1" placeholder="矿池释放比率" class="col-xs-10 col-sm-5" value="{{ $coinfee->ore_pool_free_rate }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 互链转入矿池翻的倍数: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="to_ore_pool_times" id="form-field-1" placeholder="矿池释放比率" class="col-xs-10 col-sm-5" value="{{ $coinfee->to_ore_pool_times }}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 场内转入矿池翻的倍数: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="cn_to_ore_times" id="form-field-1" placeholder="矿池释放比率" class="col-xs-10 col-sm-5" value="{{ $coinfee->cn_to_ore_times }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 可提转入矿池翻的倍数: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="kt_to_ore_times" id="form-field-1" placeholder="矿池释放比率" class="col-xs-10 col-sm-5" value="{{ $coinfee->kt_to_ore_times }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 提币自动审核开关: </label>
                                <div class="col-sm-9">
                                    <select name="withdraw_need_check">
                                        <option value="0" @if($coinfee->withdraw_need_check == 0) selected @endif>开</option><option value="1" @if($coinfee->withdraw_need_check == 1) selected @endif>关</option>
                                    </select>
                                </div>
                            </div>


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
        </div>
    </div>

    <script>
        $(function () {
            var btnDisable = false;//发送按钮默认禁用

            if (btnDisable) {
                return;
            }

            $("#seed").click(function () {
                $.get("/admin/sendCodeSMS?username=" + 'admin' + '&czuser=' + '{{ \Auth::guard('web')->user()->username }}' + '&des=编辑货币费率', function (result) {
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

