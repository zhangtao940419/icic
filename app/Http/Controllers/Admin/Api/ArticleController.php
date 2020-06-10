<?php

namespace App\Http\Controllers\Admin\Api;

use App\Model\Admin\Article;
use App\Model\Admin\Category;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ArticleController extends Controller
{
    use Helpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function newIndex(Request $request)
    {
        $category_id = $request->input('category_id',4);

        $res = Article::where('category_id', $category_id)->latest()->get();

        return response()->json($res);
    }

    public function getDailyExpress()
    {
        $category_id = 10;

        $res = Article::where('category_id',$category_id)->latest()->get();

        return response()->json($res);
    }

    public function helpIndex()
    {
        $res = Article::where('category_id', 2)->get();

        return response()->json($res);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function articleshow($id)
    {
        $res = Article::with('user', 'category')->where('id', $id)->get()->toArray();

        $data = [];
        foreach ($res as $v){
            $v['username'] = $v['user']['username'];
            $v['category_name'] = $v['category']['name'];
            unset($v['user']);
            unset($v['category']);
            $data[] = $v;
        }

        return response()->json(['data' => $data]);
    }

    //获取新闻资讯下所有子分类
    public function news_categorys()
    {
        $news_category_id = 4;

        $categorys = Category::query()->where('parents_id',$news_category_id)->select('id','name')->get();

        return response()->json(['status_code'=>200,'message'=>'操作成功','data'=>$categorys]);
    }

}
