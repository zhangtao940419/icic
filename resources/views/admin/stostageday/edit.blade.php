@extends('admin.layouts.app')
@section('title',  '编辑' )
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('stoStage.index') }}">分类列表</a>
            </li>
                <li class="active">编辑</li>
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
            编辑
        </h1>
    </div>

       <form class="form-horizontal" role="form" method="post" action="{{ route('stoStageDay.update',$stoStageDay['day_id']) }}">
        {{ csrf_field() }}
           @method('PUT')
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1">天数 </label>
               <div class="col-sm-9">
                   <label class="col-sm-3 control-label no-padding-right" for="form-field-1"><font size="4px" color="red">第{{$stoStageDay['issue_day']}}天</font> </label>
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 基币 </label>
               <div class="col-sm-9">
                   <select id="form-field-2" class="col-xs-10 col-sm-5" name="base_coin_id">
                       <option value="{{$base_coin_data['coin_id']}}"  SELECTED >{{$base_coin_data['coin_name']}}</option>
                   </select>
               </div>
           </div>
           <input type="hidden" name="stage_id" id="form-field-1" placeholder="data_id" class="col-xs-10 col-sm-5" value="{{$stoStageDay['stage_id']}}" />
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 兑币 </label>
               <div class="col-sm-9">
                   <select id="form-field-2" class="col-xs-10 col-sm-5" name="exchange_coin_id">
                           <option value="{{$coin_data['coin_id']}}"  SELECTED >{{$coin_data['coin_name']}}</option>
                   </select>
               </div>
           </div>
        <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 每日发行量 </label>
               <div class="col-sm-9">
                   <input type="text" name="stage_issue_number" id="form-field-1" placeholder="货币总量" class="col-xs-10 col-sm-5" value="{{$stoStageDay['stage_issue_number']}}" />
               </div>
           </div>
        <div class="space-4"></div>

        <div class="space-4"></div>

        <div class="space-4"></div>
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