@extends('admin.layouts.app')
@section('title', '新增类型')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('question_type.index') }}">问题类型管理</a>
            </li>

            <li>
                新增类型
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    @include('admin.layouts._message')
    @include('admin.layouts._error')


    <hr/>

    <div>

                    <form class="form-horizontal" role="form" method="post" action="{{ route('question_type.store') }}">
                        {{ csrf_field() }}

                        <div class="space-4"></div>


                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-4">类型</label>

                            <div class="col-sm-5">
                                <textarea name="type" rows="5" cols="55"></textarea>
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