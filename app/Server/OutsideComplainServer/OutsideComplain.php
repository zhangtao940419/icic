<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/17
 * Time: 17:32
 */

namespace App\Server\OutsideComplainServer;

use App\Model\OutsideOrderComplain;
use App\Traits\FileTools;
use Illuminate\Support\Facades\DB;
use App\Model\ComplainImg;

class OutsideComplain
{
    use FileTools;
    private $complain_url='app/complain/';

    private $outsideOrderComplain;

    private $complainImg;

   // private $complain_error_message = '';

    public function __construct(OutsideOrderComplain $outsideOrderComplain,ComplainImg $complainImg ){

        $this->outsideOrderComplain =$outsideOrderComplain;

        $this->complainImg =$complainImg;

    }


    /* 用户投诉入库
      return -1 ;事务执行失败；
    */
    public function saveComplain($complain){

      foreach ($complain['complain_img'] as $key=>$value)
      {
         $complainImg[]=$this->complain_url.$this->putImage($value,date('Ym'),'complain');

      }
        DB::beginTransaction();
        $complain_id = $this->outsideOrderComplain->saveComplain($complain);
        if(!$complain_id>0){ DB::rollBack(); return -1;}
        $complainError=[];
        foreach ($complainImg as $value){
           if(!$this->complainImg->saveComplainImg($complain_id,$value)){
               $complainError[]=0;
           }
          if(count($complainError)<=0){
               DB::commit();
               return 1;
          }else{
              DB::rollBack();
              return -1;
          }
        }
    }
}