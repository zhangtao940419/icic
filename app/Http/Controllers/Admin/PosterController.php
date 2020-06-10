<?php

namespace App\Http\Controllers\Admin;

use App\Model\Poster;
use App\Traits\QiNiuFileTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PosterController extends Controller
{
    use QiNiuFileTool;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posters = Poster::all();

        return view('admin.poster.index', compact('posters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Poster $poster)
    {
        return view('admin.poster.create_and_edit',compact('poster'));
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
            'imgurl' => 'required',
        ]);

        $data = $request->all();

        if ($request->imgurl) {
            $result = $this->qiniuuploadSingleImg($request->imgurl, 'poster');
            if ($result) {
                $data['imgurl'] = $result;
            }
        }

        Poster::create($data);

        return redirect()->route('poster.index')->with('success', '添加成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Poster $poster)
    {
        return view('admin.poster.create_and_edit', compact('poster'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Poster $poster)
    {
        $data = $request->all();

        if ($request->imgurl) {
            $result = $this->qiniuuploadSingleImg($request->imgurl, 'poster');
            if ($result) {
                $data['imgurl'] = $result;
            }
        }

        $poster->update($data);

        return redirect()->route('poster.index')->with('success', '操作成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Poster $poster)
    {
        $poster->delete();

        return redirect()->route('poster.index')->with('success', '操作成功');
    }
}
