<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/11
 * Time: 10:59
 */

namespace App\Handlers;
use App\Model\Admin\Invitation;
use App\Model\WalletDetail;
use App\Traits\RedisTool;
use App\Model\CoinType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Helpers
{
    use RedisTool;

    //创建表
    public function createTable($tables)
    {
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                return;
            } else {
                Schema::create($table, function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('vol')->default(0)->comment('成交量');
                    $table->string('current_price')->default(0)->comment('当前价格');
                    $table->string('float_type')->default('')->comment('价格浮动率');
                    $table->string('price_float')->default('')->comment('成交价格浮动的百分比,涨幅');
                    $table->string('begin')->default('')->comment('开始');
                    $table->string('end')->default('')->comment('结束');
                    $table->decimal('min_price', 20, 6)->default(0)->comment('最低价');
                    $table->decimal('max_price', 20, 6)->default(0)->comment('最高价');
                    $table->string('deal_time')->index()->comment('时间点记录');
                });
            }
        }
    }

    public function getAll($prefix)
    {
        $coinType = new CoinType();
        //获取所有货币的键值对
        $data = $coinType->getChanges();
//        dd($data);
        foreach ($data as $v) {
            $res = $this->redisHgetAll($v);

            if(!empty($res) && $res['switch'] == 1){
                $base_coin = $coinType->getCoinName(['coin_id' => $res['base_coin_id']]);
                $exchange_coin = $coinType->getCoinName(['coin_id' => $res['exchange_coin_id']]);
                if ($this->redisExists($base_coin . '_' . $exchange_coin . '_info')) {
                    if ($this->redisHget($base_coin . '_' . $exchange_coin . '_info', 'is_trade_' . $prefix) == 0) {
                        $res['begin'] =$res['current_price'];
                        $res['end'] = $res['current_price'];
                        $max = $res['current_price'];
                        $min = $res['current_price'];
                        $res['vol'] = 0;
                    } else {
                        $res['begin'] = $this->redisHget($base_coin . '_' . $exchange_coin . '_info', 'begin_' . $prefix);
                        $res['end'] = $this->redisHget($base_coin . '_' . $exchange_coin . '_info', 'end_' . $prefix);
                        $res['vol'] = $this->redisHget($base_coin . '_' . $exchange_coin . '_info', 'vol_' . $prefix);
                        $max = $this->redisHget($base_coin . '_' . $exchange_coin . '_info', 'max_' . $prefix);
                        $min = $this->redisHget($base_coin . '_' . $exchange_coin . '_info', 'min_' . $prefix);
                    }

                    //取出需要的字段
                    $info = [
                        'vol' => $res['vol'],
                        'current_price' => $res['current_price'],
                        'float_type' => $res['float_type'],
                        'price_float' => $res['price_float'],
                        'min_price' =>$min,
                        'max_price' => $max,
                        'begin' => $res['begin'],
                        'end' => $res['end'],
                        'deal_time' => time(),
                    ];

                    $this->redisHget($base_coin . '_' . $exchange_coin . '_info', 'max_' . $prefix, $info['end']);
                    $data = $info;

    //                $data['end'] = $info['current_price'];
                    //入库
                    $this->saveTable($data, $base_coin, $exchange_coin, $prefix, $res['end']);
                }
            }
        }
    }


    public function saveTable($data, $base_coin, $exchange_coin, $prefix, $end)
    {
        $table = strtolower("time_sharing_" . $prefix . "_" . $base_coin . "_" . $exchange_coin);
        if (Schema::hasTable($table)) {
            $result = \DB::table($table)->insert($data);
            if ($result) {
                $this->redisHmset($base_coin . '_' . $exchange_coin . '_info',
                    ['is_trade_' . $prefix => 0,
                     'vol_' . $prefix => 0,
                     'end_' . $prefix => $end,
                     'begin_' . $prefix => $end,
                     'min_' . $prefix => $end,
                     'max_' . $prefix => $end]);
            }
        }

    }


    //计算每天交易货币的汇总
    public function summary()
    {
        $coinType = (new CoinType())->all();

        foreach ($coinType as $coin) {
            $wallet_usable_balance = \DB::table('wallet_detail')->where('coin_id', $coin->coin_id)->select('wallet_usable_balance')->get()->sum('wallet_usable_balance');
            $wallet_freeze_balance = \DB::table('wallet_detail')->where('coin_id', $coin->coin_id)->select('wallet_freeze_balance')->get()->sum('wallet_freeze_balance');
            $data['coin_sum_money'] = $wallet_usable_balance + $wallet_freeze_balance;
            if (!empty(\DB::table('real_coin_center_wallet')->where('coin_id', $coin->coin_id)->get()->toArray())) {
                \DB::table('real_coin_center_wallet')->where('coin_id', $coin->coin_id)->update($data);
            } else {
                $data['coin_id'] = $coin->coin_id;
                \DB::table('real_coin_center_wallet')->insert($data);
            }
        }
    }


    //保存交易对的现价
    public function getCurrentPrice()
    {
        $coinType = new CoinType();
        //获取所有货币的键值对
        $data = $coinType->getChanges();

        foreach ($data as $v) {
            $res = $this->redisHgetAll($v);
            if (!empty($res)) {
                if ($res['current_price'] != 0) {
                    $this->redisHmset($v, ['begin_price' => $res['current_price'], 'day_vol' => 0, 'max_price' => $res['current_price'], 'min_price' => $res['current_price']]);
                } else {
                    $this->redisHset($v, 'begin_price', 1);
                }
            }
        }
    }


    //邀请人奖励
    public function increaseCoinNum($pid)
    {
        $data = array_merge(unserialize($this->stringGet('Invitation_set')), ['user_id' => $pid]);
        //增加用户钱包可交易余额
        WalletDetail::where(['coin_id' => $data['coin_id'], 'user_id' => $data['user_id']])->increment('wallet_usable_balance', $data['coin_num']);
        $res = Invitation::where(['user_id' => $data['user_id'], 'coin_id' => $data['coin_id']])->first();


        if (!empty($res)) {
            $rs = Invitation::where(['user_id' => $data['user_id'], 'coin_id' => $data['coin_id']])->increment('coin_num', $data['coin_num']);
            if ($rs) return 1;
        } else {
            $r = Invitation::create($data);
            if ($r) return 2;
        }
    }





}
