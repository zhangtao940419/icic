<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/css/media.css">
    <link rel="stylesheet" href="/assets/css/news_details.css">
    <title>新闻详情页</title>
</head>
<body>
<div id="news_details">
    <div class="list">
        <!-- news header s -->
        <header>
            <div class="container">
                <p>
                    @{{news_details[0].title}}
                </p>
            </div>
        </header>
        <!-- news header e -->

        <!-- authers s -->
        <div class="authers">
            <div class="container">
                <span>作者：@{{news_details[0].username}}</span>
                <span>@{{news_details[0].created_at}}</span>
            </div>
        </div>
        <!-- authers e -->

        <!-- content s -->
        <div class="content">
            <div class="container">
                <div class="content_left">
                    <span class="one"></span>
                    <span class="two"></span>
                </div>
                <div class="content_center">
                    <p v-html="news_details[0].body">
                    </p>
                </div>
                <div class="content_right">
                    <span class="one"></span>
                    <span class="two"></span>
                </div>
                <!-- <div class="content_img">
                    <img src="./img/timg.jpg" alt="">
                </div> -->
                <!-- <div class="content_center" style="margin-top: 0.2rem;">
                    <p v-html="news_details[0].body">
                    </p>
                </div> -->
            </div>
        </div>
        <!-- content e -->
    </div>
</div>
<script src="/assets/js/vue.js"></script>
<script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
<script>
    new Vue({
        el: "#news_details",
        data: function () {
            return {
                id:1,
                news_details: [{

                }],

            }
        },

        methods: {
            getlistdetails() {
                // 获取路径所有参数的字符串
   /*             var url = window.location.href.split("?")[1];
                // // console.log(url)
                // // 获取id
                var id = parseInt(url.split("id=")[1].split("&")[0]);*/
                // console.log(id)
                this.$http.get("http://47.91.225.168/api/new/" + this.id).then(function (res) {
                    console.log(res.data)
                    if (res.data.data.length > 0) {
                        this.news_details = res.data.data
                        console.log(this.news_details)
                    }
                })
            }
        },

        mounted() {
            this.id={{$id}}
            this.getlistdetails()
        },

    })
</script>
</body>

</html>