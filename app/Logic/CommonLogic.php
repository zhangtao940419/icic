<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 16:43
 */

namespace App\Logic;
use App\Http\Response\ApiResponse;
use App\Model\Notice;
use App\Server\UserServers\Dao\UserDao;

class CommonLogic
{
    use ApiResponse;

    public function getInvitationCode($data)
    {
        $userInvitationCode = UserDao::find($data['user_id'],['user_Invitation_code']);
        $url = env('DOMAIN_NAME',$_SERVER['HTTP_HOST']);
        $qrCodeMsg = 'http://' . $url . '/register?invitation_code=' . $userInvitationCode->user_Invitation_code;
        return $this->successWithData(['qr_code'=>$qrCodeMsg]);
    }

    public function getNewNotice()
    {
        return $this->successWithData((new Notice())->getNewNotice());
    }

}