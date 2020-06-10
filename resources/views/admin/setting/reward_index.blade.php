@extends('admin.layouts.app')
@section('title', '系统奖励设置')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                系统奖励设置
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="page-header">
        <h1>
            系统奖励设置
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">



        </div>
        <!-- add reset e -->
    </div>
    @include('admin.layouts._message')
    @include('admin.layouts._error')
    <div>
        邀请注册奖励设置
        @foreach($topRs as $topR)
        <form class="form-horizontal" role="form" method="post" action="{{ route('user.reward_setting.update') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <input name="type" type="text" value="1" hidden>
                <input name="id" type="text" value="{{ $topR->id }}" hidden>
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">  </label><br>
                <div class="col-sm-9">
                    奖励币种: <select name="coin_id" disabled="disabled">
                        <option value="">请选择奖励币种</option>
                        @foreach($coins as $coin)
                        <option value="{{ $coin->coin_id }}" @if($topR &&  $topR->coin_id == $coin->coin_id) selected @endif>{{ $coin->coin_name }}</option>
                            @endforeach
                    </select>
                    奖励数量: <input type="text" name="number" value="@if($topR){{ $topR->number }}@endif">
                    开关: <select name="switch">
                        <option value="1" @if($topR &&  $topR->switch == 1) selected @endif>开</option>
                        <option value="0" @if($topR &&  $topR->switch == 0) selected @endif>关</option>
                    </select>
                    <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
                </div>
            </div>
        </form>
        @endforeach

        <br>
        新增
        <form class="form-horizontal" role="form" method="post" action="{{ route('user.reward_setting.update') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <input name="type" type="text" value="1" hidden>
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">  </label><br>
                <div class="col-sm-9">
                    奖励币种: <select name="coin_id">
                        <option value="">请选择奖励币种</option>
                        @foreach($coins as $coin)
                            <option value="{{ $coin->coin_id }}">{{ $coin->coin_name }}</option>
                        @endforeach
                    </select>
                    奖励数量: <input type="text" name="number" value="">
                    开关: <select name="switch">
                        <option value="1">开</option>
                        <option value="0">关</option>
                    </select>
                    <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
                </div>
            </div>
        </form>

        <hr><hr>

        推荐奖励设置
        @foreach($tjRs as $key=>$tjR)
            @foreach($tjR as $k=>$tj)
                <form class="form-horizontal" role="form" method="post" action="{{ route('user.reward_setting.update') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input name="type" type="text" value="2" hidden>
                        <input name="id" type="text" value="{{ $tj->id }}" hidden>
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1">  </label><br>
                        <div class="col-sm-9">
                            达标人数: <input type="number" name="s_number" value="@if($tj){{ $tj->s_number }}@endif">
                            奖励币种: <select name="reward_coin_id" disabled="disabled">
                                <option value="">请选择奖励币种</option>
                                @foreach($coins as $coin)
                                    <option value="{{ $coin->coin_id }}" @if($tj &&  $tj->reward_coin_id == $coin->coin_id) selected @endif>{{ $coin->coin_name }}</option>
                                @endforeach
                            </select>
                            奖励数量: <input type="text" name="reward_number" value="@if($tj){{ $tj->reward_number }}@endif">
                            开始时间: <input type="date" name="start_time" value="{{ $tj->s_t }}">
                            结束时间: <input type="date" name="end_time" value="{{ $tj->e_t }}">

                            <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
                        </div>
                    </div>
                </form>
            @endforeach
            <hr>
        @endforeach
        <br>
        新增
        <form class="form-horizontal" role="form" method="post" action="{{ route('user.reward_setting.update') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <input name="type" type="text" value="2" hidden>
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">  </label><br>
                <div class="col-sm-9">
                    达标人数: <input type="number" name="s_number" value="1">
                    奖励币种: <select name="reward_coin_id">
                        <option value="">请选择奖励币种</option>
                        @foreach($coins as $coin)
                            <option value="{{ $coin->coin_id }}">{{ $coin->coin_name }}</option>
                        @endforeach
                    </select>
                    奖励数量: <input type="text" name="reward_number" value="">
                    开始时间: <input type="date" name="start_time" value="">
                    结束时间: <input type="date" name="end_time" value="">

                    <button type="submit" style="margin-left: 5px;" class="btn btn-sm btn-success">提交</button>
                </div>
            </div>
        </form>



    </div>
@endsection