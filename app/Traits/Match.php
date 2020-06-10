<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 14:50
 */
namespace App\Traits;


trait Match
{

    /*  计算两个高精度的浮点数
     *
     * @param $douleNum,$multiple
     *
     */
    public function Bcuml($douleNum,$multiple){

        return bcmul($douleNum,$multiple);

    }

    /* 相除两个高精度的浮点数，并选择相除后保存的小数后精度
     *   @param
     *  $douleNum,$multiple,$precision
     *
     */
    public function Bcdiv($douleNum,$multiple,$precision=10){

        return bcdiv($douleNum,$multiple,$precision);


    }

       /*相加两个高精度的数
         * $param
         *  $leftDouleNum
         *  $rightDouleNum
        */
       public function Bcadd($leftDouleNum,$rightDouleNum){

           return bcadd($leftDouleNum,$rightDouleNum);

       }

        /*相减两个高精度的数
         * $param
         *  $leftDouleNum
         *  $rightDouleNum
         */
    public function Bcsub($leftDouleNum,$rightDouleNum,$precision=10){

        return bcsub($leftDouleNum,$rightDouleNum,$precision=10);

    }


     /* 处理右边多个零的情况
      *
      *
      *
      */

    public function dealRigthZere($number){

        $newKey = rtrim($number,0);
        $Key =explode('.',$newKey);
        if(isset($Key[1]) && empty($Key[1])){
            $newKey = $newKey.'0';
        }

        return $newKey;

    }




}