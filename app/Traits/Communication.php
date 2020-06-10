<?php

namespace App\Traits;

use App\Libs\Alibaichuan\OpenImUser;
use App\Libs\Alibaichuan\OpenimUsersAddRequest;
use App\Libs\Alibaichuan\OpenimUsersUpdateRequest;
use App\Libs\Alibaichuan\OpenimUsersGetRequest;
use App\Libs\Alibaichuan\OpenimChatlogsGetRequest;
use App\Libs\Alibaichuan\TopClient;
use App\Libs\Alibaichuan\Userinfos;


trait Communication
{
    public function getUser()
    {
        $c = new TopClient;
        $c->appkey = env('ALAppKey');
        $c->secretKey = env('ALAppSecret');
        $req = new OpenimUsersGetRequest;
        $req->setUserids(25);
        $resp = $c->execute($req);

        return $resp;
    }



    public function addUser($user)
    {
        $c = new TopClient;
        $c->appkey = env('ALAppKey');
        $c->secretKey = env('ALAppSecret');
        $req = new OpenimUsersAddRequest;
        $userinfos = new Userinfos;
        $userinfos->userid = $user->user_id;
        $userinfos->password = "btp123";
        $userinfos->icon_url = $user->avatar;
        $userinfos->name = $user->user_name;
        $userinfos->nick = $user->user_name;
        $req->setUserinfos(json_encode($userinfos));

        return $resp = $c->execute($req);
    }

    public function updateUser($user)
    {
        $c = new TopClient;
        $c->appkey = env('ALAppKey');
        $c->secretKey = env('ALAppSecret');
        $req = new OpenimUsersUpdateRequest;
        $userinfos = new Userinfos;
        $userinfos->userid = $user->user_id;
        $userinfos->password = "btp123";
        $userinfos->icon_url = $user->avatar;
        $userinfos->name = $user->user_name;
        $userinfos->nick = $user->user_name;
        $req->setUserinfos(json_encode($userinfos));

        return $resp = $c->execute($req);
    }

    //查看聊天记录
    public function getMsg($data)
    {
        $c = new TopClient;
        $c->appkey = env('ALAppKey');
        $c->secretKey = env('ALAppSecret');
        $req = new OpenimChatlogsGetRequest;
        $user1 = new OpenImUser;
        $user1->uid = $data['user_id1'];
        $user1->taobao_account="false";
        $req->setUser1(json_encode($user1));
        $user2 = new OpenImUser;
        $user2->uid = $data['user_id2'];
        $user2->taobao_account="false";
        $req->setUser2(json_encode($user2));
        $req->setBegin($data['begin']);
        $req->setEnd($data['end']);
        $req->setCount($data['count']);

        return json_decode(json_encode($c->execute($req)));
    }
}