<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>新闻列表</title>
    <link rel="stylesheet" href="/web/css/muse-ui.css">
    <link rel="stylesheet" href="/web/css/news.css">
    <style>
        [v-cloak] {
            display: none;
        }
        body {
            background: #1F202A;
        }
    </style>
</head>

<body>
<div id="app">
    <mu-container>
        <mu-tabs color="#1F202A" indicator-color="#2ACC82" full-width>
            <mu-tab v-for="(item,index) in categorys" :key="index"  @click="tabChange(item.id)" >@{{ item.name }}</mu-tab>
            {{--<mu-tab @click="dailyExpress()" >每日快讯</mu-tab>--}}
        </mu-tabs>
        <div class="demo-text">
            <div class="se_1 clearfix" v-for="(item,index) in listData" :key="index" @click="goto(index)" v-cloak>
                <div class="left">
                    <img :src="item.cover" alt="">
                </div>
                <div class="right">
                    <p class="titles">@{{item.title}}</p>
                    <p class="times">@{{item.created_at}}</p>
                    <p class="contents">@{{item.excerpt}}</p>
                </div>
            </div>
        </div>
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
                default_category_id:4,
                categorys:[],
                listData:[
                    // {
                    //     img:"./img/timg.jpg",
                    //     title:"区块链阿达啊实打实大苏打",
                    //     times:"2015-12-01",
                    //     content:"在最近的阿拉伯商业创业学院上发言的专家表示，区块链技术将很快改变几个行业的商业运作外汇汇兑..."
                    // },
                    // {
                    //     img:"./img/1.jpg",
                    //     title:"区块链",
                    //     times:"2015-12-02",
                    //     content:"在最近的阿拉伯商业创业学院上发言的专家表示，区块链技术将很快改变几个行业的商业运作外汇汇兑..."
                    // }
                ],

            }
        },
        methods: {
            // 获取新闻分类
            getCategorys() {
                this.$http.get("{{url("api/article/news_categorys")}}").then(function (res) {
                    if (res.status === 200) {
                        this.categorys = res.data.data;
                        this.categorys.push({id:99,name:'每日快讯'});
                        console.log(this.categorys)
                        if(res.data.data[0].id){
                            this.default_category_id = res.data.data[0].id;
                        }
                        // console.log(this.default_category_id);
                        this.getnewsList(this.default_category_id);
                    } else {
                        alert(error.message)
                    }
                })
            },
            // 获取新闻列表
            getnewsList(category_id = 4) {
                this.$http.get("{{url("api/news")}}?category_id=" + category_id).then(function (res) {
                    // console.log(res)
                    if (res.status === 200) {
                        this.listData = res.body
                    } else {
                        alert(error.message)
                    }
                })
            },

            tabChange(value){
                // console.log(value);
                if(value == 99){
                    this.dailyExpress();
                }
                this.getnewsList(value)
            },

            // 跳转页面
            goto(index) {
                // console.log(index)
                window.location.href = "{{ url('news/detail') }}?id=" + this.listData[index].id
            },
            dailyExpress(){
                window.location.href = "{{ url('news/daily_express') }}"
            }
        },
        mounted() {
            this.getCategorys();
            console.log(this.default_category_id);
        }
    })
</script>
</body>

</html>
