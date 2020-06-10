@extends('admin.layouts.app')
@section('title', '文章列表')
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                文章列表
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>

    <div class="nav-search" id="nav-search" style="position: absolute; top: 5.45em; left: 15em">
        <form class="form-search1">
            <span class="input-icon">
                <select class="nav-search-input" id="category" name="category">
                    <option value="">文章分类</option>
                    @foreach($categories as $category)
                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                    @endforeach
                </select>
            </span>
        </form>
    </div>

    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    <div class="page-header">
        <h1>
            文章管理
        </h1>
        <!-- add reset s -->
        <div class="hidden-sm hidden-xs btn-group" style="float: right;margin-right: 40px;margin-top: -30px;">

            <a href="{{ route('article.create') }}" class="btn btn-success">
                <i class="menu-icon glyphicon glyphicon-plus align-top bigger-125"></i>
                新增
            </a>

        </div>
        <!-- add reset e -->
    </div>

    {{--引入信息提示页面--}}
    @include('admin.layouts._message')

    <div class="row">
        <div class="col-lg-9 col-md-9 topic-list">
            @if (count($articles))

                <ul class="media-list">
                    @foreach ($articles as $article)
                        <li class="media">
                            <div class="media-left">
                                <a href="{{ route('article.show', [$article->user_id]) }}">
                                    <img class="media-object img-thumbnail" style="width: 52px; height: 52px;" src="/public/head_default.png" title="{{ $article->user->username }}">
                                </a>
                            </div>

                            <div class="media-body">
                                <div class="media-heading">
                                    <a href="{{ route('article.show', $article->id) }}" title="{{ $article->title }}">
                                        {{ $article->title }}
                                    </a>
                                </div>

                                <div class="media-body meta">

                                    <a href="#" title="{{ $article->category->name }}">
                                        <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
                                        {{ $article->category->name }}
                                    </a>

                                    <span> • </span>
                                    <a href="#" title="{{ $article->user->username }}">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                        {{ $article->user->username }}
                                    </a>
                                    <span> • </span>
                                    <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                                    <span class="timeago" title="最后活跃于">{{ $article->updated_at->diffForHumans() }}</span>
                                </div>

                            </div>
                        </li>

                        @if ( ! $loop->last)
                            <hr>
                        @endif

                    @endforeach
                </ul>

                {{-- 分页 --}}
                {!! $articles->render() !!}

            @else
                <div class="empty-block">暂无数据 ~_~ </div>
            @endif
        </div>
    </div>
@endsection
@section('myJs')
    <script>
        var search = {!! json_encode($search) !!};

        $('#category').on('change', function () {
            $(".form-search1").submit();
        });

        $("#category").val(search.category);
    </script>
@endsection
