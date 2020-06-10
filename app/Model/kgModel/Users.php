<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/7
 * Time: 16:32
 */

namespace App\Model\kgModel;


use Illuminate\Database\Eloquent\Model;

class Users extends Model
{



    protected $connection = 'mysql_hjkg';

    protected $table = 'kcode_user';

    protected $primaryKey = 'user_id';

    public $timestamps = false;



    public function user_icic_wallet()
    {
        return $this->hasOne(UserWallet::class,'user_id','user_id')->where('coin_id',2);
    }















}