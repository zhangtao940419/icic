<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Model\Admin\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Category $category)
    {
        $categories = $category->getTree(Category::all());
        $count = $category->count();
        $categories = $this->setPage2($request, $categories, 15, $count);

        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        $categories = $category->getTree(Category::all());

        return view('admin.category.create_and_edit', compact('category', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
           'name' => 'required',
           'parents_id' => 'required',
           'description' => 'required'
        ]);

        Category::create($request->all());
        event(new AdminUserBehavior(auth('web')->user()->id,"新增文章分类",'c文章分类'));
        return redirect()->route('category.index')->with('success', '创建成果');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $categories = $category->getTree(Category::all());

        return view('admin.category.create_and_edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $category->update($request->all());
        $category->save();
        event(new AdminUserBehavior(auth('web')->user()->id,"修改文章分类",'c文章分类'));
        return redirect()->route('category.index')->with('success', '编辑成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $result = $category->delete();

        $data = ['该商品下面还有子分类，或者该分类下还有文章,不能删除'];

        if ($result) {
            return [];
        } else {
            return $data;
        }
    }

    public function setPage2(Request $request, $data, $prepage, $total)
    {
        #每页显示记录
        $prePage = $prepage;
        $allitem = $prepage *100;
        $total > $allitem ? $total = $allitem : $total;
        if(isset($request->page)){
            $current_page =intval($request->page);
            $current_page =$current_page<=0?1:$current_page;
        }else{
            $current_page = 1;
        }
        #url操作
        $url = $url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
        if(strpos($url,'?page')) $url=str_replace('?page=' . request()->page, '',$url);

        # $data must be array
        $item =array_slice($data,($current_page-1)*$prePage,$prePage);
        $paginator = new LengthAwarePaginator($item,$total,$prePage,$current_page,[
            'path'=>$url,
            'pageName'=>'page'
        ]);

        return $paginator;
    }
}
