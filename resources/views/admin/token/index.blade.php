@extends('admin.layouts.app')
@section('title', 'eth代币列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                eth代币列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>


    <div class="page-header">
        <h1>
            eth代币列表
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('token.create') }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>

        </div>
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
            <th class="center">ID</th>
            <th class="center">代币简写</th>
            <th class="center">代币的总发行量</th>
            <th class="center">合约地址</th>
            <th class="center">小数位</th>
            <th class="center">操作</th>
        </tr>
        </thead>

        <tbody>
        @foreach($ethtokens as $token)
            <tr>
                <td class="center" style="line-height: 100px;">{{ $token->token_id }}</td>
                <td class="center" style="line-height: 100px;">{{ $token->token_symbol }}</td>
                <td class="center" style="line-height: 100px;">{{ $token->token_total_supply }}</td>
                <td class="center" style="line-height: 100px;">{{ $token->token_contract_address }}</td>
                <td class="center" style="line-height: 100px;">{{ $token->token_decimal }}</td>
                <td class="center" style="line-height: 100px;">
                    <div>
                        <a href="{{ route('token.edit', $token->token_id) }}" class="btn btn-xs btn-info" title="编辑">
                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $ethtokens->links() }}
@endsection