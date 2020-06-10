<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/31
 * Time: 19:03
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use App\Server\CoinServers\BitCoinServer;
use App\Server\CoinServers\GethServer;
use App\Server\CoinServers\GethTokenServer;
use App\Server\CoinServers\OmnicoreServer;

class CenterWalletDetail extends Model
{

    /*表名称*/
    protected $table = 'center_wallet_detail';

    protected $primaryKey = 'center_wallet_id';

    //关联货币表
    public function coin()
    {
        return $this->hasOne('App\Model\CoinType', 'coin_id', 'coin_id');
    }


}

