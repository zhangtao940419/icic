@extends('admin.layouts.app')
@section('title', '首页')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                控制面板
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            控制面板
        </h1>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-block alert-success">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>

                <font style="vertical-align: inherit;">
                    <font style="vertical-align: inherit;">欢迎来到    ICIC后台   ~_~</font>
                </font>
            </div>
            @if($permission == 1)
            <div class="row">
                <div class="space-6"></div>

                <div class="col-sm-7 infobox-container">
                    <div class="infobox infobox-green">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-users"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $userCount }}</font></font></span>
                            <div class="infobox-content"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">会员</font></font></div>
                        </div>
                    </div>

                    <div class="infobox infobox-blue">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-bullhorn"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $outsideOrderCount }}</font></font></span>
                            <div class="infobox-content"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">场外订单</font></font></div>
                        </div>
                    </div>

                    <div class="infobox infobox-pink">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-bullhorn"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $c2cOrderCount }}</font></font></span>
                            <div class="infobox-content"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">C2C订单</font></font></div>
                        </div>
                    </div>

                    <div class="infobox infobox-red">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-flag"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $articleCount }}</font></font></span>
                            <div class="infobox-content"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">文章</font></font></div>
                        </div>
                    </div>

                    <div class="infobox infobox-red">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-calendar"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $userIdentifyCount }}</font></font></span>
                            <div class="infobox-content"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">待审核高级认证</font></font></div>
                        </div>
                    </div>


                    <div class="infobox infobox-red">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-user"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $authUser }}</font></font></span>
                            <div class="infobox-content"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">初级认证会员人数</font></font></div>
                        </div>
                    </div>


                    <div class="infobox infobox-red">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-user"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $finishUser }}</font></font></span>
                            <div class="infobox-content"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">已完成高级认证</font></font></div>
                        </div>
                    </div>


                    <br>
                    <div class="infobox infobox-red">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-flask"></i>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $coinOrderCount }}</font></font></span>
                            <div class="infobox-content"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">提币订单</font></font></div>
                        </div>
                    </div>



                    <div class="space-6"></div>
                </div>

                <div class="vspace-12-sm"></div>

                <div class="col-sm-5">
                    <div class="widget-box">
                        <div class="widget-header widget-header-flat widget-header-small">
                            <h5 class="widget-title">
                                <i class="ace-icon fa fa-signal"></i>
                                    <font style="vertical-align: inherit;">
                                        中心钱包
                                    </font>
                            </h5>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main">
                                <div id="piechart-placeholder" style="width: 100%; min-height: 150px; padding: 0px; position: relative;">
                                    <div style="margin-left: 5px;">
                                        <table  class="table table-hover table-responsive">
                                            <thead>
                                            <tr>
                                                <th class="center">货币名称</th>
                                                <th class="center">货币数量总和</th>
                                                <th class="center">中央钱包(手续费收入总和)</th>
                                                <th class="center">STO收入总和</th>
                                                <th class="center">用户区块转入总和</th>
                                            </tr>
                                            </thead>

                                            <tbody>

                                            @foreach($coins as $coin)
                                                <tr>
                                                    <td class="center">{{ $coin->coin_name }}</td>
                                                    <td class="center" style="color: red;">{{ $coin->totalAmount($coin->coin_id) }}</td>
                                                    <td class="center" style="color: green;">{{ $coin->feeAmount($coin->coin_id) }}</td>
                                                    <td class="center" style="color: green;">{{ $coin->stoIncome($coin->coin_id) }}</td>
                                                    <td class="center" style="color: red;">{{ $coin->blockAmount($coin->coin_id) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="hr hr8 hr-double"></div>
                            </div>
                            <!-- /.widget-main -->
                        </div>
                        <!-- /.widget-body -->
                    </div>
                    <!-- /.widget-box -->
                </div>
                <!-- /.col -->
            </div>

            <div class="hr hr32 hr-dotted"></div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="widget-box transparent" id="recent-box">
                        <div class="widget-header">
                            <h4 class="widget-title lighter smaller">
                                <i class="ace-icon fa fa-rss orange"></i><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">最近
                                    </font></font></h4>

                            <div class="widget-toolbar no-border">
                                <span></span> <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">会员</font></font></a>
                            </div>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main padding-4">
                                <div class="tab-content padding-8">

                                    <div id="member">
                                        <div class="clearfix">
                                            {{--用户头像位置--}}
                                            @foreach($users as $user)
                                                <div class="itemdiv memberdiv">
                                                <div class="user">
                                                    <img alt="user" style="max-height: 40px; max-width: 40px;" src="{{ $user->user_headimg }}">
                                                </div>

                                                <div class="body">
                                                    <div class="name">
                                                        <a href="#">{{ $user->user_name }}</a>
                                                    </div>

                                                    <div class="time">
                                                        <i class="ace-icon fa fa-clock-o"></i>
                                                        <span class="green">{{ $user->created_at->diffForHumans() }}</span>
                                                    </div>

                                                    <div>
                                                        <span class="label label-warning label-sm">active</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                        <div class="space-4"></div>

                                        <div class="center">
                                            <i class="ace-icon fa fa-users fa-2x green middle"></i>
                                            &nbsp;
                                            <span class="btn btn-sm btn-white btn-info">
                                                最新用户 &nbsp;
                                            </span>
                                        </div>

                                        <div class="hr hr-double hr8"></div>
                                    </div>
                                    <!-- /.#member-tab -->
                                </div>
                            </div>
                            <!-- /.widget-main -->
                        </div>
                        <!-- /.widget-body -->
                    </div>
                    <!-- /.widget-box -->
                </div>
                <div class="col-sm-4" style="margin-left: 140px; margin-top: 35px;">
                    <div class="widget-box">
                        <div class="widget-header">
                            <h4 class="widget-title lighter smaller">
                                <i class="ace-icon fa fa-comment blue"></i>
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">
                                        初始汇率
                                    </font>
                                </font>
                            </h4>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main no-padding">
                                <div class="dialogs ace-scroll center">
                                    <h3>当前USDT换成CNY比率为: <span style="color: red">{{ $usdtRate }}</span></h3>
                                    <h3>当前场内的交易费率为: <span style="color: red">{{ $insideRate }}</span></h3>
                                    <h3>当前场外的交易费率为: <span style="color: red">{{ $outsideRate }}</span></h3>
                                </div>
                            </div>

                            </div>
                        </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection