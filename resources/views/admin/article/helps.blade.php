<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>帮助中心</title>
    <link rel="stylesheet" href="/assets/css/helpCenter.css">
    <link rel="stylesheet" href="https://unpkg.com/muse-ui/dist/muse-ui.css">
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>

<body>
<div id="app" style="background: white;">
    <p class="hotspot">热点问题</p>
    <mu-container style="margin: 0;padding: 0;" v-cloak>
        <mu-expansion-panel style="box-shadow: none;" v-for="(item,index) in helplist" :key="index" >
            <div slot="header">@{{item.title}}</div>
            <span v-html="item.body"></span>
        </mu-expansion-panel>
    </mu-container>

</div>
<script src="/assets/js/vue.js"></script>
<script src="https://unpkg.com/muse-ui/dist/muse-ui.js"></script>
<script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
<script>
    new Vue({
        el: "#app",
        data() {
            return {
                panel: '',
                panels: '',
                helplist:[
                    {

                    }
                ]
            }
        },
        methods: {

            // 获取帮助中心列表
            gethelplist ( ) {
                this.$http.get("http://47.91.225.168/api/helps").then(function(res){
                    console.log(res)
                    if (res.status === 200) {
                        this.helplist = res.body;
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