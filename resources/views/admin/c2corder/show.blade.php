@extends('admin.layouts.app')
@section('title', 'c2c交易订单')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>
            <li>
                <a href="{{ route('c2cmessage.index') }}">c2c交易订单</a>
            </li>

            <li>
                c2c交易订单详情
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    @include('admin.layouts._message')
    <table id="simple-table" class="table table-bordered table-hover" style="width:60%; margin-left:15em; font-size: 20px;">
        <tbody>
        <tr>
            <th class="center">ID</th>
            <th class="center">{{ $c2corder->order_id }}</th>
        </tr>
        <tr>
            <th class="center">发起人</th>
            <th class="center">{{ $c2corder->tradeMsg->userMsg->user_name }}</th>
        </tr>
        <tr>
            <th class="center">交易方</th>
            <th class="center">{{ $c2corder->user->user_name }}</th>
        </tr>
        <tr>
            <th class="center">买卖类型</th>
            <th class="center"><span class="label label-xlg {{ $c2corder->tradeMsg->trade_type == 1 ? 'label-success' : 'label-danger'}}">{{ $c2corder->tradeMsg->trade_type == 1 ? '买入' : '卖出'}}货币</span></th>
        </tr>
        <tr>
            <th class="center">单价</th>
            <th class="center">{{ $c2corder->tradeMsg->trade_price }}</th>
        </tr>
        <tr>
            <th class="center">付款码</th>
            <th class="center">{{ $c2corder->order_pay_number }}</th>
        </tr>
        <tr>
            <th class="center">收款或付款的银行卡号</th>
            <th class="center">{{ $c2corder->bank_card_no }}</th>
        </tr>
        <tr>
            <th class="center">交易订单号</th>
            <th class="center">{{ $c2corder->order_number }}</th>
        </tr>
        <tr>
            <th class="center">需求货币类型</th>
            <th class="center">{{ $c2corder->tradeMsg->coin[0]->coin_name }}</th>
        </tr>
        <tr>
            <th class="center">出售数量</th>
            <th class="center">{{ $c2corder->tradeMsg->trade_number }}个</th>
        </tr>
        <tr>
            <th class="center">总价值</th>
            <th class="center">{{ $c2corder->tradeMsg->trade_number * $c2corder->tradeMsg->trade_price }}  cny</th>
        </tr>
        <tr>
            <th class="center">订单当前状态</th>
            <th class="center">{!! $c2corder->getOrderStatus()[$c2corder->order_status] !!}</th>
        </tr>
        <tr>
            <th class="center">订单创建时间</th>
            <th class="center">{{ $c2corder->created_at->diffForHumans() }}</th>
        </tr>
        @if($c2corder->order_status == 3)
            <tr>
                <th class="center">订单完成时间</th>
                <th class="center">{{ $c2corder->confirm_at }}</th>
            </tr>
        @endif
        <tr>
            <th class="center" style="line-height: 200px;">商家汇款的凭证图片</th>
            <th class="center pimg" onclick="showWindow()" style="cursor: pointer"> <img style="height: 200px; width: 200px;" src="{{ $c2corder->transfer_img }}"></th>
        </tr>
        </tbody>
    </table>


    {{--<td width="350">--}}
        {{--<img height="100" width="100" src="{{ $c2corder->transfer_img }}" class="pimg"/>--}}
    {{--</td>--}}



    <div id="outerdiv" style="position:fixed;top:0;left:0;background:rgba(0,0,0,0.7);z-index:2;width:100%;height:100%;display:none;">
        <div id="innerdiv" style="position:absolute;">
            <img id="bigimg" style="border:5px solid #fff;" src="{{ $c2corder->transfer_img }}" />
        </div>
    </div>
@endsection
@section('myJs')
<script>
    $(function(){
        $(".pimg").click(function(){
            var _this = $(this).children("img");//将当前的pimg元素作为_this传入函数
            imgShow("#outerdiv", "#innerdiv", "#bigimg", _this);
        });
    });

    function imgShow(outerdiv, innerdiv, bigimg, _this){
        var src = _this.attr("src");//获取当前点击的pimg元素中的src属性
        $(bigimg).attr("src", src);//设置#bigimg元素的src属性

        /*获取当前点击图片的真实大小，并显示弹出层及大图*/
        $("<img/>").attr("src", src).load(function(){
            var windowW = $(window).width();//获取当前窗口宽度
            var windowH = $(window).height();//获取当前窗口高度
            var realWidth = this.width;//获取图片真实宽度
            var realHeight = this.height;//获取图片真实高度
            var imgWidth, imgHeight;
            var scale = 0.8;//缩放尺寸，当图片真实宽度和高度大于窗口宽度和高度时进行缩放

            if(realHeight>windowH*scale) {//判断图片高度
                imgHeight = windowH*scale;//如大于窗口高度，图片高度进行缩放
                imgWidth = imgHeight/realHeight*realWidth;//等比例缩放宽度
                if(imgWidth>windowW*scale) {//如宽度扔大于窗口宽度
                    imgWidth = windowW*scale;//再对宽度进行缩放
                }
            } else if(realWidth>windowW*scale) {//如图片高度合适，判断图片宽度
                imgWidth = windowW*scale;//如大于窗口宽度，图片宽度进行缩放
                imgHeight = imgWidth/realWidth*realHeight;//等比例缩放高度
            } else {//如果图片真实高度和宽度都符合要求，高宽不变
                imgWidth = realWidth;
                imgHeight = realHeight;
            }
            $(bigimg).css("width",imgWidth);//以最终的宽度对图片缩放

            var w = (windowW-imgWidth)/2;//计算图片与窗口左边距
            var h = (windowH-imgHeight)/2;//计算图片与窗口上边距
            $(innerdiv).css({"top":h, "left":w});//设置#innerdiv的top和left属性
            $(outerdiv).fadeIn("fast");//淡入显示#outerdiv及.pimg
        });

        $(outerdiv).click(function(){//再次点击淡出消失弹出层
            $(this).fadeOut("fast");
        });
    }
</script>
@endsection