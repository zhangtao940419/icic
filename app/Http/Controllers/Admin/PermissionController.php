<?php

namespace App\Http\Controllers\Admin;
use App\Model\Admin\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Permission $permission)
    {

        $permissions = $permission->getTree(Permission::all());
        $count = $permission->count();
        $permissions = $this->setPage2($request, $permissions, 15, $count);

        return view('admin.permission.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Permission $permission)
    {
        return view('admin.permission.create_or_edit', compact('permission'));
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
            'route' => 'required'
        ]);

        Permission::create($request->all());

        return redirect()->route('permission.index')->with('success', '创建成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        return view('admin.permission.create_or_edit',compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $permission->update($request->all());
        $permission->save();

        return redirect()->route('permission.index')->with('success', '编辑成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $res = $permission->delete();

        if ($res) {
            \DB::table('permission_user')->where('permission_id', $permission->id)->delete();
            return [];
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
//        dd($url);
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
