<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Model\CoinType;
use App\Traits\RedisTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserInvitationController extends Controller
{
    use RedisTool;
    public function index()
    {
        $coins = CoinType::all();
        $invitation = $this->redisExists('Invitation_set') ? unserialize($this->stringGet('Invitation_set')) : [];

        return view('admin.invitation.index' , compact('invitation', 'coins'));
    }

    public function post(Request $request)
    {
        $coins = CoinType::all();

        $invitation = unserialize($this->stringGet('Invitation_set'));

        if ($request->isMethod('GET')) {
            return view('admin.invitation.create_or_edit', compact('invitation', 'coins'));
        } elseif($request->isMethod('POST')) {
            $this->validate($request, [
                'coin_id' => 'required',
                'coin_num' => 'required',
            ]);
            $data = $request->except('_token');

            $this->stringSet('Invitation_set', serialize($data));
            event(new AdminUserBehavior(auth('web')->user()->id,"邀请奖励设置",'邀请奖励设置'));

            return redirect()->route('invitation.index')->with('success', '创建成功');
        }
    }

}
