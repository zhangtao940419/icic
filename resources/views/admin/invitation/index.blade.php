@extends('admin.layouts.app')
@section('title', '邀请奖励设置')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                邀请奖励设置
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="page-header">
        <h1>
            邀请奖励
        </h1>
    </div>
    @if(!empty($invitation))
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3>邀请奖励设置</h3>
            </div>
            <div class="panel panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right"> 奖励的货币类型:
                        <h3>{{ \App\Model\CoinType::where('coin_id', $invitation['coin_id'])->value('coin_name') }}</h3>
                    </label>
                    <label class="no-padding-right"> 奖励的货币数量:
                        <h3> {{ $invitation['coin_num'] }}</h3>
                    </label>
                </div>

                <h5 class="page-header"></h5>
                <div class="space-4"></div>
                <div class="space-4"></div>
                <div class="space-4"></div>
                <div class="space-4"></div>
                <div class="space-4"></div>

                <a href="{{ route('invitation.post') }}" class="btn btn-sm btn-warning" title="修改">
                修改
                </a>
            </div>
        </div>


    @else
        请先设置邀请奖励
        <a href="{{ route('invitation.post') }}" class="btn btn-sm btn-info" title="操作">
            <i class="ace-icon fa fa-pencil bigger-120"></i>
        </a>
    @endif

@endsection