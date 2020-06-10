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
                <a href="{{ route('stoList.index') }}">分类列表</a>
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
    @include('admin.layouts._message')
    <div class="page-header">
        <h1>
            编辑
        </h1>
    </div>

       <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="{{ route('stoList.update', $coin_data[0]['data_id']) }}">
        {{ csrf_field() }}
           @method('PUT')
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 选择基币 </label>
               <div class="col-sm-9">
                   <select id="form-field-2" class="col-xs-10 col-sm-5" name="base_coin_id">
                             <option value="{{ $coin_data[0]['get_base_coin_names']['coin_id'] }}" >{{ $coin_data[0]['get_base_coin_names']['coin_name'] }}</option>
                   </select>
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 选择兑币<font color="red">(发行的货币)</font> </label>
               <div class="col-sm-9">
                   <select id="form-field-2" class="col-xs-10 col-sm-5" name="coin_id">
                           <option value="{{ $coin_data[0]['get_coin_names']['coin_id'] }}" >{{ $coin_data[0]['get_coin_names']['coin_name'] }}</option>
                   </select>
               </div>
           </div>
        <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 货币总量 </label>
               <div class="col-sm-9">
                   <input type="text" name="total_coin_issuance" id="form-field-1" placeholder="货币总量" class="col-xs-10 col-sm-5" value="{{ $coin_data[0]['total_coin_issuance'] }}" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 发行总量 </label>
               <div class="col-sm-9">
                   <input type="text" name="issue_coin_number" id="form-field-1" placeholder="发行总量" class="col-xs-10 col-sm-5" value="{{ $coin_data[0]['issue_coin_number'] }}" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 背景图片 </label>
               <div class="col-sm-9">
                   <input type="file" name="img" id="form-field-1" placeholder="背景图片" class="col-xs-10 col-sm-5" value="" />

               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1">背景预览图 </label>
               <img class="col-xs-4 col-sm-2"  src="{{ $coin_data[0]['img'] }}"/>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 详情描述图片 </label>
               <div class="col-sm-9">
                   <input type="file" name="des_img" id="form-field-1" placeholder="详情描述图片" class="col-xs-10 col-sm-5" value="" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 白皮书(pdf格式) </label>
               <div class="col-sm-9">
                   <input type="file" name="white_paper" id="form-field-1" placeholder="白皮书" class="col-xs-10 col-sm-5" value="" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 是否奖励上级 </label>
               <div class="col-sm-9">
                   <select id="form-field-2" class="col-xs-10 col-sm-5" name="is_reward">
                       <option value="1" @if($coin_data[0]['is_reward']) selected @endif>是</option>
                       <option value="0" @if($coin_data[0]['is_reward'] == 0) selected @endif>否</option>
                   </select>
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 下级第一次购买奖励上级百分比 </label>
               <div class="col-sm-9">
                   <input type="text" name="first_reward_rate" id="form-field-1" placeholder="发行总量" class="col-xs-10 col-sm-5" value="{{ $coin_data[0]['first_reward_rate'] }}" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 下级非第一次购买奖励上级百分比 </label>
               <div class="col-sm-9">
                   <input type="text" name="reward_rate" id="form-field-1" placeholder="发行总量" class="col-xs-10 col-sm-5" value="{{ $coin_data[0]['reward_rate'] }}" />
               </div>
           </div>
           @method('PUT')
        <div class="space-4"></div>

        <div class="space-4"></div>

        <div class="space-4"></div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-4">描述</label>

            <div class="col-sm-9">
                <textarea name="des" rows="3" cols="50">{{ $coin_data[0]['des'] }}</textarea>
            </div>
        </div>
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