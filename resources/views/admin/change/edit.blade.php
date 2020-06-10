@extends('admin.layouts.app')
@section('title', '交易对设置')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                交易对设置
            </li>
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
            交易对设置
        </h1>
    </div>

    <form class="form-horizontal" role="form" method="post" action="{{ route('change.changeNumber', ['base_coin_id' => $data['base_coin_id'], 'exchange_coin_id' => $data['exchange_coin_id']]) }}">
        {{ csrf_field() }}
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 交易对: </label>
            <div class="col-sm-9">
                <input type="text" disabled value="{{ strtoupper($data['base_coin_name'] . '/' . $data['exchange_coin_name']) }}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 交易数量: </label>
            <div class="col-sm-9">
                <input type="text" name="vol" id="form-field-1" placeholder="数量" class="col-xs-10 col-sm-5" value="{{ $data['vol'] }}"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 每日最大卖出次数: </label>
            <div class="col-sm-9">
                <input type="number" name="day_sell_num_limit" id="form-field-1" placeholder="每日最大卖出次数" class="col-xs-10 col-sm-5" value="{{ $insideSetting=== false ? '--' :$insideSetting->day_sell_num_limit }}"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 交易费率(%): </label>
            <div class="col-sm-9">
                <input type="text" name="fee" id="form-field-1" placeholder="交易费率(%)" class="col-xs-10 col-sm-5" value="{{ $insideSetting=== false ? '' :$insideSetting->fee }}"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"></label>
            <div class="col-sm-9">
                <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
            </div>
        </div>
    </form>
@endsection