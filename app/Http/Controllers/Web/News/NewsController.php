<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/30
 * Time: 15:14
 */

namespace App\Http\Controllers\Web\News;

use App\Http\Controllers\Web\BaseController;

class NewsController extends BaseController
{

    public function index()
    {

        return view('news.news');
    }

    public function daily_express()
    {
        return view('news.daily_express');
    }

    public function detail()
    {
        return view('news.detail');
    }



}
