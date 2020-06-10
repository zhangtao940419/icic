@extends('admin.layouts.app')
@section('title', empty($invitation) ? '邀请奖励设置' : '修改邀请奖励设置')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('invitation.index') }}">邀请奖励</a>
            </li>

            <li>
                邀请奖励
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
            @if(empty($invitation))
                设置初始化邀请奖励
            @else
                修改邀请奖励
            @endif
        </h1>
    </div>
    <form class="form-horizontal" role="form" method="post" action="{{ route('invitation.post') }}">
        {{ csrf_field() }}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3>邀请奖励设置</h3>
            </div>
            <div class="panel panel-body">
                <div class="form-group">
                    <label class="col-sm-2 no-padding-right"> 奖励的货币类型:
                        <select name="coin_id">
                            @foreach($coins as $coin)
                                <option {{ $invitation['coin_id'] == $coin->coin_id ? "selected" : "" }} value="{{ $coin->coin_id }}">{{ $coin->coin_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="no-padding-right"> 奖励的货币数量:
                        <input type="text" name="coin_num" value="{{ $invitation['coin_num'] }}">
                    </label>
                </div>
                <input type="submit" class="btn btn-sm btn-info">
            </div>
        </div>

        <h5 class="page-header"></h5>
        <div class="space-4"></div>
        <div class="space-4"></div>
        <div class="space-4"></div>
        <div class="space-4"></div>
        <div class="space-4"></div>

    </form>

@endsection