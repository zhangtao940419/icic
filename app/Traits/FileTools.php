<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/13
 * Time: 15:15
 */

namespace App\Traits;


trait FileTools
{


    /*上传单图片*/
    public function putImage($file,$path='',$disk = 'UploadImages')
    {
        if($file->getError() === 0){//上传错误个数
//            $mimeType = $file->getClientMimeType();//获取图片类型
//            if($mimeType=='image/jpeg' || $mimeType=='image/bmp' || $mimeType=='image/png' || $mimeType=='multipart/form-data'){
//                $size = $file->getClientSize();//获取图片大小
//                if($size>3000000) return 2;
//                    $filePath = $file->store($path,$disk);
//            }
            return $file->store($path,$disk);
        }
//        if (!empty($filePath)) return $filePath;return 0;
    }



    /*上传多图片*/
    public function putImages($arr, $path = '') {
        //多图片
        if($arr){
            foreach ($arr as $value) {
                $name = $value->getClientOriginalName();
                if($value->getError() === 0){//上传错误个数
                    $mimeType = $value->getClientMimeType();//获取图片类型
                    if($mimeType=='image/jpeg' || $mimeType=='image/gif' || $mimeType=='image/png'){
                        $size = $value->getClientSize();//获取图片大小
                        if($size<10000000){//小于2m
                            $path = $value->store($path,'UploadImages');
                        }
                    }
                }
            }
        }
        if(!empty($path))
            return response()->json(['name'=>$name,'shorturl'=>'/UploadImages/' . $path,'url'=> 'http://' . $_SERVER['HTTP_HOST'] . '/UploadImages/' . $path]);
        return response()->json(['error'=>'图片上传失败','code'=>404]);
    }
}