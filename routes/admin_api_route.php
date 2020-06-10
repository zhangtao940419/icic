<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Admin\Api',
    'middleware' => ['admin.cors'],
], function ($api) {
    //获取新闻资讯所有子分类
    $api->get('article/news_categorys', 'ArticleController@news_categorys')
        ->name('api.article.news_categorys');

    $api->get('article/category', 'ArticleController@category')
        ->name('api.article.category');

    //获取每日快讯
    $api->get('getDailyExpress', 'ArticleController@getDailyExpress')
        ->name('api.getDailyExpress');

    //获取新闻资讯
    $api->get('news', 'ArticleController@newIndex')
        ->name('api.news.index');

    //获取帮助中心文章
    $api->get('helps', 'ArticleController@helpIndex')
        ->name('api.help.index');

    //显示单条文章
    $api->get('new/{id}', 'ArticleController@articleshow')
        ->name('api.new.show');

    //个推推送信息
    $api->post('getui', 'GeTuiController@postMsg');

    //个推推送图片
    $api->post('getui', 'GeTuiController@postImg');
});
