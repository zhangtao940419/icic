<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>app下载</title>
    <link rel="stylesheet" href="/assets/css/media.css">
    <link rel="stylesheet" href="/assets/css/download.css">
    <style>
        .down .gozc {
            border: 1px solid #3e3838;
            height: .8rem;
            line-height: .8rem;
            margin: 0 .4rem;
            border-radius: 5px;
            text-align: center;
            margin-bottom:0.3rem;
        }

        .down .gozc span {
            
            font-size: 0.3rem;
            color: #3e3838;
            margin-top: 1rem;
            vertical-align: top;
            letter-spacing: 0.03em;
        }

        /* 遮罩 s */
        #download {
            /* height: 1rem; */
            position: absolute;
            background: rgba(0, 0, 0, 0.7);
            width: 100%;
            height: 200%;
            font-size: 16px;
            color: #eee;
            z-index: 999;
            margin-top: -20px;
            display: none;
        }

        #download p:nth-child(1) {
            float: left;
            width: 78%;
            margin-top: 1rem;
            margin-left: 1rem;
        }

        #download p:nth-child(2) {
            float: right;
            width: 16%;
            margin-top: 1.5rem;
        }

        #download p:nth-child(2) img {
            width: 1rem;
            height: 1rem;
            /* margin-left: .1rem; */
        }
        /* 遮罩 e */
    </style>
</head>

<body>
<!-- download s -->
<div class="download" id="download">
    <p class>点击右上角按钮，然后在弹出的菜单中，点击在浏览器中打开，即可安装</p>
    <p><img src="/assets/img/jiantou.png" alt=""></p>
</div>
<!-- download e -->

<div>
    <!-- app header s -->
    <div class="app_header">
        <div class="img">
            <img src="/assets/img/logo.png" alt="">
        </div>
        <div class="Tts">
            <span style="font-size: 32px;color: #3e3838;">TTS</span>
        </div>
    </div>
    <!-- app header e -->

    <!-- down s -->
    <div class="down">
        <div class="gozc">
            <a href="{{ url('register') }}?invitation_code={{ $invitationCode }}">
                <span>注册账号</span>
            </a>
        </div>
        <div class="android">
            <a href="{{ \App\Model\Admin\AppVersion::where(['phone_type'=>'android'])->latest('id')->first()->update_url }}" target="_blank">
                <img src="/assets/img/android.png" alt="">
                <span>Android版本下载</span>
            </a>
        </div>
        <div class="ios">
            <a href="{{ \App\Model\Admin\AppVersion::where(['phone_type'=>'ios'])->latest('id')->first()->update_url }}" target="_blank">
                <img src="/assets/img/ios.png" alt="">
                <span>iOS版本下载</span>
            </a>
        </div>
    </div>
    <!-- down e -->

    <!-- about s -->
    <div class="about">
        <h3>关于我们:</h3>
        <span>Tts以區塊鏈技術爲基礎，以平等透明的交易原則爲根本，接受所有用戶的監督，是一個安全穩定、公開透明、平等互信的去中心化、智能多層級的數字貨币服務平台。
Tts隸屬于智链国际，是世界領先的區塊鏈貨币場外交易平台, 是您可以信賴的專業區塊鏈貨币交易商，總部設在美國華爾街。
Tts是不涉及第三方的P2P交易平台，能夠實現快速買賣，交易過程方便快捷。Tts是安全可靠的交易平台，擁有冷存儲、SSL、多重加密等銀行級别安全技術和十年金融安全經驗的安全團隊。Tts平台交易随時随地，WEB、APP行情及時掌握。</span>
    </div>
    <!-- about e -->
</div>

</body>

</html>
<body onload="is_weixin()">
<script type="text/javascript" src="https://cdn.ywxi.net/js/1.js" async></script>
<script>
    // 验证微信浏览器
    function is_weixin() {
        var ua = navigator.userAgent.toLowerCase();
        var vs = document.getElementById("download");
        if (ua.match(/MicroMessenger/i) == "micromessenger") {
            // alert("yes");
            vs.style.display = "block"
        } else {
            // alert("no");
            return false

        }
    }
</script>
