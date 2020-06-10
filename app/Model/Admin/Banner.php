<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banner';

    protected $primaryKey = 'banner_id';

    protected $fillable = ['banner_imgurl', 'banner_tourl', 'is_usable', 'banner_tourl_type'];

    //获取轮播图信息
    public function getBanner()
    {
        return $this->where('is_usable', 1)->select(['banner_imgurl', 'banner_tourl', 'banner_tourl_type', 'banner_id'])->latest()->take(3)->get()->toArray();
    }


    public function getBannerImgurlAttribute($value)
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . $value;
    }


}
