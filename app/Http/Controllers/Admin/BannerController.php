<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Handlers\ImageUpload;
use App\Model\Admin\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Banner $banner)
    {
        $banners = $banner->where('is_usable', 1)->latest()->paginate(5);

        return view('admin.banner.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Banner $banner)
    {
        return view('admin.banner.create_and_edit', compact('banner'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ImageUpload $uploader)
    {
        $this->validate($request, [
            'banner_imgurl' => 'required',
            'banner_tourl' => 'required',
        ]);

        $data = $request->all();

        if ($request->banner_imgurl) {
            $result = $uploader->save($request->banner_imgurl, 'banner');
            if ($result) {
                $data['banner_imgurl'] = $result['path'];
            }
        }

        Banner::create($data);
        event(new AdminUserBehavior(auth('web')->user()->id,"新增轮播图",'操作轮播图'));
        return redirect()->route('banner.index')->with('success', '添加成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $banner)
    {
        return view('admin.banner.create_and_edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Banner $banner, ImageUpload $uploader)
    {
        $data = $request->all();

        if ($request->banner_imgurl) {
            $result = $uploader->save($request->banner_imgurl, 'banner');
            if ($result) {
                $data['banner_imgurl'] = $result['path'];
            }
        }

        $banner->update($data);
        event(new AdminUserBehavior(auth('web')->user()->id,"编辑轮播图",'操作轮播图'));
        return redirect()->route('banner.index')->with('success', '操作成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banner $banner)
    {
        $banner->update(['is_usable' => 0]);
        $banner->save();

        return redirect()->route('banner.index')->with('success', '操作成功');
    }
}
