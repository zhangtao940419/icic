@extends('admin.layouts.app')
@section('title', 'Sto购买记录')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                Sto购买记录
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search">
        <form class="form-search">
            <select class="nav-search-input" id="exchangeCoinId" name="exchangecoinid">
                <option value="">兑币类型</option>
                @foreach($stoCoinList as $value)
                <option value="{{ $value->coin_id }}" @if(request('exchangecoinid')==$value->coin_id) selected @endif>{{ $value->coin_name }}</option>
                @endforeach
            </select>
            <select class="nav-search-input" id="stage" name="stage">
                <option value="">阶段</option>
                @foreach([1,2,3,4,5,6,7,8,9,10] as $value)
                    <option value="{{ $value }}" @if(request('stage')==$value) selected @endif>{{ $value }}</option>
                @endforeach
            </select>
            <select class="nav-search-input" id="day" name="day">
                <option value="">天数</option>
                @foreach([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20] as $value)
                    <option value="{{ $value }}" @if(request('day')==$value) selected @endif>{{ $value }}</option>
                @endforeach
            </select>
            <select class="nav-search-input" id="rate" name="rate">
                <option value="">比率</option>
                @foreach($rateList as $value)
                    <option value="{{ $value->exchange_rate }}" @if(request('rate')==$value->exchange_rate) selected @endif>1:{{ $value->exchange_rate }}</option>
                @endforeach
            </select>
            <div class="jeitem" style="display: inline-block">
                <div class="jeinpbox">
                    <input type="text" class="jeinput nav-search-input" id="test04" name="begintime" placeholder="开始时间" value="@if(request('begintime')) {{ request('begintime') }} @endif">
                    格式:2016-10-06 10:00:00
                </div>
            </div>
            <div class="jeitem" style="display: inline-block">
                <div class="jeinpbox">
                    <input type="text" class="jeinput nav-search-input" id="test05" name="endtime" placeholder="结束时间" value="@if(request('endtime')) {{ request('endtime') }} @endif">
                </div>
            </div>
            <span>
                <input type="text" placeholder="会员电话..." class="nav-search-input" id="userphone" name="userphone" autocomplete="off" value="@if(request('userphone')) {{ request('userphone') }} @endif">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            Sto购买记录
        </h1>
        <!-- add reset s -->
        {{--<div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">--}}

            {{--<a href="{{ route('stoList.create') }}" class="btn btn-success">--}}
                {{--<i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>--}}
                {{--新增--}}
            {{--</a>--}}

        {{--</div>--}}
        <!-- add reset e -->
    </div>


    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div>
        合计兑币数量为:
        <h4 style="color: red; display: inline-block">
            {{ $sum_exchange }}
        </h4>|
        <div class="space-4"></div>
        合计基币数量为:
        <h4 style="color: red; display: inline-block">
            {{ $sum_base }}
        </h4>
        <span class="label label-xlg label-primary" style="margin-left: 80%;cursor: pointer" onclick="outExcel()">导出excel</span>
    </div>

    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">用户</th>
            <th class="center">真实姓名</th>
            <th class="center">基币名称</th>
            <th class="center">兑币名称</th>
            <th class="center">阶段</th>
            <th class="center">天数</th>
            <th class="center">花费基币数量</th>
            <th class="center">得到兑币数量</th>
            <th class="center">比率(基币:兑币)</th>
            <th class="center">时间</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($records as $record)
        <tr>
            <td class="center">{{ $record->record_id }}</td>
            <td class="center">{{ $record->user->user_phone }}</td>
            <td class="center">{{ $record->user->userIdentify->identify_name }}</td>
            <td class="center">{{ $record->base_coin->coin_name }}</td>
            <td class="center">{{ $record->exchange_coin->coin_name }}</td>
            <td class="center">{{ $record->stage_id == 888888 ? '--' :$record->stage->stage_number }}</td>
            <td class="center">{{ $record->stage_id == 888888 ? '--' :$record->day->issue_day }}</td>
            <td class="center">{{ $record->base_trade_number }}</td>
            <td class="center">{{ $record->exchange_trade_number }}</td>
            <td class="center">1:{{ rtrim(rtrim($record->exchange_rate,'0'),'.') }}</td>
            <td class="center">{{ $record->created_at }}</td>
            <td class="center">
                <div>

                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    {{ $records->appends(\Request::except('page'))->render() }}
@endsection
@section('myJs')

    <script type="text/javascript">

        function outExcel() {
            // if (!confirm('确定导出excel吗?')){
            //     return false;
            // }
            window.location.href="{!! $excel !!}";
        }
    </script>
@endsection