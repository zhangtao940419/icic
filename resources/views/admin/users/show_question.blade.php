@extends('admin.layouts.app')
@section('title', '问题详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('user.userQuestion') }}">用户提问列表</a>
            </li>

            <li>
                问题详细信息
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    @include('admin.layouts._message')
    @include('admin.layouts._error')
    <table id="simple-table" class="table table-bordered table-hover" style="width:60%; margin-left:15em; font-size: 20px;">
        <tbody>
        <tr>
            <th class="center">ID</th>
            <th class="center">
                {{ $record->id }}
            </th>
        </tr>
        <tr>
            <th class="center">会员</th>
            <th class="center">
                {{ $record->user->user_phone }}
            </th>
        </tr>
        <tr>
            <th class="center">问题类型</th>
            <th class="center">
                {{ $record->type == null ? '无' : $record->type->type }}
            </th>
        </tr>
        <tr>
            <th class="center">问题详情</th>
            <th class="center">
                {{ $record->question }}
            </th>
        </tr>
        <tr>
            <th class="center">图片1</th>
            <th class="center">
                <img src="{{ $record->image1 }}">
            </th>
        </tr>
        <tr>
            <th class="center">图片2</th>
            <th class="center">
                <img src="{{ $record->image2 }}">
            </th>
        </tr>
        <tr>
            <th class="center">图片3</th>
            <th class="center">
                <img src="{{ $record->image3 }}" style="width: 100%">
            </th>
        </tr>
        <tr>
            <th class="center">用户邮箱</th>
            <th class="center">
                {{ $record->email }}
            </th>
        </tr>
        <tr>
            <th class="center">状态</th>
            <th class="center">
                {{ $record->getStatus() }}
            </th>
        </tr>

        @if($record->status)
        <tr>
            <th class="center">回复</th>
            <th class="center">
                {{ $record->answer }}
            </th>
        </tr>
@endif

        </tbody>
    </table>

    <hr/>

    <div>

                    <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="{{ route('user.answers',$record->id) }}">
                        {{ csrf_field() }}

                        <div class="space-4"></div>


                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-4">回答</label>

                            <div class="col-sm-5">
                                <textarea name="answer" rows="8" cols="150">{{ $record->answer }}</textarea>
                            </div>

                            <div class="col-sm-5" style="margin: 0 auto">

                                回复图片1: @if($record->a_image1)<img src="{{ $record->a_image1 }}" style="width: 15%;height: 20%">@endif <input name="a_image1" type="file"/><hr/>
                                回复图片2: @if($record->a_image2)<img src="{{ $record->a_image2 }}" style="width: 15%;height: 20%">@endif <input name="a_image2" type="file"/><hr/>
                                回复图片3: @if($record->a_image3)<img src="{{ $record->a_image3 }}" style="width: 15%;height: 20%">@endif <input name="a_image3" type="file"/><hr/>


                            </div>

                            
                            
                        </div>

                        <div class="space-4"></div>

                        <div class="space-4"></div>

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

    </div>





@endsection

@section('myJs')

    <script>
    </script>
@endsection