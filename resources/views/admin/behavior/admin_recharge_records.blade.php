@extends('admin.layouts.app')
@section('title', '用户钱包')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                后台充值记录
            </li>
        </ul>
    </div>
        <!-- /.breadcrumb -->
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="nav-search" id="nav-search">
        <form class="form-search">

            <span class="input-icon">
                <select class="nav-search-input" id="user_auth_level" name="coin_id">
                    <option value="">币种</option>
                    @foreach($coins as $coin)
                    <option value="{{ $coin->coin_id }}" @if(request('coin_id')==$coin->coin_id) selected @endif>{{ $coin->coin_name }}</option>
                        @endforeach
                </select>

                <select class="nav-search-input" id="user_auth_level" name="type">
                    <option value="">类型</option>
                        <option value="1" @if(request('type')==1) selected @endif>增加</option>
                    <option value="2" @if(request('type')==2) selected @endif>减少</option>
                </select>

                <select class="nav-search-input" id="user_auth_level" name="wallet_type">
                    <option value="">余额类型</option>
                        <option value="1" @if(request('wallet_type')==1) selected @endif>场内</option>
                    <option value="2" @if(request('wallet_type')==2) selected @endif>可提</option>
                    <option value="3" @if(request('wallet_type')==3) selected @endif>矿池</option>
                </select>
            </span>

            <span>
                <input type="text" placeholder="用户手机/后台用户" class="nav-search-input" id="nav-search-input" name="value" autocomplete="off">
                <button style="background: none; border: 1px solid #6FB3E0;"><i class="ace-icon fa fa-search nav-search-icon"></i></button>
            </span>


        </form>
    </div>

    <div class="page-header">
        <h1>
            后台充值记录
        </h1>
    </div>
    <span class="label label-xlg label-primary" style="margin-left: 80%;cursor: pointer" onclick="outExcel()">导出excel</span>
    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <table id="simple-table" class="table  table-bordered table-hover">
        <thead>
        <tr>
            <th class="center">id</th>
            <th class="center">后台用户</th>
            <th class="center">用户</th>
            <th class="center">币种</th>
            <th class="center">类型</th>
            <th class="center">余额类型</th>
            <th class="center">数量({{ $total }})</th>
            <th class="center">时间</th>
        </tr>
        </thead>

        <tbody>
        @foreach($records as $record)
            <tr>
                <td class="center">{{ $record->id }}</td>
                <td class="center">{{ $record->admin_user->username }}</td>
                <td class="center">{{ $record->user->user_phone }} ({{ $record->user->userIdentify ? $record->user->userIdentify->identify_name : '--' }})</td>
                <td class="center">{{ $record->coin->coin_name }}</td>
                <td class="center">{{ $record->type()[$record->type] }}</td>
                <td class="center">{{ $record->wallet_type()[$record->wallet_type] }}</td>
                <td class="center">{{ $record->amount }}</td>
                <td class="center">{{ $record->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--分页--}}
    {{ $records->links() }}


    <script type="text/javascript">


    function outExcel() {
    // if (!confirm('确定导出excel吗?')){
    //     return false;
    // }
    window.location.href="{!! $excel !!}";
    }
    </script>
@endsection

