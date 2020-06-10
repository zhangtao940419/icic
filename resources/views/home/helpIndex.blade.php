<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>帮助中心</title>
    <link rel="stylesheet" href="/web/css/helpCenter.css">
    <link rel="stylesheet" href="/web/css/muse-ui.css">
    <style>
        body {
            background: #1F202A;
        }
        [v-cloak] {
            display: none;
        }
    </style>
</head>

<body>
<div id="app">
    <p class="hotspot">热点问题</p>
    <mu-container style="margin: 0;padding: 0;" v-cloak>
        <mu-expansion-panel style="box-shadow: none;" v-for="(item,index) in helplist" :key="index" >
            <div slot="header" class="titles">@{{item.title}}</div>
            <span v-html="item.body" class="contents"></span>
        </mu-expansion-panel>
    </mu-container>


</div>
<script src="/web/js/vue.js"></script>
<script src="/web/js/muse-ui.js"></script>
<script src="/web/js/vue-resource.min.js"></script>
<script>
    new Vue({
        el: "#app",
        data() {
            return {
                helplist:[
                    {

                    }
                ]
            }
        },
        methods: {

            // 获取帮助中心列表
            gethelplist ( ) {
                this.$http.get("{{ url('api/helps') }}").then(function(res){
                    console.log(res)
                    if (res.status === 200) {
                        this.helplist = res.body
                        console.log(this.helplist)
                    } else {
                        alert(error.message)
                    }
                })
            },

        },
        mounted() {

            // 调用获取帮助中心列表方法
            this.gethelplist()
        },
    })
</script>
</body>

</html>