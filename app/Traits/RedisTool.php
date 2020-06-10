<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/5/8
 * Time: 10:23
 */

namespace App\Traits;

use App\Model\CoinType;
use Illuminate\Support\Facades\Redis;

trait RedisTool
{

    /*
     *  Redis hash类型存储，保存
     *  @param string $hashKey hash的键
     *  @param  array $hashValue hash的值
     * 说明： 返回值为2则说明，该键值已存在
     *        返回 1说明成功；
     *        返回 0 说明失败;
     */
    public function redisHmset(string $hashKey,array $hashValue){

        if(is_string($hashKey) && is_array($hashValue))
          {
              Redis::hmset($hashKey,$hashValue);
              return 1;
          }
        return 0;
    }
	

    /*
     * Redis hash 类型获取
     *  param string $hashKey
     *   return array;
     */
    public function redisHgetAll($hashKey){

         return  Redis::hgetall($hashKey);

    }
	
	/**
	 * hash表中元素递增
	 * @param $hashKey string hash表名
	 * @param $key string 键名
	 * @param $incNum int 递增的数值
	 */
	public function redisHincrby($hashKey, $key, $incNum){
		if(is_string($hashKey) && is_string($key))
          {
              Redis::hincrby($hashKey,$key,$incNum);  return 1;
          }
        return 0;
	}
	
	
	
	/**
	 *hash表操作 
	 * 浮点数递增
	 */
	public function redisHincrbyFloat($hashKey, $key, $incNum)
	{
		if(is_string($hashKey) && is_string($key))
          {
              Redis::hincrbyfloat($hashKey,$key,$incNum);  return 1;
          }
        return 0;
	}
	
	
	/**
	 * hash表中元素递减
	 * @param $hashKey string hash表名
	 * @param $key string 键名
	 * @param $decNum int 递减的数值
	 */
	public function redisHdecrby($hashKey, $key, $decNum){
		if(is_string($hashKey) && is_string($key) && is_numeric($decNum))
          {
              Redis::hincrby($hashKey,$key,-1 * $decNum);  return 1;
          }
        return 0;
	}
	
	/**
	 * 获取hash表中指定key的元素
	 * @param string or int $hashKey  hash表名
	 * @param string or int $key 指定键名
	 */
	public function redisHget($hashKey, $key){
		return Redis::hget($hashKey, $key);
	}
	
	/**
	 * hash表保存健值
	 * @param string $hashKey 表名
	 * @param string $key 键名
	 * @param string or int $value 值
	 */
	public function redisHset($hashKey, $key, $value){
		

//			if(!Redis::exists($hashKey)) return 2;
			Redis::hset($hashKey, $key, $value);


	}
	
	/**
	 * hash表追加元素
	 * key值不可重复
	 */
	public function redisHsetnx($hashKey, $key, $value){
		if(is_string($hashKey) && is_string($key) && is_string($value)){
			if(!Redis::exists($hashKey)) return 2;
			Redis::hsetnx($hashKey, $key, $value);return 1;
		}
		return 0;
	}
	
	


    /*
     * redis string 类型设置
     *如果键名存在不会覆盖
     * param string $key
     * param string $keyValue
     */
    public function stringSet($key,$keyValue){
//   if(Redis::exists($key)) return 2;
      if(is_string($key) && ($keyValue)){
          Redis::set($key,$keyValue); return 1;
      }
      return 0;

    }


     /*
      *  redis string 类型设置
      *  如果键名存在不会覆盖
      *  param string $key
      *  param string $keyValue
      */
    public function stringSetNx($key,$keyValue,$expireTTL = 5){

          return  Redis::set($key,$keyValue,'ex',$expireTTL,'nx');

    }

	
	/**
	 * 存放带时效性的数据
	 * 如已存在,不会覆盖原有值
	 * 类型:string
	 * @param string or int $key 键名
	 * @param int $time 有效时间
	 * @param string or int $value 值
	 */
	public function stringSetex($key, $expire, $value) {
//		if(Redis::exists($key)) return 2;
		if(is_string($key) && is_numeric($expire)){
			Redis::setex($key,$expire,$value);return 1;
		}
		return 0;
	}
	

    /*
     * redis string 类型设置
     * param string $key
     * return string
     */
    public function stringGet($key){

      return Redis::get($key);

    }
	
	/**
	 * 值的递增
	 */
	public function setIncrement($key, $incNum){
		//if(!Redis::exists($key)) return 2;
		if(is_string($key) && is_numeric($incNum)){
			Redis::incrby($key, $incNum);return 1;
		}
		return 0;
	}
	
	/**
	 * 字符串
	 * 浮点数递增
	 */
	public function setIncrementFloat($key,$incNum)
	{
		if(!Redis::exists($key)) return 2;
		if(is_string($key) && is_numeric($incNum)){
			Redis::incrbyfloat($key, $incNum);return 1;
		}
		return 0;
	}

	/*
	 * 哈希表的值递增
	 */
	public function hxIncrement($hxKey, $key, $incNum)
    {
        Redis::Hincrbyfloat($hxKey, $key, $incNum);
    }
	
	/**
	 * 值的递减
	 * @param $key 键名
	 * @param $decNum 递减的数目
	 */
	public function setDecrement($key, $decNum){
		if(!Redis::exists($key)) return 2;
		if(is_string($key) && is_numeric($decNum)){
			Redis::decrby($key, $decNum);return 1;
		}
		return 0;
	}
	
	/**
	 * 删除指定key
	 */
	public function redisDelete($key){
		if(is_string($key)){
			Redis::del($key);return 1;
		}
		return 0;
	}

	/**
     * 查询指定key是否存在
     */
	public function redisExists($key)
    {
        return Redis::exists($key);
    }

    /**
     * 检查验证码
     */
    public function checkCode($phone,$code)
    {
        if (!Redis::exists($phone)){
            return 0;
        }elseif ((int)$this->stringGet($phone) === (int)$code){
            return 1;
        }else{
            return 2;
        }

    }

    /*查询更新汇率
    参数1:目标货币;参数2:对标货币,默认是美元
    返回值:汇率(等价1单位对标货币的目标货币数值)
    */
    public function getCurrencyExchange($tcur,$scur='USD',$expire=7200)
    {
        if ($this->redisExists('EXCHANGE_'.$scur.'_'.$tcur)){
            return $this->stringGet('EXCHANGE_'.$scur.'_'.$tcur);
        }
        $result = json_decode(file_get_contents(sprintf(env('CURRENCY_EXCHANGE_URL'),$scur,$tcur)),true);
        if ($result['success']){
            $this->stringSetex('EXCHANGE_'.$scur.'_'.$tcur,$expire,round($result['result']['rate'],2));
            return round($result['result']['rate'],2);
        }
        return 0;
    }


    //获取某某虚拟货币兑换成其他货币的数量(默认人民币)
    public function changeTo_Other_Coin($base_coin_id, $country_code='CNY', $num=1)
    {
        $usdt_coin_id = CoinType::where('coin_name', 'QC')->pluck('coin_id')->first();

        //交易对redis的键
        $key = strtoupper('INSIDE_TEAM_' . $usdt_coin_id . '_' . $base_coin_id);

//        if (!$this->redisExists($key)) return 0;
        $res = $this->redisHgetAll($key);
        $cny_id = \DB::table('world_currency')->where('currency_code', 'CNY')->pluck('currency_id')->first();
        $qc_cuy_rate = \DB::table('coin_exchange_rate')->where(['virtual_coin_id' => $usdt_coin_id, 'real_coin_id' => $cny_id])->pluck('rate')->first();
        if(!empty($res)) {
            //dd($res['current_price'] * $num * $qc_cuy_rate);
             return round($res['current_price'] * $num * $qc_cuy_rate, 6);


        } else {


            return round(\DB::table('coin_exchange_rate')->where(['virtual_coin_id' => $base_coin_id, 'real_coin_id' => $cny_id])->pluck('rate')->first()
                * $num, 6);
        }
    }



    //获取CNY换成虚拟货币的数量
    public function changeTo_Virtual_currency($cny_num, $base_coin_id)
    {
        $usdt_coin_id = CoinType::where('coin_name', 'USDT')->pluck('coin_id')->first();

        $key = strtoupper('INSIDE_TEAM_' . $usdt_coin_id . '_' . $base_coin_id);

        $data = $this->redisHgetAll($key);

        $rate = $this->redisHget('USDT-CNY-RATE', 'rate');

        if(!empty($data) && $data['current_price'] != 0 && isset($rate) && $rate != 0) {
            return round($cny_num / $rate / $data['current_price'], 6);
        } else {
            return round($cny_num / \DB::table('coin_exchange_rate')->where('virtual_coin_id', $base_coin_id)->pluck('rate')->first(), 6);
        }
    }


    ///////////////set集合操作
    /*增加set集合元素， 返回true， 重复返回false*/
    public function sAdd($key,$value)
    {
        if (Redis::sadd($key,$value)) return 1;return 0;
    }

    /*移除指定元素*/
    public function sRem($key,$value)
    {
        if (Redis::exists($key) && Redis::srem($key,$value)) return 1;return 0;
    }

    /*返回set集合元素个数*/
    public function sCard($key)
    {
        return Redis::scard($key);
    }

    /*判断元素是否属于当前set集合*/
    public function sIsMember($key,$value)
    {
        if (Redis::exists($key) && Redis::sismember($key,$value)) return 1;return 0;
    }

    /*返回当前set集合的所有元素*/
    public function sMembers($key)
    {
        if (Redis::exists($key)) return Redis::smembers($key);return [];
    }

    /*队列*/

    /* 将值插入一个列表里
     * key:队列的键
     * value:队列的值
     * return 0|1
     * 0 失败；
     *  1 成功；
     */
    public function setList($key,$value){
        if(is_string($value)){
            Redis::lpush($key,$value);
            return 1;
        }
        return 0;
    }

     /* 给某个健值加锁
      *      1、 客户端A请求服务器设置key的值，如果设置成功就表示加锁成功
      *       2、 客户端B也去请求服务器设置key的值，如果返回失败，那么就代表加锁失败
      *       3、 客户端A执行代码完成，删除锁
      *       4、 客户端B在等待一段时间后在去请求设置key的值，设置成功
      *       5、 客户端B执行代码完成，删除锁
       */

     public function setKeyLock($keyName,$lockTime){

         if(!Redis::setNX($keyName, 1))
                return 0;
         Redis::expire($keyName, $lockTime);
         return 1;
     }



      /*  获取列表的全部值
       *  key:队列的键
       *  return 0|array
       *  0 失败；
       *  array 成功；
        */
    public function getList($key){
        if(is_string($key)){
            return   Redis::lrange($key,0,-1);
        }
        return 0;
                                  }


        /*有序集合*/

        /*   设置有序集合
         *   $zkey 有序集合的健值；
         *   $score 分数
         *   $value 值
         */
      public function setZadd($zkey,$score,$value){

                 return Redis ::zadd($zkey,(double)$score,$value);

      }

      /* 根据分数获取有序集合的值
       *
       *
       *
       */
      public function getZaddByScore($zkey,$mix,$max,$withScore='WITHSCORES'){

           return Redis::zrangebyscore($zkey,$mix,$max,$withScore);

      }

    /* 根据分数获取排行榜
   */
    public function getZrevRange($zkey,$start,$end,$options = array('WITHSCORES'=>true))
    {
        //return Redis::ZREVRANGE($zkey,$start,$end,'WITHSCORES');
        return Redis::ZREVRANGE($zkey,$start,$end,$options);
    }

      //按分数降序
    public function getZaddZrevrangebyscore($zkey,$min,$max)
    {
        return Redis::zrevrangebyscore($zkey,$min,$max);
    }


       /* 添加指定价格的统计数据
        *
        *
        */
      public function setZincrbyScore($zkey,$increment,$member){

       return   Redis::Zincrby($zkey,$increment,$member);

      }

      /* 删除指定有序集合中的健值；
       *
       *
       */
        public function delZsetMember($zkye,$memberName){

             return Redis::zrem($zkye,$memberName);

        }


        /* 删除特定区间的分数所有成员
         *
         *
         */
        public function delZremrangebyscore($zkye,$min,$max){

            return Redis::Zremrangebyscore($zkye,$min,$max);

        }


     /* 获取特定成员的成员
      *
      *
      */
    public function getZscore($zkye,$member){

        return Redis::Zscore ($zkye,$member);

    }


    /**
     * 获取键的过期时间
     */
    public function getTTL($key)
    {
        if (!$this->redisExists($key)) return 0;//不存在
        return Redis::ttl($key);
    }

    //设置过期时间
    public function setExpire($key,$time)
    {
        return Redis::expire($key,$time);//秒
    }

    public function getTrustList($userId)
    {
        return $this->sMembers('ouside:TRUST_LIST_' . $userId);
    }

}
