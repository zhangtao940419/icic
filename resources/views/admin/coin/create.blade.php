@extends('admin.layouts.app')
@section('title', '币币汇率列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
                <li class="active">创建币币汇率</li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入报错信息页面--}}
    @include('admin.layouts._error')

    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div class="page-header">
        <h1>
            创建初始汇率
        </h1>
    </div>
        <form class="form-horizontal" role="form" method="post" action="{{ route('change.store') }}">
            {{ csrf_field() }}

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 货币名称 </label>
                <div class="col-sm-9">
                    <select type="text" name="base_coin_id" placeholder="货币名称" class="col-xs-10 col-sm-5">
                        <option selected value="{{ $current_coin->coin_id }}">{{ $current_coin->coin_name }}</option>
                    </select>
                </div>
            </div>

            <div class="space-4"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 兑换货币 </label>
                <div class="col-sm-9">
                    <select class="col-xs-10 col-sm-5" id="exchange_coin_id" name="exchange_coin_id">
                        <option selected value="{{ $change_coin->coin_id }}">{{ $change_coin->coin_name }}</option>
                    </select>
                </div>
            </div>

            <div class="space-4"></div>
            <div class="space-4"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">成交量</label>

                <div class="col-sm-9">
                    <input type="text" name="vol" id="form-field-1" placeholder="成交量" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            <div class="space-4"></div>
            <div class="space-4"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">价格升降</label>

                <div class="col-sm-9">
                    <select type="text" name="float_type" placeholder="价格是升降" class="col-xs-10 col-sm-1">
                        <option value="">请选择</option>
                        <option value="+" style="color: red;">+(涨)</option>
                        <option value="-" style="color: green">-(跌)</option>
                    </select>
                </div>
            </div>

            <div class="space-4"></div>
            <div class="space-4"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">成交价格浮动的百分比,涨幅</label>

                <div class="col-sm-9">
                    <input type="text" name="price_float" id="form-field-1" placeholder="成交价格浮动的百分比,涨幅" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            <div class="space-4"></div>
            <div class="space-4"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">最低交易价格</label>

                <div class="col-sm-9">
                    <input type="text" name="min_price" id="form-field-1" placeholder="最低交易价格" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            <div class="space-4"></div>
            <div class="space-4"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">最高交易价格</label>

                <div class="col-sm-9">
                    <input type="text" name="max_price" id="form-field-1" placeholder="最高交易价格" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            <div class="space-4"></div>
            <div class="space-4"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">当前价格</label>

                <div class="col-sm-9">
                    <input type="text" name="current_price" id="form-field-1" placeholder="当前价格" class="col-xs-10 col-sm-5"/>
                </div>
            </div>

            <div class="space-4"></div>
            <div class="space-4"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">24小时交易量</label>

                <div class="col-sm-9">
                    <input type="text" name="day_vol" id="form-field-1" placeholder="24小时交易量" class="col-xs-10 col-sm-5"/>
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
@endsection


