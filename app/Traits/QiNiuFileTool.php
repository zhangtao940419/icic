<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/9/3
 * Time: 15:22
 */

namespace App\Traits;


use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

trait QiNiuFileTool
{


    /**
     * 上传单图片
     */
    public function qiniuuploadSingleImg($imageFile,$imageName = '')
    {
        $disk = \Storage::disk('qiniu');
      try{
        $re = $disk->put($imageName,$imageFile);
        return $re;
      }catch (\Exception $exception){
          return false;
      }
    }


//除图片之外的文件上传
    public function qiniuupload($file,$fileName = '')
    {
        $accessKey =config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        $bucket = config('filesystems.disks.qiniu.bucket');
        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);

        // 要上传文件的本地路径
        $filePath = $file;
// 上传到七牛后保存的文件名
        $key = $fileName;
// 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
// 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
//        echo "\n====> putFile result: \n";
        if ($err !== null) {
            return false;
            //dd($err);
        } else {
            return $ret['key'];
            dd($ret);
        }


    }



}