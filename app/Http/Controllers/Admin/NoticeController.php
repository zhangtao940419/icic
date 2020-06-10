<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Handlers\ImageUpload;
use App\Model\Notice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::where('is_usable', 1)->paginate();

        return view('admin.notice.index', compact('notices'));
    }

    public function create(Notice $notice)
    {
        return view('admin.notice.create_or_edit', compact('notice'));
    }

    public function store(Request $request, ImageUpload $uploader)
    {
        $this->validate($request, [
            'notice_content' => 'required'
        ]);

        $data = $request->all();

        if ($request->notice_img) {
            $result = $uploader->save($request->notice_img, 'notice');
            if ($result) {
                $data['notice_img'] = $result['path'];
            }
        }

        Notice::create($data);
        event(new AdminUserBehavior(auth('web')->user()->id,"新增app公告",'app公告'));

        return redirect()->route('notice.index')->with('success', '添加成功');
    }



    public function edit(Notice $notice)
    {
        return view('admin.notice.create_or_edit', compact('notice'));
    }


    public function update(Request $request, Notice $notice, ImageUpload $uploader)
    {
        $data = $request->all();

        if ($request->notice_img) {
            $result = $uploader->save($request->notice_img, 'notice');
            if ($result) {
                $data['notice_img'] = $result['path'];
            }
        }

        $notice->update($data);
        event(new AdminUserBehavior(auth('web')->user()->id,'修改app公告','app公告'));

        return redirect()->route('notice.index')->with('success', '操作成功');
    }

    public function destroy(Notice $notice)
    {
        $notice->is_usable = 0;
        $notice->save();

        return back()->with('success', '操作成功');
    }
}
