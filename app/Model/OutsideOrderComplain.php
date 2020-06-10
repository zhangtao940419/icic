<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OutsideOrderComplain extends Model
{
    /*表名称*/
    protected $table = 'outside_order_complain';

    protected $primaryKey = 'complain_id';

    protected $fillable = ['user_id','order_number','complain_title','complain_content','complain_status','complain_reply'];

    private    $fields =['user_id','order_number','complain_title','complain_content','complain_status','complain_reply'];

    private    $insert_fielids=[];

  //  protected $fillable = ['coin_name'];


    /*
     * 插入投诉表
     *
     * return $complain_id 返回插入的id
     */
    public function saveComplain($complain){

        foreach ($complain as $key=>$value){
            if(in_array($key,$this->fields)){
                $this->insert_fielids[$key]=$value;
            }
        }

       if($this->create($this->insert_fielids)->complain_id) return 1;
        return 0;

    }


}
