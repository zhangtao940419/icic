<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>每日快讯</title>
    <link rel="stylesheet" type="text/css" href="/web/css/reset.css" />
</head>
<style>
    #tts .top img {
        width: 100%;
        background-size: contain;
    }

    #tts .center {
        width: 90%;
        margin: 0 auto;
        background: rgba(255, 255, 255, 1);
        box-shadow: -1px 5px 8px 0px rgba(0, 0, 0, 0.14);
        border-radius: 5px;
    }

    #tts .center p {
        padding: 10px 20px;
        letter-spacing: 3px;
    }

    #tts .center_1{
        height: 310px;
        overflow: scroll;
    }

    #tts .center_1 div:last-child {
        padding-bottom: 10px;
    }

    #tts .center .center_top {
        display: flex;
        align-items: center;
        padding: 10px 0 10px 10px;
    }


    #tts .center img {
        width: 20px;
        height: 20px;
    }

    #tts .center span {
        padding-left: 10px;
    }

    #tts .img {
        width: 90%;
        margin: 0 auto;
        margin-bottom: 20px;
    }

    #tts .img img {
        width: 100%;
        background-size: cover;
        margin-top: -40px;
    }
</style>

<body>
<div id="tts">
    <div class="top">
        <img src="/web/img/1.png" alt="">
    </div>
    <div class="center_1">
        <div class="center" v-for="(item,index) in listData" :key="index">
                <span class="center_top">
                    <img src="/web/img/3.png" alt="">
                    <span>@{{ item.created_at }}</span>
                </span>
            <div v-html="item.body"></div>
        </div>
    </div>
    <div class="img">
        <img src="/web/img/2.png" alt="">
    </div>
</div>
</body>

</html>
<script src="/web/js/vue.js"></script>
<script src="/web/js/vue-resource.min.js"></script>
<script src="/web/js/axios.js"></script>
<script>
    new Vue({
        el: "#tts",
        data() {
            return {
                listData:[],
            }
        },
        methods: {
            // 获取数据
            getData() {
                this.$http.get("{{url("api/getDailyExpress")}}").then(function (res) {
                     console.log(res)

                    if (res.status === 200) {
                        this.listData = res.body
                    } else {
                        alert(error.message)
                    }
                })
            },
        },
        mounted() {
            this.getData();
        }
    })
</script>
