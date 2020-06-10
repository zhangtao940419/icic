<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShowArticleController extends Controller
{
    public function showNews()
    {
        return view('admin.article.news');
    }

    public function showNew($id)
    {
        return view('admin.article.new', compact('id'));
    }

    public function showHelps()
    {
        return view('admin.article.helps');
    }
}
