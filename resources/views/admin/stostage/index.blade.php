@extends('admin.layouts.app')
@section('title', 'Sto发行阶段列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                Sto发行阶段列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            Sto发行阶段列表
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('stoStage.create','data_id='.$data_id) }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>

        </div>
        <!-- add reset e -->
    </div>


    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">ID</th>
            <th class="center">期数</th>
            <th class="center">基币名称</th>
            <th class="center">兑币名称</th>
            <th class="center">兑率</th>
            <th class="center">阶段发行量</th>
            <th class="center">阶段发行剩余量</th>
            <th class="center">开始时间</th>
            <th class="center">发行时长</th>
            <th class="center">每日开购时间</th>
            <th class="center">每日结束时间</th>
            <th class="center">状态</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($stoStageList as $stoStageLists)
        <tr>
            <td class="center">{{ $stoStageLists['stage_id'] }}</td>
            <td class="center" ><font size="3px" color="#4169e1">第{{ $i++ }}期</font></td>
            <td class="center">{{ $stoStageLists['get_base_coin_names']['coin_name'] }}</td>
            <td class="center">{{ $stoStageLists['get_exchange_coin_names']['coin_name'] }}</td>
            <td class="center" title="兑率是基本与兑币之间的汇率">{{ $stoStageLists['exchange_rate'] }}</td>
            <td class="center"><font color="#f4a460">{{ $stoStageLists['stage_issue_number'] }}</font></td>
            <td class="center"><font color="fuchsia">{{ $stoStageLists['stage_issue_remain_number'] }}</font></td>
            <td class="center"><font color="red">{{ date('Y-m-d H:i:s',$stoStageLists['issue_begin_time'])}}</font></td>
            <td class="center">{{ $stoStageLists['issue_time'] }}天</td>
            <td class="center">{!! $stoStageLists['start_time'] !!} </td>
            <td class="center">{!! $stoStageLists['end_time'] !!} </td>
            <td class="center">{!! $stoStageLists['issue_status'] !!} </td>
            <td class="center">
                <div>
                    <a href="{{ route('stoStage.edit',$stoStageLists['stage_id']) }}" class="btn btn-xs btn-info"  title="编辑">
                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                    </a>
                    <a href="{{ route('stoStageDay.index','stage_id='.$stoStageLists['stage_id']) }}" class="btn btn-xs btn-info"  title="发行天数">
                        <i class="ace-icon fa fa-flag bigger-120"></i>
                    </a>
                    <form id="del_form{{$stoStageLists['stage_id']}}" method="post" action="{{ route('stoStage.destroy',  $stoStageLists['stage_id']) }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-xs btn-danger" data-url="{{ route('stoStage.destroy',  $stoStageLists['stage_id']) }}" data-id="{{$stoStageLists['stage_id']}}" onclick="deleteOne(this)" style="margin-left: 2px">
                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                        </button>
                    </form>

                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section('myJs')

    <script type="text/javascript">


        function deleteOne(e) {
            // alert($(e).attr('data-id'));return;
            if (confirm('确定删除吗?')){
                var form = document.getElementById('del_form'+$(e).attr('data-id'));
                form.submit();
                // window.location.href="admin/stoStage/destroy";
                // return false;
            }

        }
    </script>
@endsection