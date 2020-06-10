<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 17:14
 */

namespace App\Http\Controllers\Web\Api\V2;
use App\Http\Controllers\Web\BaseController;


class CommonController extends BaseController
{

    public function text()
    {
        return '123v2';
    }

}