@extends('admin.layouts.app')
@section('title', '中央钱包')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li class="active">中央钱包</li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入报错信息页面--}}
    @include('admin.layouts._error')
    <div class="nav-search pull-right" id="nav-search">
        <form class="form-search">
            <span>
                <select class="nav-search-input" id="coin" name="time">
                    <option value="0" @if(request('time') == 0 || !request('time')) selected @endif>历史收入</option>
                    <option value="1" @if(request('time') == 1) selected @endif>今日收入</option>
                    <option value="2" @if(request('time') == 2) selected @endif>本月收入</option>
                </select>
            </span>
            <span>
                <select class="nav-search-input" id="coin" name="coin_id">
                    @foreach($coins as $coin)
                        <option value="{{ $coin['coin_id'] }}" @if($coin['coin_id'] == request('coin_id')) selected @endif>{{ $coin['coin_name'] }}</option>
                    @endforeach
                </select>
                <button type="submit" style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>
        </form>
    </div>
    <div class="page-header">
        <h1>
            中央钱包收入记录
        </h1>
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div>

        <h4 style="color: green; display: inline-block">
            收入总额:   {{ $amount }}
        </h4>


    </div>
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">流水id</th>
            <th class="center">所属会员</th>
            <th class="center">货币类型</th>
            <th class="center">数量</th>
            <th class="center">详情</th>
            <th class="center">产生时间</th>
            {{--<th class="center">操作</th>--}}
        </tr>
        </thead>

        <tbody>
        @foreach($flows as $flow)
            <tr>
                <td class="center">{{ $flow->id }}</td>
                <td class="center">{{ $flow->user_id  }}</td>
                <td class="center">{{ \App\Model\CoinType::find($flow->coin_id)->coin_name}}</td>
                <td class="center" style="color: green;">+{{ $flow->total_money }}</td>
                <td class="center">{{ $flow->content }}</td>
                <td class="center">{{ $flow->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $flows->appends(Request::except('page'))->render() }}
@endsection
@section('myJs')
    <script>
        var order = '';
        $(function () {
            $("#order").val(order)
        })
    </script>
@endsection