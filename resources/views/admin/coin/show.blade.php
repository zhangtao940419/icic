@extends('admin.layouts.app')
@section('title', '查看货币交易详细信息')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('coinType.index') }}">交易信息</a>
            </li>

            <li>
                交易详细信息
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    @include('admin.layouts._message')
    <table id="simple-table" class="table table-bordered table-hover" style="width:60%; margin-left:12em; font-size: 20px;">
        <tbody>
        <tr>
            <th class="center" style="width: 20em;">货币</th>
            <th class="center" style="width: 10em;">操作</th>
        </tr>
        @foreach($others as $other)
        <tr>
            <th class="center">
                <span class="label label-xlg label-success">{{ $coinType->coin_name }}</span>
                =><span class="label label-xlg label-danger">{{ $other->coin_name }}</span>
            </th>
            <th class="center">
                <a href="{{ route('change.edit', ['coin_id' => $coinType->coin_id, 'change_coin_id' => $other->coin_id]) }}" class="btn btn-xs btn-info" title="生成交易对">
                    <i class="ace-icon fa fa-send bigger-120"></i>
                </a>
            </th>
        </tr>
        @endforeach
        </tbody>
    </table>

@endsection