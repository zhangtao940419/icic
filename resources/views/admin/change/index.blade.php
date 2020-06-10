@extends('admin.layouts.app')
@section('title', '交易对列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                交易对列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>


    <div class="page-header">
        <h1>
            交易对列表
        </h1>
        <!-- add reset e -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>



    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">交易对</th>
            <th class="center">成交量</th>
            <th class="center">交易对状态</th>
            <th class="center">管理交易对</th>
        </tr>
        </thead>

        <tbody>
        @foreach($changes as $change)
            <tr class="center">
                <td style=" line-height: 40px;">
                    <span style="font-size: 20px;">
                        {{ $change['exchange_coin_name'] . '/' . $change['base_coin_name'] . '(基币)' }}
                    </span>
                </td>
                <td style=" line-height: 40px;">
                    {{ $change['vol'] }}
                </td>
                <td style=" line-height: 40px;">
                    &nbsp;<span style="height: 20px; width: 40px;" class="label label-sm {{ $change['switch'] ? 'label-success' : 'label-danger'}}">{{ $change['switch'] ? '开放' : '关闭'}}</span>
                </td>
                <td class="center" style="line-height: 40px;">
                    <div>
                        <a href="{{ route('change.changeNumber', ['base_coin_id' => $change['base_coin_id'], 'exchange_coin_id' => $change['exchange_coin_id']]) }}" class="btn btn-xs btn-warning" title="编辑">
                            <i class="ace-icon fa fa-edit bigger-120"></i>
                        </a>
                        <a href="{{ route('change.switch', ['base_coin_id' => $change['base_coin_id'], 'exchange_coin_id' => $change['exchange_coin_id'], 'switch' =>  $change['switch']]) }}" class="btn btn-xs btn-danger hk seed" title="开启或关闭该交易">
                            <i class="ace-icon fa fa-key bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
