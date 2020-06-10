<?php

namespace App\Observers;
use App\Http\Requests\Request;
use App\Model\Admin\Article;
use App\Model\Admin\Category;

class CategoryObserve
{
    public function deleting(Category $category)
    {
        $res = Category::where('parents_id', $category->id)->get()->toArray();

        $article = Article::where('category_id', $category->id)->get()->toArray();

        if ($res || $article) {
            return false;
        }
    }
}
