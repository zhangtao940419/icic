<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/11/20
 * Time: 11:19
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ContractSetting extends Model
{

    protected $table = 'contract_setting';


    protected $primaryKey = 'id';




    public function getOne()
    {

        return $this->where(['is_usable'=>1])->first();


    }



}