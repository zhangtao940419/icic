@extends('admin.layouts.app')
@section('title',  $c2csetting->id ? '编辑交易设置' : '创建交易设置' )
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
                            <a href="#">c2c交易设置</a>
                        </li>
                        <li class="active">
                            @if($c2csetting->id)
                                编辑交易设置
                            @else
                                创建交易设置
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
                        @if($c2csetting->id)
                            编辑交易设置
                        @else
                            创建交易设置
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
            @if($c2csetting->id)
                <form class="form-horizontal" role="form" method="post" action="{{ route('c2csetting.update', $c2csetting->id) }}">
                    <input type="hidden" name="_method" value="PUT">
                    @else
                        <form class="form-horizontal" role="form" method="post" action="{{ route('c2csetting.store') }}">
                            @endif

                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 货币类型: </label>
                                <div class="col-sm-9">
                                    <select name="coin_id" id="form-field-1" class="col-xs-10 col-sm-5" >
                                        <option value="">请选择</option>
                                        @foreach($coins as $coin)
                                            <option value="{{ $coin->coin_id }}" {{ $c2csetting->coin_id == $coin->coin_id ? 'selected' : '' }}>{{ $coin->coin_name }}</option>
                                        @endforeach
                                    </select>
                                    <span style="color: red; position: relative; left: 5px; top: 7px;">(必填)</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 买单价格: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="buy_price" id="form-field-1" placeholder="买单价格" class="col-xs-10 col-sm-5" value="{{ $c2csetting->buy_price }}" />
                                    <span style="color: red; position: relative; left: 5px; top: 7px;">(必填)</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 卖单的价格: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="sell_price" id="form-field-1" placeholder="买单价格" class="col-xs-10 col-sm-5" value="{{ $c2csetting->sell_price }}" />
                                    <span style="color: red; position: relative; left: 5px; top: 7px;">(必填)</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 用户同时挂买单单数量: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="user_buy_order_limit" id="form-field-1" placeholder="默认1单" class="col-xs-10 col-sm-5" value="{{ $c2csetting->user_buy_order_limit }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 用户同时挂卖单数量: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="user_sell_order_limit" id="form-field-1" placeholder=" 默认1单" class="col-xs-10 col-sm-5" value="{{ $c2csetting->user_sell_order_limit }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 用户挂买单单笔数量限制最小值: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="user_buy_num_min" id="form-field-1" placeholder="默认1" class="col-xs-10 col-sm-5" value="{{ $c2csetting->user_buy_num_min }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 用户挂买单单笔数量最大值: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="user_buy_num_max" id="form-field-1" placeholder=" 默认1000" class="col-xs-10 col-sm-5" value="{{ $c2csetting->user_buy_num_max }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 用户挂卖单单笔数量限制最小值: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="user_sell_num_min" id="form-field-1" placeholder="默认1" class="col-xs-10 col-sm-5" value="{{ $c2csetting->user_sell_num_min }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 用户挂卖单单笔数量限制最大值: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="user_sell_num_max" id="form-field-1" placeholder="默认1000" class="col-xs-10 col-sm-5" value="{{ $c2csetting->user_sell_num_max }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 用户单日挂卖单累计上限(0h-24h): </label>
                                <div class="col-sm-9">
                                    <input type="text" name="user_sell_day_max" id="form-field-1" placeholder="默认1000" class="col-xs-10 col-sm-5" value="{{ $c2csetting->user_sell_day_max }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 商家同时能接的买单最大数量: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="business_buy_order_limit" id="form-field-1" placeholder="默认5" class="col-xs-10 col-sm-5" value="{{ $c2csetting->business_buy_order_limit }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 商家同时能接卖单的最大数量: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="business_sell_order_limit" id="form-field-1" placeholder="默认1" class="col-xs-10 col-sm-5" value="{{ $c2csetting->business_sell_order_limit }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 商家接买单的时间间隔(分钟): </label>
                                <div class="col-sm-9">
                                    <input type="text" name="business_buy_order_time_space" id="form-field-1" placeholder="默认30" class="col-xs-10 col-sm-5" value="{{ $c2csetting->business_buy_order_time_space }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 商家确认买单已收款的最低时间(分钟): </label>
                                <div class="col-sm-9">
                                    <input type="text" name="business_buy_order_confirm_time" id="form-field-1" placeholder="默认30" class="col-xs-10 col-sm-5" value="{{ $c2csetting->business_buy_order_confirm_time }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 商家确认卖单已付款的最低时间(分钟): </label>
                                <div class="col-sm-9">
                                    <input type="text" name="business_sell_order_confirm_time" id="form-field-1" placeholder="默认30" class="col-xs-10 col-sm-5" value="{{ $c2csetting->business_sell_order_confirm_time }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 商家接买单自动撤销时间(小时): </label>
                                <div class="col-sm-9">
                                    <input type="text" name="buy_order_auto_handle" id="form-field-1" placeholder="默认24" class="col-xs-10 col-sm-5" value="{{ $c2csetting->buy_order_auto_handle }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 卖出/买入比率异常界限值,大于或等于此值的用户会显示异常: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="unusual_rate" id="form-field-1" placeholder="默认2" class="col-xs-10 col-sm-5" value="{{ $c2csetting->unusual_rate }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 长时间未买入检测时间(天/设为0表示取消该功能): </label>
                                <div class="col-sm-9">
                                    <input type="text" name="long_time_not_buy_check_day" id="form-field-1" placeholder="默认2" class="col-xs-10 col-sm-5" value="{{ $c2csetting->long_time_not_buy_check_day }}" />
                                </div>
                            </div>

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
        </div>
    </div>
@endsection

