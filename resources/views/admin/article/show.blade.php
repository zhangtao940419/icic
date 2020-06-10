@extends('admin.layouts.app')
@section('title', "文章:" . $article->title)
@section('content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{ url('/admin') }}">首页</a>
            </li>

            <li>
                <a href="{{ route('article.index') }}">文章列表</a>
            </li>
            <li>
                文章详细
            </li>
        </ul>
        <!-- /.breadcrumb -->
    </div>
    <div class="space-4"></div>
    <div class="space-4"></div>
    <div class="space-4"></div>

    {{--引入信息提示页面--}}
    @include('admin.layouts._message')
    <div class="row" style="margin-left: 20em;">
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 topic-content center">
            <div class="panel panel-default">
                <div style="margin-bottom: 30px; border-bottom: 1px solid black;">
                    <h1>封面图片</h1>
                    <img src="{{ $article->cover }}">
                </div>
                <div class="panel-body">
                    <h1 class="text-center">
                        {{ $article->title }}
                    </h1>

                    <div class="article-meta text-center">
                        {{ $article->created_at->diffForHumans() }}
                        ⋅
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                        {{ $article->user->username }}
                        ⋅
                        <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>
                        {{ $article->reply_count }}
                    </div>

                    <div class="topic-body" style="text-align: left">
                        {!! $article->body !!}
                    </div>
                </div>
                <div class="operate">
                    <hr>
                    <div style="padding-bottom: 20px;">
                        <a href="{{ route('article.edit', $article->id) }}" class="btn btn-default btn-xs btn-info" role="button">
                            <i class="glyphicon glyphicon-edit"></i> 编辑
                        </a>
                    </div>
                    <form action="{{ route('article.destroy', $article->id) }}" method="post">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-danger btn-xs pull-left" style="position: relative; left: 45em; top: -50px;">
                            <i class="glyphicon glyphicon-trash"></i>
                            删除
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection