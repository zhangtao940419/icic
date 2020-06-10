@extends('admin.layouts.app')
@section('title', '盘面数据')
@section('myCss')
    <style>
        [v-cloak] {
            display: none;
        }

        ul,
        li,
        ol {
            list-style: none;
        }

        .clearfix:after {
            content: "";
            display: block;
            height: 0;
            clear: both;
            visibility: hidden;
        }

        .coin_content {
            margin-left: 30%;
        }

        .coin_content .s1 {
            float: left;
            width: 260px;
            position: relative;
            height: 30px;
            line-height: 30px;

        }
        .coin_content .s2 {
            float: left;
            width: 150px;
            position: relative;
            height: 30px;
            line-height: 30px;
            margin-left: 30px;
        }

        .coin_content .coin_header {
            position: relative;
            height: 50px;
            line-height: 50px;
            width: 374px;
        }
        .coin_content .coin_header span {
            color: #333;
            font-size: 20px;
        }
        .coin_content .coin_header .coin_jiage {
            position: absolute;
            left: 0;
        }
        .coin_content .coin_header .coin_num {
            position: absolute;
            right: 0;
        }
        .coin_content li {
            width: 370px;
            position: relative;
            height: 30px;
            line-height: 30px;
        }
        .coin_content li .coin_red {
            color: #E31818;
            font-size: 16px;
            position: absolute;
            left: 0;
        }
        .coin_content li .coin_h {
            color: #666;
            font-size: 16px;
            position: absolute;
            right: 0;
        }
        .coin_content .coin_M {
            color: #333;
            font-size: 20px;
        }
        .coin_content .coin_Y {
            color: #666;
            font-size: 14px;
            margin-top: -10px;
        }
        .coin_content li .coin_blue {
            color: #414AFC;
            font-size: 16px;
            position: absolute;
            left: 0;
        }
    </style>
@endsection
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li class="active">盘面数据</li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入报错信息页面--}}
    @include('admin.layouts._error')

    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div class="page-header">
        <h1>
            盘面数据
        </h1>
    </div>

    <div class="row" style="width: 100%;height:50rem;overflow-x: auto;overflow-y: auto;border: 1px black solid">
        <div class="col-xs-12">
            <div class="row" id="news_id">
                <div class="col-xs-12" style="">

                    <ul class="coin_content" v-cloak style="margin: 0 auto">

                        <div class="clearfix">
                            <div class="labels s1">
                                <label>选择货币类型</label>
                                <select  @change="changTradeTeam">
                                    <!-- <option disabled value="">请选择</option> -->
                                    <option v-for="(option1,index) in options1" :value="index" >
                                        @{{option1.exchange_coin_name}}/@{{option1.base_coin_name}}
                                    </option>
                                </select>
                            </div>

                            <div class="labels s2">
                                <label>显示数量</label>
                                <select v-model="selected" @change="changTradeTeam1">
                                    <!-- <option disabled value="">请选择</option> -->
                                    <option v-for="(option2,index) in options2">
                                        @{{option2.num}}
                                    </option>

                                </select>
                            </div>
                        </div>

                        <div class="coin_header">
                            <span class="coin_jiage">价格(@{{coinlist.tradeTeam.exchange_coin_name}})</span>
                            <span class="coin_num">数量(SYS)</span>
                        </div>
                        <li v-for="(item1,index) in coinlist.sell" v-if="(index<=(selected-1))">
                            <span class="coin_red">@{{item1.unit_price}}</span>
                            <span class="coin_h" style="left: 35%">@{{item1.trade_total_num}} <a @click="showWindow(1,item1.unit_price,coinlist.tradeTeam.base_coin_id,coinlist.tradeTeam.exchange_coin_id)">详情</a></span>
                        </li>
                        <p class="coin_M">@{{coinlist.tradeTeam.current_price}}</p>
                        <p class="coin_Y">≈@{{coinlist.tradeTeam.CNY_price}}CNY</p>
                        <li v-for="(item2,index) in coinlist.buy" v-if="(index<=(selected-1))">
                            <span class="coin_blue">@{{item2.unit_price}}</span>
                            <span class="coin_h" style="left: 35%">@{{item2.trade_total_num}} <a @click="showWindow(2,item2.unit_price,coinlist.tradeTeam.base_coin_id,coinlist.tradeTeam.exchange_coin_id)">详情</a> </span>
                        </li>
                    </ul>


                </div>

                <!-- 遮罩层 -->
                <div id="cover" style="background: #000; position: absolute; left: 0px; top: 0px; width: 100%;height: 100%; filter: alpha(opacity=30); opacity: 0.9; display: none; z-index: 2 "></div>
                    <!-- 弹窗 -->
                    <div id="showdiv" style="width: 130%; margin: 0 auto; height: 149.5rem; border: 1px solid #999; display: none; position: absolute; top: 2%; left: 2%; z-index: 3; background: #fff">
                        <!-- 标题 -->
                        <div style="background: red; width: 100%; height: 2rem; font-size: 0.65rem; line-height: 2rem; border: 1px solid #999; text-align: center;cursor: pointer" onclick="closeWindow()">
                            关闭
                        </div>
                        <!-- 内容 -->
                        <div style="text-indent: 50px; height: 40rem; font-size: 0.5rem; padding: 0.5rem; line-height: 1rem; ">
                            <label class="block clearfix" style="margin-left: 0%">
                                <div style="height: 30px;">
                                    <ul class="coin_content" v-cloak style="margin-left: -2%">
                                        <li id="danjia"></li>
                                        <li v-for="(item,index) in user_list" v-if="(index<=(selected-1))">
                                            <span class="coin_blue">@{{item.user.user_phone}} (@{{ item.user.user_identify.identify_name }})           </span>
                                            <span class="coin_h" style="left: 18rem">@{{item.trade_total_num}}</span>
                                        </li>
                                    </ul>
                                </div>
                            </label>
                        </div>


                        <!-- 按钮 -->
                        {{--<div style="background: darkgray; cursor:pointer; width: 60%; margin: 0 auto; height: 3rem; line-height: 3rem; text-align: center;color: #fff;margin-top: 5%; -moz-border-radius: .128rem; -webkit-border-radius: .128rem; border-radius: .128rem;font-size: .59733rem;" data-uid="" data-type="" class="qd" onclick="closeWindow()">--}}
                        {{--确 定--}}
                        {{--</div>--}}
                    </div>



            <div class="row" style="display: none;">
                <div class="col-xs-12">
                    <div>
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


@endsection
@section('myJs')
    <script src="https://cdn.bootcss.com/vue/2.4.2/vue.min.js"></script>
    <script src="https://cdn.bootcss.com/vue-resource/1.5.1/vue-resource.min.js"></script>
    <script>
        new Vue({
            el: "#news_id",
            data: function () {
                return {
                    selecteds:'',
                    selected: "5",
                    selecttedValue:{},
                    options1: [
                        {
                            text: 'ETC'
                        },
                        {
                            text: 'ICIC'
                        },
                        {
                            text: 'BTC'
                        },
                    ],
                    options2: [{
                            num: 5
                        },
                        {
                            num: 20
                        },
                        {
                            num: 40
                        },
                        {
                            num: 60
                        },
                        {
                            num: 150
                        },
                    ],
                    coinlist: {
                    },
                    user_list: [
                        {
                            id : 1,
                            user : {
                                user_phone : '--',
                                user_identify : {
                                    idnetify_name : '--'
                                }
                            },
                            trade_total_num : 0
                        }

                    ]
                }
            },
            methods: {
                showWindow:function (type,price,baseCoinId,exchangeCoinId) {
                    // alert(baseCoinId);return;
                    var url='/api/adminGetInsideList?base_coin_id='+baseCoinId
                        +'&&exchange_coin_id='+exchangeCoinId+'&&type='+type+'&&price='+price
                    this.$http.get(url).then(function(res){
                        if(res.data.status_code==200){
                            this.user_list =res.data.data
                            // console.log(this.coinlist);
                        }
                    },function(res){
                    })


                    $("#danjia").text('单价:' + price);
                    $('#showdiv').show();  //显示弹窗
                    $('#cover').css('display','block'); //显示遮罩层
                    $('#cover').css('height',document.body.clientHeight+'px'); //设置遮罩层的高度为当前页面高度
                },
                getTtadeTeam: function () {
                    this.$http.get('/api/getTradeTeamList').then(function(res){
                           if(res.data.status_code==200){
                               this.options1 =res.data.data.tradeTeamList
                               this.selecteds = this.options1[0].exchange_coin_name+'/'+this.options1[0].base_coin_name;
                               this.options1[0].text = this.options1[0].exchange_coin_name+'/'+this.options1[0].base_coin_name;
                               var url='/api/adminGetTradeDisksurface?base_coin_id='+this.options1[0].base_coin_id
                                   +'&&exchange_coin_id='+this.options1[0].exchange_coin_id+'&&pageSize='+this.selected
                                 this.$http.get(url).then(function(res){
                                  if(res.data.status_code==200){
                                      this.coinlist =res.data.data;console.log(this.coinlist);
                                  }
                               },function(res){
                               })
                           }
                    },function(res){
                    })
                },
                changTradeTeam:function (val) {
                   // console.log(val.target.value)
                    // console.log(this.selecteds);
                    var url='/api/adminGetTradeDisksurface?base_coin_id='+this.options1[val.target.value].base_coin_id
                        +'&&exchange_coin_id='+this.options1[val.target.value].exchange_coin_id+'&&pageSize='+this.selected
                    this.$http.get(url).then(function(res){
                        if(res.data.status_code==200){
                            this.coinlist =res.data.data
                            console.log(this.coinlist);
                        }
                    },function(res){
                    })
                },
                changTradeTeam1:function () {
                    // console.log(val.target.value)
                    // console.log(this.selecteds);
                    var url='/api/adminGetTradeDisksurface?base_coin_id='+this.coinlist.tradeTeam.base_coin_id
                        +'&&exchange_coin_id='+this.coinlist.tradeTeam.exchange_coin_id+'&&pageSize='+this.selected
                    this.$http.get(url).then(function(res){
                        if(res.data.status_code==200){
                            this.coinlist =res.data.data
                            console.log(this.coinlist);
                        }
                    },function(res){
                    })
                }
            },

            mounted() {
                this.getTtadeTeam();
            }


        });

        // 弹窗
        // function showWindow() {
        //     $('#showdiv').show();  //显示弹窗
        //     $('#cover').css('display','block'); //显示遮罩层
        //     $('#cover').css('height',document.body.clientHeight+'px'); //设置遮罩层的高度为当前页面高度
        // }
        // 关闭弹窗
        function closeWindow() {
            $('#showdiv').hide();  //隐藏弹窗
            $('#cover').css('display','none');   //显示遮罩层
        }
    </script>
@endsection