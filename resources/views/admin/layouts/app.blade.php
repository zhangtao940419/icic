<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title> @yield('title') </title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/font-awesome/4.5.0/css/font-awesome.min.css" />
    <!-- page specific plugin styles -->

    <link rel="stylesheet" href="/assets/css/colorbox.min.css" />

    <!-- text fonts -->
    <link rel="stylesheet" href="/assets/css/fonts.googleapis.com.css" />

    <!-- ace styles -->
    <link rel="stylesheet" href="/assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="/assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
    <![endif]-->
    <link rel="stylesheet" href="/assets/css/ace-skins.min.css" />
    <link rel="stylesheet" href="/assets/css/ace-rtl.min.css" />
    <link rel="stylesheet" href="/assets/css/jedate.css">
    @yield('myCss')
    <!--[if lte IE 9]>
    <link rel="stylesheet" href="/assets/css/ace-ie.min.css" />

    <![endif]-->
    <!-- inline styles related to this page -->

</head>
<body class="no-skin">
    {{--头部--}}
    <div id="navbar" class="navbar navbar-default ace-save-state">
        @include('admin.layouts._header')
    </div>
    {{--头部--}}

    {{--侧边栏--}}
    <div id="sidebar" class="sidebar responsive ace-save-state">
        @include('admin.layouts._sidebar')
    </div>
    {{--侧边栏--}}

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content" style="background: white;">

            {{--内容--}}
            @yield('content')
            {{--内容--}}
            </div>
        </div>
    </div>


    {{--底部--}}
    <div class="footer">
        @include('admin.layouts._footer')
    </div>
    {{--底部--}}

    {{--js--}}
    @include('admin.layouts._js')
    @yield('myJs')
    {{--js--}}
</body>

</html>
