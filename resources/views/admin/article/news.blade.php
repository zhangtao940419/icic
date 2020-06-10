<!doctype html>
<html class="no-js">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>新闻列表</title>
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <!-- No Baidu Siteapp-->
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" type="image/png" href="assets/i/favicon.png">
    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="assets/i/app-icon72x72@2x.png">
    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Amaze UI" />
    <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">
    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="assets/i/app-icon72x72@2x.png">
    <meta name="msapplication-TileColor" content="#0e90d2">
    <link rel="stylesheet" href="/assets/css/amazeui.min.css">
    <link rel="stylesheet" href="/assets/css/news.css">
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>

<body>
<div data-am-widget="tabs" class="am-tabs am-tabs-d2" id="news_id">
    <!-- <ul class="am-tabs-nav am-cf">
        <li class="am-active">
            <a href="[data-tab-panel-0]">USDT</a>
        </li>
        <li class="">
            <a href="[data-tab-panel-1]">ETH</a>
        </li>
        <li class="">
            <a href="[data-tab-panel-2]">BTC</a>
        </li>
        <li class="">
            <a href="[data-tab-panel-2]">LTC</a>
        </li>
    </ul> -->
    <div class="am-tabs-bd" style="border:none;">
        <div data-tab-panel-0 class="am-tab-panel am-active" >
            <ul>
                <li class="lists clearfix" v-cloak v-for="(item,index) in newsList" :key="index" @click="goto(index)">
                    <div class="left_img" >
                        <img :src="item.cover" alt="">
                    </div>
                    <div class="right_content">
                        <p class="title">@{{item.title}}</p>
                        <p class="times">@{{item.created_at}}</p>
                        <p class="contents" v-html="item.excerpt"></p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>



<!--[if (gte IE 9)|!(IE)]><!-->
<script src="http://www.jq22.com/jquery/jquery-2.1.1.js"></script>
<!--<![endif]-->
<!--[if lte IE 8 ]>
<script src="http://libs.baidu.com/jquery/1.11.3/jquery.min.js"></script>
<script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
<script src="assets/js/amazeui.ie8polyfill.min.js"></script>
<![endif]-->
<script src="/assets/js/amazeui.min.js"></script>
<script src="/assets/js/vue.js"></script>
<script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
<script>
    new Vue({
        el: "#news_id",
        data: function () {
            return {
                newsList: [
                    {
                        id:1,
                    },
                    {
                        id:2
                    }
                ]
            }
        },
        methods: {

            // 获取新闻
            getlist() {
                this.$http.get("http://47.91.225.168/api/news").then(function(res){
                    console.log(res)
                    if(res.status == 200 ) {
                        console.log(res.data)
                        this.newsList = res.data
                    }
                })
            },

            // 跳转页面
            goto(index) {
                console.log(index);
                window.location.href = "/show/new/" + this.newsList[index].id
            }
        },
        mounted () {
            this.getlist()
        }
    })
</script>
</body>

</html>