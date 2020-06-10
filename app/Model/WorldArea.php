<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WorldArea extends Model
{
    /*表名称*/
    protected $table = 'world_area';

    protected $primaryKey = 'country_id';

    private $fields =['country_id','country_cn_abbreviate','country_en_name','currency_code'];

   /*获取所有的地区信息*/
  public function getWorldArea():array
  {

      return $this->with('currency')->select($this->fields)->where('is_usable',1)->get()->toArray();

  }


  public function currency()
  {
      return $this->hasOne(WorldCurrency::class,'currency_code','currency_code');
  }




}
