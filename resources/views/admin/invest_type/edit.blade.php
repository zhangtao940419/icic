@extends('admin.layouts.app')
@section('title', '创建代币' )
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('token.index') }}">分类列表</a>
            </li>
                <li class="active">添加分类</li>
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
                创建代币
        </h1>
    </div>

{{--        <form class="form-horizontal" role="form" method="post" action="{{ route('token.update', $token->token_id) }}">
            <input type="hidden" name="_method" value="PUT">--}}

                <form class="form-horizontal" role="form" method="post" action="{{ route('token.store') }}">

                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 代币名称 </label>
                        <div class="col-sm-9">
                            <select type="text" name="coin_id" placeholder="代币名称" class="col-xs-10 col-sm-5">
                                <option value="">请选择</option>
                                @foreach($coins as $coin)
                                    <option value="{{ $coin->coin_id }}">{{ $coin->coin_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1">理财类型</label>
                        <div class="col-sm-9">
                            <input type="text" name="token_total_supply" id="form-field-1" placeholder="代币的总发行量" class="col-xs-10 col-sm-5" value="" />
                        </div>
                    </div>

{{--
                    <div class="space-4"></div>
                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-4">合约地址</label>
                        <div class="col-sm-9">
                            <textarea name="token_contract_address" rows="1" cols="70">{{ $token->token_contract_address }}</textarea>
                        </div>
                    </div>

                    <div class="space-4"></div>
                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-4">合约abi</label>
                        <div class="col-sm-9">
                            <textarea name="token_contract_abi" rows="20" cols="75">{{ $token->token_contract_abi }}</textarea>
                        </div>
                    </div>

                    <div class="space-4"></div>
                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 小数位 </label>
                        <div class="col-sm-9">
                            <input type="text" name="token_decimal" id="form-field-1" placeholder="小数位" class="col-xs-10 col-sm-5" value="{{ $token->token_decimal }}" />
                        </div>
                    </div>--}}

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