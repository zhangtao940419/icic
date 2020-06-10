<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/6
 * Time: 14:41
 */
namespace App\Model;

use EloquentFilter\Filterable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use Filterable;

    protected $primaryKey = 'user_id';

    /*表名称*/
    protected $table = 'users';

    public function modelFilter()
    {
        return $this->provideFiltet(\App\ModelFilters\UserFilter::class);
    }

    // Rest omitted for brevity

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = [
//        'user_name', 'user_password', 'user_pay_password','user_email','user_headimg','is_usable','created_at','updated_at','user_auth_level', 'is_special_user'
//    ];
        protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_password', 'user_pay_password',
    ];

//    protected $appends = ['ts_phone'];

//    public function getTsPhoneAttribute()
//    {
//        return substr_replace($this->user_phone,'****',3,4);
//    }

    public function p_user()
    {
        return $this->belongsTo(User::class,'pid','user_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'pid','user_id');
    }

    public function direct_user_count()
    {
        return $this->children()->count();
    }

    /*自定义条件查询*/
    public function getOneRecord(array $data)
    {
        return $this->where($data)->first();
    }

    public function getOneData($condition,$data)
    {
        return $this->where($condition)->select($data)->first();

    }
    /*根据id获取用户*/
    public function getUserById(int $id,$column = [])
    {
        if ($column){
            return $this->find($id,$column);
        }
        return $this->find($id);
    }
    /*根据手机号获取用户*/
    public function getUserByPhone($phone)
    {
        return $this->where('user_phone',$phone)->first();
    }

    public function updateOneRecord(int $id,array $data)
    {
//        if ($this->find($id)->update($data)) return $this->find($id);return 0;
        return $this->find($id)->update($data);
    }
    /*保存用户信息*/
    public function saveOneRecord(array $data)
    {
        if ($this->getUserByPhone($data['phone'])) return 0;
        $this->user_name = $data['nickname'];
        $this->user_phone = $data['phone'];
        $this->user_password = $data['password'];
        $this->user_headimg = '/app/head_image/head_default.png';
        if ($this->save()) return $this;
        return 0;
    }

    public function getUserHeadimgAttribute($value)
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . $value;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getStatus()
    {
        return ['未认证', '初级认证', '高级认证'];
    }

    //关联用户场外等级信息
    public function user_grade()
    {
        return $this->hasOne('App\Model\OutsideUserGrade', 'id', 'outside_grade');
    }

    //关联用户高级认证信息表
    public function userIdentify()
    {
        return $this->hasOne('App\Model\UserIdentify', 'user_id', 'user_id');
    }



    //关联用户钱包表
    public function userWallet()
    {
        return $this->hasMany('App\Model\WalletDetail', 'user_id', 'user_id');
    }

    public function wallet_flows(){
        return $this->hasMany(WalletFlow::class,'user_id','user_id');
    }

    public function ore_pool_transfer_records(){
        return $this->hasMany(OrePoolTransferRecord::class,'user_id','user_id');
    }

    //发起人关联
    public function order()
    {
        return $this->hasMany('App\Model\OutsideTradeOrder', 'user_id', 'user_id');
    }

    //交易方关联
    public function orderPeople()
    {
        return $this->hasMany('App\Model\OutsideTradeOrder', 'trade_user_id', 'user_id');
    }

    //获取8个最新的用户
    public function getlatestUser()
    {
        return $this->where('is_usable', 1)->latest()->take(8)->get();
    }

    /*获取用户认证信息*/
    public function getUserAuthMsg(int $userId)
    {
        return $this->with('userName')->select('user_phone','user_id')->find($userId)->toArray();
    }

    /*关联用户姓名*/
    public function userName()
    {
        return $this->hasOne('App\Model\UserIdentify','user_id','user_id')->select('user_id','identify_name','identify_card');
    }

    public function coinorder()
    {
        return $this->hasMany('App\Model\CoinTradeOrder', 'user_id', 'user_id');
    }


    public function datum()
    {
        return $this->hasOne(UserDatum::class,'user_id','user_id')->select(['user_id','trade_total_num','trade_trust_num','trade_favourable_comment']);
    }

    public function get_s_user_num()
    {
        return $this->where(['pid'=>$this->user_id])->count();
    }

    public function get_s_user_top_auth_num()
    {

        return $this->where(['pid'=>$this->user_id,'user_auth_level'=>2])->count();
    }

}
