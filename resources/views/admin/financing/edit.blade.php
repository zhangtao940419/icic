@extends('admin.layouts.app')
@section('title', '编辑理财类型' )
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('InvestmentType.index') }}">分类列表</a>
            </li>
                <li class="active">编辑分类</li>
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
            编辑理财类型
        </h1>
    </div>
{{--        <form class="form-horizontal" role="form" method="post" action="{{ route('token.update', $token->token_id) }}">
            <input type="hidden" name="_method" value="PUT">--}}
                <form class="form-horizontal" role="form" method="post" action="{{ route('InvestmentRule.update',$record['invest_id']) }}">
                    <input type="hidden" name="_method" value="PUT">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1">代币名称</label>
                        <div class="col-sm-9">
                            <select type="text" name="coin_id" placeholder="代币名称" class="col-xs-10 col-sm-5">
                                <option value="">请选择</option>
                                @foreach($coins as $coin)
                                    <option value="{{ $coin->coin_id }}" @if($coin->coin_id==$record['coin_id']) selected @endif >{{ $coin->coin_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1">理财类型</label>
                        <div class="col-sm-9">
                            <select type="text" name="invest_id" placeholder="理财类型" class="col-xs-10 col-sm-5">
                                <option value="">请选择</option>
                                @foreach($investmentType as $investment)
                                    <option value="{{ $investment['invest_id'] }}" @if($investment['invest_id']==$record['invest_id']) selected @endif  >{{ $investment['invest_type_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-4"></div>

{{--                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1">理财套餐名称</label>
                        <div class="col-sm-9">
                            <input type="text" name="invest_type_name" id="form-field-1" placeholder="理财套餐名称" class="col-xs-10 col-sm-5" value="{{$record}}" />
                        </div>
                    </div>--}}
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1">套餐时长</label>
                        <div class="col-sm-9">
                            <input type="text" name="invest_time" id="form-field-1" placeholder="套餐时长(以天为单位)" class="col-xs-10 col-sm-5" value="{{$record['invest_time']}}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1">利率</label>
                        <div class="col-sm-9">
                            <input type="text" name="rate_of_return_set" id="form-field-1" placeholder="利率(0~100)" class="col-xs-10 col-sm-5" value="{{$record['rate_of_return_set']}}" />
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
                            <button class="btn" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                重置
                            </button>
                        </div>
                    </div>
                </form>
@endsection