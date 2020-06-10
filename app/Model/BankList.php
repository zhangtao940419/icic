<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 14:41
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BankList extends Model
{


    protected $table = 'bank_list';

    protected $primaryKey = 'bank_id';

    public function getRecords()
    {

        return $this->where('is_usable',1)->get()->toArray();

    }

    public function getRecordById(int $bankId)
    {
        return $this->find($bankId)->toArray();
    }


    public function getBankByEnCode($code)
    {
        return $this->where(['bank_en_name' => $code])->first();
    }




}