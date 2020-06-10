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

       <form class="form-horizontal" role="form" method="post" action="{{ route('stoStage.update',$stoStage['stage_id']) }}">
        {{ csrf_field() }}
           @method('PUT')
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1">阶段 </label>
               <div class="col-sm-9">
                   <label class="col-sm-3 control-label no-padding-right" for="form-field-1"><font size="4px" color="red">第{{$stoStage['stage_number']}}阶段</font> </label>
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
           <input type="hidden" name="data_id" id="form-field-1" placeholder="data_id" class="col-xs-10 col-sm-5" value="{{$data_id}}" />
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 兑币 </label>
               <div class="col-sm-9">
                   <select id="form-field-2" class="col-xs-10 col-sm-5" name="exchange_coin_id">
                           <option value="{{$coin_data['coin_id']}}"  SELECTED >{{$coin_data['coin_name']}}</option>
                   </select>
               </div>
           </div>
        <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 周期发行总量 </label>
               <div class="col-sm-9">
                   <input type="text" name="stage_issue_number" id="form-field-1" placeholder="货币总量" class="col-xs-10 col-sm-5" value="{{$stoStage['stage_issue_number']}}" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 兑率 </label>
               <div class="col-sm-9">
                   <input type="text" name="exchange_rate" id="form-field-1" placeholder="兑率(例如汇率0.5,则兑币 = 基币 *0.5)" class="col-xs-10 col-sm-5" value="{{$stoStage['exchange_rate']}}" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 开始时间 </label>
               <div class="col-sm-9">
                   <input type="date" name="issue_begin_time" id="form-field-1" placeholder="开始时间" class="col-xs-10 col-sm-5" value="" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 周期时长 </label>
               <div class="col-sm-9">
                   <input type="text" name="issue_time" id="form-field-1" placeholder="周期时长(以天为单位)" class="col-xs-10 col-sm-5" value="{{$stoStage['issue_time']}}" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 每日开购时间 </label>
               <div class="col-sm-9">
                   <input type="time" name="start_time" id="form-field-1" placeholder="开始时间" class="col-xs-10 col-sm-5" value="{{$stoStage['start_time']}}" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 每日结束时间 </label>
               <div class="col-sm-9">
                   <input type="time" name="end_time" id="form-field-1" placeholder="开始时间" class="col-xs-10 col-sm-5" value="{{$stoStage['end_time']}}" />
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