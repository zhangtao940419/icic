<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TTS注册页面</title>
    <link rel="stylesheet" href="/web/css/register.css">
    <link rel="stylesheet" href="/web/css/musi-ui.css">
    <link rel="stylesheet" href="/web/css/material-icons.css">
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>

<body>
    <div id="app">

        <!-- 遮罩 s -->
        <div id="download">
            <p class>点击右上角按钮，然后在弹出的菜单中，点击在浏览器中打开</p>
            <p><img src="/web/img/jiantou.png" alt=""></p>
        </div>
        <!-- 遮罩 e -->

        <!-- 注册信息 -->
        <mu-container ref="user">
            <!-- 注册 -->
            <h1>注册</h1>


            <!-- 手机号码 -->
            <div class="phone">
                <mu-text-field v-model="user.value1" full-width type="number" color="#FA6055" placeholder="请输入电话号码"></mu-text-field>
            </div>

            <!-- 验证码 -->
            <div class="authcode">
                <mu-text-field v-model="user.value2" full-width type="text" color="#FA6055" placeholder="请输入验证码"></mu-text-field>
                <div id="btn" @click="authcode()" v-show="user.sendAuthCode">
                    <span>发送验证码</span>
                </div>
                <div id="btns" v-show="!user.sendAuthCode">
                    <span class="auth_text_blue" v-html="user.auth_time"></span>秒后重发
                </div>
            </div>

            <!-- 输入密码 -->
            <div class="password">
                <mu-text-field v-model="user.value3" full-width placeholder="请设置密码" color="#FA6055" :action-icon="user.visibility ? 'visibility_off' : 'visibility'"
                    :action-click="() => (user.visibility = !user.visibility)" :type="user.visibility ? 'text' : 'password'"></mu-text-field>
            </div>

            <!-- 再输入密码 -->
            <div class="aginpassword">
                <mu-text-field v-model="user.value4" full-width placeholder="请再次输入密码" color="#FA6055" :action-icon="user.visibility2 ? 'visibility_off' : 'visibility'"
                    :action-click="() => (user.visibility2 = !user.visibility2)" :type="user.visibility2 ? 'text' : 'password'"></mu-text-field>
            </div>

            <!-- 请输入邀请码 -->
            <div class="invitation">
                <mu-text-field v-model="user.value5" full-width type="text" color="#FA6055" placeholder="请输入邀请码(选填)" disabled></mu-text-field>
            </div>

            <!-- 点击注册 -->
            <div class="sets">
                <mu-flex justify-content="center" align-items="center">
                    <mu-button full-width large color="#FA6055" @click="register()"><span>注册</span></mu-button>
                </mu-flex>
            </div>
        </mu-container>

        <br><br>
        <a style="color: grey; margin-left: 8%" href="/download?invitation_code={{ request('invitation_code') }}">下载app</a>
    </div>
    <body onload="is_weixin()">
    <script src="/web/js/jquery.min.js"></script>
    <script src="/web/js/vue.js"></script>
    <script src="/web/js/muse-ui.js"></script>
    <script src="/web/js/vue-resource.min.js"></script>
    <script>

        // 验证微信浏览器
        function is_weixin() {
            var ua = navigator.userAgent.toLowerCase();
            var vs = document.getElementById("download")
            if (ua.match(/MicroMessenger/i) == "micromessenger") {
                // alert("yes");
                vs.style.display = "block"
            } else {
                // alert("no");
                return false

            }
        }

        //验证手机号码
        function isPhone(str) {
            const regexp2 = /^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(17[0,6-8])|(18[0-9])|199)[0-9]{8}$/
            if (regexp2.test(str)) {
                return true
            } else {
                return false
            }
        }

        new Vue({
            el: "#app",
            data() {
                return {
                    user: {
                        value1: '',
                        value2: '',
                        value3: '',
                        value4: '',
                        value5: '{{ request('invitation_code') }}',
                        visibility: false,
                        visibility2: false,
                        sendAuthCode: true,
                        auth_time: 0,
                    }
                }
            },

            methods: {
                // 验证手机号码
                addMenber() {
                    let user = this.user;
                    if (!isPhone(user.value1)) {
                        alert("请输入正确的手机号码")
                        return true
                    }
                },

                //点击发送验证码
                authcode() {
                    if (this.addMenber()) {
                        return
                    }
                    this.$http.get('{{url('api/sendCodeSMS')}}?phone=' + this.user.value1).then(res => {
                        console.log(res)
                        if (res.body.status_code == 200) {
                            alert('发送成功,请注意查收!5分钟内有效')
                        } else if (res.body.status_code == 1012 || res.body.status_code == false) {
                            alert('请勿重复发送')
                        }
                    })
                    this.user.sendAuthCode = false;
                    this.user.auth_time = 120;
                    var auth_timetimer = setInterval(() => {
                        this.user.auth_time--;
                        if (this.user.auth_time <= 0) {
                            this.user.sendAuthCode = true;
                            clearInterval(auth_timetimer);
                        }
                    }, 1000);
                },

                // 点击注册
                register() {
                    let user = this.user
                    if (!user.value1 || !isPhone(user.value1)) {
                        alert("请输入正确的手机号码")
                        return
                    }
                    if (!user.value2) {
                        alert("验证码不能为空")
                        return
                    }
                    if (!user.value3) {
                        alert("密码不能为空")
                        return
                    }
                    if (user.value3.length > 16 || user.value3.length < 6) {
                        alert("密码数为6~18位")
                        return
                    }
                    if (!user.value4) {
                        alert("请再次输入密码")
                        return
                    }
                    if (user.value3 !== user.value4) {
                        alert("密码不一致")
                        return
                    }
                    if (!user.value5) {
                        alert("邀请码不能为空")
                        return
                    }
                    this.$http.post("{{ url('api/register') }}", {
                        phone: this.user.value1,
                        code: this.user.value2,
                        password: this.user.value3,
                        re_password: this.user.value4,
                        invitation_code: this.user.value5
                    }).then(function (res) {
                        console.log(res)
                        if (res.body.status_code === 200) {
                            alert("注册成功，请登陆APP")
                            window.location.href="{{ url('/') }}"
                        } else {
                            alert(res.body.message)
                        }
                    })
                },

            },

            mounted() {

            },
        })
    </script>
</body>

</html>