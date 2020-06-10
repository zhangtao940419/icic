@extends('admin.layouts.app')
@section('title', '盘面数据')
@section('myCss')
    <style>
        [v-cloak] {
            display: none;
        }

        ul,
        li,
        ol {
            list-style: none;
        }

        .clearfix:after {
            content: "";
            display: block;
            height: 0;
            clear: both;
            visibility: hidden;
        }

        .coin_content {
            margin-left: 30%;
        }

        .coin_content .s1 {
            float: left;
            width: 260px;
            position: relative;
            height: 30px;
            line-height: 30px;

        }
        .coin_content .s2 {
            float: left;
            width: 150px;
            position: relative;
            height: 30px;
            line-height: 30px;
            margin-left: 30px;
        }

        .coin_content .coin_header {
            position: relative;
            height: 50px;
            line-height: 50px;
            width: 374px;
        }
        .coin_content .coin_header span {
            color: #333;
            font-size: 20px;
        }
        .coin_content .coin_header .coin_jiage {
            position: absolute;
            left: 0;
        }
        .coin_content .coin_header .coin_num {
            position: absolute;
            right: 0;
        }
        .coin_content li {
            width: 370px;
            position: relative;
            height: 30px;
            line-height: 30px;
        }
        .coin_content li .coin_red {
            color: #E31818;
            font-size: 16px;
            position: absolute;
            left: 0;
        }
        .coin_content li .coin_h {
            color: #666;
            font-size: 16px;
            position: absolute;
            right: 0;
        }
        .coin_content .coin_M {
            color: #333;
            font-size: 20px;
        }
        .coin_content .coin_Y {
            color: #666;
            font-size: 14px;
            margin-top: -10px;
        }
        .coin_content li .coin_blue {
            color: #414AFC;
            font-size: 16px;
            position: absolute;
            left: 0;
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
            <li class="active">盘面数据</li>
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
            盘面数据
        </h1>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="row" id="news_id">
                <div class="col-xs-12">
                    <ul class="coin_content">
                        <form class="form-search">
                        <div class="clearfix">
                            <div class="labels s1">
                                <label>选择货币类型</label>
                                <select name="team"  id="team">
                                    <option value="">请选择</option>
                                    @foreach($tradeTeamList as $value)
                                    <option value="{{ $value['exchange_coin_id'].'_'.$value['base_coin_id'] }}" @if(request('team')==$value['exchange_coin_name'].'_'.$value['base_coin_name']) selected @endif>{{ $value['exchange_coin_name'].'/'.$value['base_coin_name'] }}</option>
                                        @endforeach
                                </select>
                            </div>

                            <div class="labels s2">
                                <label>显示数量</label>
                                <select name="num" id="num">
                                    <!-- <option disabled value="">请选择</option> -->
                                    <option value="5" @if(request('num')==5) selected @endif>5</option>
                                    <option value="10" @if(request('num')==10) selected @endif>10</option>
                                    <option value="20" @if(request('num')==20) selected @endif>20</option>

                                </select>
                            </div>
                        </div>
                            </form>

                        <div class="coin_header">
                            <span class="coin_jiage">价格()</span>
                            <span class="coin_num">数量(SYS)</span>
                        </div>
                        <li>
                            <span class="coin_red"></span>
                            <span class="coin_h"></span>
                        </li>
                        <p class="coin_M"></p>
                        <p class="coin_Y">≈CNY</p>
                        <li>
                            <span class="coin_blue"></span>
                            <span class="coin_h"></span>
                        </li>
                    </ul>

                </div>
            </div>

            <div class="row" style="display: none;">
                <div class="col-xs-12">
                    <div>
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('myJs')
    <script>
        $('#team').on('change', function () {
            $(".form-search").submit();
        })
        $('#num').on('change', function () {
            $(".form-search").submit();
        })
    </script>
@endsection