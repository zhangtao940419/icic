@extends('admin.layouts.app')
@section('title',  '创建' )
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
                <li class="active">添加</li>
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
                创建
        </h1>
    </div>

       <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="{{ route('stoList.store') }}">
        {{ csrf_field() }}
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 选择基币 </label>
               <div class="col-sm-9">
                   <select id="form-field-2" class="col-xs-10 col-sm-5" name="base_coin_id">
                       <option value="0">顶级分类</option>
                        @foreach($coin as $value)
                             <option value="{{ $value['coin_id'] }}" >{{ $value['coin_name'] }}</option>
                         @endforeach
                   </select>
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 选择兑币<font color="red">(发行的货币)</font> </label>
               <div class="col-sm-9">
                   <select id="form-field-2" class="col-xs-10 col-sm-5" name="coin_id">
                       <option value="0">顶级分类</option>
                       @foreach($coin as $value)
                           <option value="{{ $value['coin_id'] }}" >{{ $value['coin_name'] }}</option>
                       @endforeach
                   </select>
               </div>
           </div>
        <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 货币总量 </label>
               <div class="col-sm-9">
                   <input type="text" name="total_coin_issuance" id="form-field-1" placeholder="货币总量" class="col-xs-10 col-sm-5" value="" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 发行总量 </label>
               <div class="col-sm-9">
                   <input type="text" name="issue_coin_number" id="form-field-1" placeholder="发行总量" class="col-xs-10 col-sm-5" value="" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 背景图片 </label>
               <div class="col-sm-9">
                   <input type="file" name="img" id="form-field-1" placeholder="背景图片" class="col-xs-10 col-sm-5" value="" />
               </div>
           </div>

           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 详情描述图片 </label>
               <div class="col-sm-9">
                   <input type="file" name="des_img" id="form-field-1" placeholder="背景图片" class="col-xs-10 col-sm-5" value="" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 白皮书(pdf格式) </label>
               <div class="col-sm-9">
                   <input type="file" name="white_paper" id="form-field-1" placeholder="背景图片" class="col-xs-10 col-sm-5" value="" />
               </div>
           </div>
           <div class="form-group">
               <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 是否奖励上级 </label>
               <div class="col-sm-9">
                   <select id="form-field-2" class="col-xs-10 col-sm-5" name="is_reward">
                       <option value="1">是</option>
                       <option value="0">否</option>
                   </select>
               </div>
           </div>
        <div class="space-4"></div>

        <div class="space-4"></div>

        <div class="space-4"></div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-4">描述</label>

            <div class="col-sm-9">
                <textarea name="des" rows="3" cols="50"></textarea>
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