<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 17:23
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class UserIdentify extends Model
{
    /*表名称*/
    protected $table = 'users_identify';

    protected $primaryKey = 'identify_id';
    /**
     * 应被转换为日期的属性。
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'identify_type',
        'identify_name',
        'identify_card',
        'user_id',
        'identify_is_pass',
        'is_usable',
        'identify_card_z_img',
        'identify_card_f_img',
        'identify_card_h_img',
        'identify_sex',
        'created_at',
        'updated_at',
    ];

    public function getOneRecordByCard($identifyCard)
    {
        return $this->where('identify_card',$identifyCard)->where('is_usable',1)->first();
    }

    public function getOneRecordByUserId(int $userId)
    {
        return $this->where('user_id',$userId)->where('is_usable',1)->first();
    }

    /*更新记录*/
    public function updateOneRecordByUserId(int $userId,array $data)
    {
        return $this->where(['user_id'=>$userId,'is_usable'=>1])->update($data);
    }

    public function saveOneRecord(array $data)
    {
        if ($this->getOneRecordByUserId($data['user_id'])) return 0;
        $this->identify_name = $data['identify_name'];
        $this->identify_card = $data['identify_card'];
        $this->user_id = $data['user_id'];
        return $this->save();
    }

    public function getOneRecordC($userId,$userName,$idCard)
    {
        return $this->where(['user_id'=>$userId,'identify_name'=>$userName,'identify_card'=>$idCard])->first();
    }

    //关联用户表
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'user_id');
    }


    public function getstatu()
    {
        return [
            0 => '未认证',
            1 => '待审核',
            2 => '通过审核',
            3 => '未通过审核'
        ];
    }


    public function user_identify_area()
    {
        return $this->belongsTo(UserIdentifyArea::class,'identify_area_id','id');
    }








}