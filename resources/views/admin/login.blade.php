<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>ICIC后台管理系统登陆</title>

    <meta name="description" content="User login page" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/font-awesome/4.5.0/css/font-awesome.min.css" />

    <!-- text fonts -->
    <link rel="stylesheet" href="/assets/css/fonts.googleapis.com.css" />

    <!-- ace styles -->
    <link rel="stylesheet" href="/assets/css/ace.min.css" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="/assets/css/ace-part2.min.css" />
    <![endif]-->
    <link rel="stylesheet" href="/assets/css/ace-rtl.min.css" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="/assets/css/ace-ie.min.css" />
    <![endif]-->

    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

    <!--[if lte IE 8]>
    <script src="/assets/js/html5shiv.min.js"></script>
    <script src="/assets/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-layout">
<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">
                    <div class="center">
                        <h1>
                            <i class="ace-icon fa fa-leaf green"></i>
                            <span class="red">ICIC</span>
                            <span class="white" id="id-text2">Application</span>
                        </h1>
                        <h4 class="blue" id="id-company-text">&copy; Company Name</h4>
                    </div>

                    <div class="space-6"></div>

                    <div class="position-relative">

                        <!-- 登陆页面 s -->
                        <div id="login-box" class="login-box visible widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header blue lighter bigger">
                                        <i class="ace-icon fa fa-coffee green"></i>
                                        请输入账号密码
                                    </h4>

                                    @include('admin.layouts._message')
                                    @include('admin.layouts._error')

                                    <div class="space-6"></div>

                                    <form action="{{ route('admin.login') }}" method="post">
                                            {{ csrf_field() }}
                                        <fieldset>
                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="text" class="form-control username" name="username" placeholder="用户名" />
                                                    <i class="ace-icon fa fa-user"></i>
                                                </span>
                                            </label>

                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="password" class="form-control password" name="password" placeholder="密码" />
                                                    <i class="ace-icon fa fa-lock"></i>
                                                </span>
                                            </label>

                                            <label class="block clearfix">
                                                <div style="height: 30px;">
                                                    <input type="text" name="code" class="code" style="display: inline-block; height: 42px; width:150px;">
                                                    <button class="btn btn-primary" id="seed" type="button" style="position: relative; width:135px;">发送验证码</button>
                                                </div>
                                            </label>
                                            <div class="space-4"></div>
                                            {{--<div class="box" id="div_geetest_lib">--}}
                                                {{--<div id="captcha"></div>--}}
                                            {{--</div>--}}

                                            <div class="space"></div>

                                            <div class="clearfix">
                                                <button type="submit" class="width-35 pull-right btn btn-sm btn-primary submit">
                                                    <i class="ace-icon fa fa-key"></i>
                                                    <span class="bigger-110">登录</span>
                                                </button>
                                            </div>

                                            <div class="space-4"></div>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- 登陆页面 e -->
                    </div>

                    <!--控制登陆页面主题 s -->
                    <div class="navbar-fixed-top align-right">
                        <br />
                        &nbsp;
                        <a id="btn-login-dark" href="#">黑色</a>
                        &nbsp;
                        <span class="blue">/</span>
                        &nbsp;
                        <a id="btn-login-blur" href="#">蓝色</a>
                        &nbsp;
                        <span class="blue">/</span>
                        &nbsp;
                        <a id="btn-login-light" href="#">白色</a>
                        &nbsp; &nbsp; &nbsp;
                    </div>
                    <!--控制登陆页面主题 e -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- basic scripts -->

<!--[if !IE]> -->
<script src="/assets/js/jquery-2.1.4.min.js"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="/assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='/assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>

<!-- inline scripts related to this page -->
<script type="text/javascript">
    jQuery(function($) {
        $(document).on('click', '.toolbar a[data-target]', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('.widget-box.visible').removeClass('visible');//hide others
            $(target).addClass('visible');//show target
        });
    });

    //you don't need this, just used for changing background
    jQuery(function($) {
        $('#btn-login-dark').on('click', function(e) {
            $('body').attr('class', 'login-layout');
            $('#id-text2').attr('class', 'white');
            $('#id-company-text').attr('class', 'blue');

            e.preventDefault();
        });
        $('#btn-login-light').on('click', function(e) {
            $('body').attr('class', 'login-layout light-login');
            $('#id-text2').attr('class', 'grey');
            $('#id-company-text').attr('class', 'blue');

            e.preventDefault();
        });
        $('#btn-login-blur').on('click', function(e) {
            $('body').attr('class', 'login-layout blur-login');
            $('#id-text2').attr('class', 'white');
            $('#id-company-text').attr('class', 'light-blue');

            e.preventDefault();
        });
    });
</script>

{{--<script src="https://static.geetest.com/static/tools/gt.js"></script>--}}
<script>
    // var handler = function (captchaObj) {
    //     // 将验证码加到id为captcha的元素里
    //     captchaObj.appendTo("#captcha");
    // };
    // $.ajax({
    //     // 获取id，challenge，success（是否启用failback）
    //     url: "/admin/captcha?rand="+Math.round(Math.random()*100),
    //     type: "get",
    //     dataType: "json", // 使用jsonp格式
    //     success: function (data) {
    //         // 使用initGeetest接口
    //         // 参数1：配置参数，与创建Geetest实例时接受的参数一致
    //         // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
    //         initGeetest({
    //             gt: data.gt,
    //             challenge: data.challenge,
    //             product: "float", // 产品形式
    //             offline: !data.success
    //         }, handler);
    //     }
    // });
</script>
<script>
    $(function () {
        var btnDisable = false;//发送按钮默认禁用

        if (btnDisable) {
            return;
        }

        $("#seed").click(function () {
            if ($('.username').val() == ''){alert('请输入账号');return false;}
            $.get("/admin/sendCodeSMS?username=" + $('.username').val() + '&czuser=' + $('.username').val() + '&des=登录后台' + '&type=login', function (result) {
                if (result.status_code != 200){
                    alert(result.message);
                }else {
                    timeWait(300);
                    btnDisable = true;
                }

            })
        });

        function timeWait(time) {
            setTimeout(function() {
                if (time >= 0) {
                    $("#seed").html(time + "s后重试");
                    time--;
                    timeWait(time);
                    $("#seed").attr("disabled", true)
                } else {
                    $("#seed").html("发送");
                    $("#seed").removeAttr("disabled")
                    btnDisable = false;
                }
            }, 1000)
        }

        $('.submit').click(function () {
            if ($('.username').val() == ''){alert('请输入账号');return false;}
            if ($('.password').val() == ''){alert('请输入密码');return false;}
            if ($('.code').val() == ''){alert('请输入验证码');return false;}
        })
    })
</script>
</body>
</html>
