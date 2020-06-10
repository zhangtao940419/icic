<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Handlers\ImageUpload;
use App\Model\Admin\Article;
use App\Model\Admin\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        $builder = Article::with(['category', 'user']);

        if($category = $request->input('category')){
            if( blank($ids = Category::getSubIds($category)) ){
                $builder->where('category_id',$category);
            }else{
                $ids[] = $category;
                $builder->whereIn('category_id',$ids);
            }
        }

        $search = array_merge($request->all(), ['count' => $builder->count()]);

        $articles = $builder->latest()->paginate(9);

        return view('admin.article.index', compact('articles','categories','search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Article $article)
    {
        $categories = Category::all();

        return view('admin.article.create_or_edit', compact('article', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ImageUpload $imageUpload)
    {
        $this->validate($request, [
           'category_id' => 'required',
           'title' => 'required',
           'body' => 'required',
        ]);

        $data = $request->except('_token');
        $data['user_id'] = \Auth::guard('web')->id();
        $data['excerpt'] = make_excerpt($request->body);

        if ($request->cover) {
            $res = $imageUpload->save($request->cover, 'cover', 'cover');
            $data['cover'] = $res['path'];
        }

        $rs = Article::create($data);
        event(new AdminUserBehavior(auth('web')->user()->id,"创建文章:{$request->title}",'操作文章'));
        return redirect()->route('article.show', $rs->id)->with('success', '创建成功！');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return view('admin.article.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        $categories = Category::all();

        return view('admin.article.create_or_edit', compact('article', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article, ImageUpload $imageUpload)
    {
        $data = $request->all();
        if ($request->cover) {
            $res = $imageUpload->save($request->cover, 'cover', 'cover');
            $data['cover'] = $res['path'];
        }

        $article->update($data);
        event(new AdminUserBehavior(auth('web')->user()->id,"编辑文章:{$request->title}",'操作文章'));
        return redirect()->route('article.show', $article->id)->with('success', '编辑成功！');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();
        event(new AdminUserBehavior(auth('web')->user()->id,"删除文章:{$article->title}",'操作文章'));
        return redirect()->route('article.index')->with('success', '成功删除！');
    }

    //富文本编辑器上传图片
    public function uploadImage(Request $request, ImageUpload $uploader)
    {
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($request->upload_file, 'article', 'article_image');
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }
}
