

<script src="/assets/js/jquery-2.1.4.min.js"></script>
<script src="/assets/js/jquery.colorbox.min.js"></script>

<!--[if IE]>
<script src="/assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
<script type="text/javascript">
    if ('ontouchstart' in document.documentElement) document.write(
        "<script src='/assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>
<script src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/excanvas.min.js"></script>

<script src="/assets/js/jquery-ui.custom.min.js"></script>
<script src="/assets/js/jquery.ui.touch-punch.min.js"></script>
<script src="/assets/js/jquery.easypiechart.min.js"></script>
<script src="/assets/js/jquery.sparkline.index.min.js"></script>
<script src="/assets/js/jquery.flot.min.js"></script>
<script src="/assets/js/jquery.flot.pie.min.js"></script>
<script src="/assets/js/jquery.flot.resize.min.js"></script>


<!-- ace scripts -->
<script src="/assets/js/ace-elements.min.js"></script>
<script src="/assets/js/ace.min.js"></script>

<!-- inline scripts related to this page -->

<!-- ace settings handler -->
<script src="/assets/js/ace-extra.min.js"></script>

<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

<!--[if lte IE 8]>
<script src="/assets/js/html5shiv.min.js"></script>
<script src="/assets/js/respond.min.js"></script>
<![endif]-->


<!-- 引入组件库 -->
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="/assets/js/jquery.colorbox.min.js"></script>


<script>
    $(document).ready(function() {
        // 删除按钮点击事件
        $('.btn-del').click(function() {
            var url = $(this).data('url');
            // 调用 sweetalert
            if (confirm('你确认删除吗?')) {
                // 调用删除接口，用 id 来拼接出请求的 url
                axios.delete(url)
                    .then(function (data) {

                        if (data.data != false) {
                            alert(data.data);
                        } else {
                            location.reload()
                        }
                    })
            }
        });
    });
</script>




