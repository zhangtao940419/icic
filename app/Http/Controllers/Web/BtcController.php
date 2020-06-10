<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/7/2
 * Time: 16:32
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use Denpa\Bitcoin\Client as BitcoinClient;
use  App\Http\Controllers\Web\Bitcoin\BitcoinController;

class BtcController extends BaseController
{
    public $bitcoind;
    public function __construct()
    {
//        $this->bitcoind = new BitcoinController(
//            'btp', 'btp','123.207.2.221','18332'
//        );
          $this->bitcoind = new BitcoinController(
        'btcuserbab', 'vsdfgb4gh42h','172.31.201.185','18333'
    );
    }


    public function bitcoin()
    {
        $bitcoind1 = new BitcoinClient([
            'scheme' => 'http',                 // optional, default http
            'host'   => '172.31.201.185',            // optional, default localhost
            'port'   => 18333,                   // optional, default 8332
            'user'   => 'btcuserbab',              // required
            'pass'   => 'vsdfgb4gh42h',          // required
         //   'ca'     => '/etc/ssl/ca-cert.pem'  // optional, for use with https scheme
        ]);
//        dd($this->bitcoind->getaccountaddress('btp_center'));
//                dd($this->bitcoind->getaccountaddress('btp_center'));
//        dd($this->bitcoind->encryptwallet('JbH0EExHTv'));
//        dd($this->bitcoind->listaccounts());//列出所有的子账户
//        dd($this->bitcoind->getbalance());
//        dd($bitcoind1->walletpassphrase('btp888',10));
//        dd($bitcoind1->getwalletinfo());//列出所有的子账户

//        dd($bitcoind1->getaccount('2N8k8qDG3oryGDy5EPUTu1DPJh2xvWMtVmm'));
//        dd($bitcoind1->createrawtransaction([['txid'=>'dfd9bfa110a508d9f21c2f98cbb01d7c6d248945e85a8fce97ca8d7ca06de745','vout'=>1]],['2N3MczkJ7kREk8UokXYuFR41Bn3gTGuiUd3'=>0.001]));
//        dd($bitcoind1->listunspent());
        dd($this->bitcoind->fundrawtransaction('020000000145e76da07c8dca97ce8f5ae84589246d7c1db0cb982f1cf2d908a510a1bfd9df0100000000ffffffff01a08601000000000017a9146ee6f5af8f54f62a98dae741b5113d203d0c64898700000000'));


        dd($bitcoind1->gettransaction('76dc21492457dd5ccabe2df02b8247f3b352303cc7a5b78f9c448f018f1b93df'));
        dd($bitcoind1->sendrawtransaction('0200000000010120bf3310c4898ebeadb8e479d5bedb227c0076aea5b8a7d4788eb6d6e4f32d070000000017160014a3a41dbf8819734bf9a9df9d89ffbe42d06243bfffffffff01400d03000000000017a9146ee6f5af8f54f62a98dae741b5113d203d0c6489870247304402205bbf4eb7e4e386c32d2695614785723d2249673083a1eba083ee498c39c9c5f802201591efa1e2968217a737fb974f8bc8a64c0443fd72222e92d904f1effb6befff012103085cbc9b67deb6d91eadec6a535c5c16f03e73c5a7d689c0b5d5f0f4106a0ad100000000'));
        dd($bitcoind1->signrawtransactionwithwallet('020000000120bf3310c4898ebeadb8e479d5bedb227c0076aea5b8a7d4788eb6d6e4f32d070000000000ffffffff01400d03000000000017a9146ee6f5af8f54f62a98dae741b5113d203d0c64898700000000',[['txid'=>'69bfda505b162a6673fd24fb7ebad9737ab5c77cd497c069689412ffc412df02','vout'=>0,'scriptPubKey'=>'a9146ee6f5af8f54f62a98dae741b5113d203d0c648987']]));

        dd($bitcoind1->decoderawtransaction('020000000120bf3310c4898ebeadb8e479d5bedb227c0076aea5b8a7d4788eb6d6e4f32d070000000000ffffffff01400d03000000000017a9146ee6f5af8f54f62a98dae741b5113d203d0c64898700000000'));




        dd($bitcoind1->getwalletinfo());
//        dd($this->bitcoind->getaccountaddress('st'));
//        dd($bitcoind1->getbalance('st'));
        //dd($this->bitcoind->settxfee(0.00001));
       // dd($bitcoind1->estimatesmartfee(2));
//dd($this->bitcoind->getaddressesbyaccount('btp'));
        //dd($bitcoind1->gettransaction('2216e163df8f9248c3c770e1976b30350fd15484b56be0d8c18ffe0010e8a787'));
        $this->bitcoind->walletpassphrase('123456',10);
        dd($bitcoind1->sendfrom('st','2N3MczkJ7kREk8UokXYuFR41Bn3gTGuiUd3','0.01'));

        dd($bitcoind1->getbalance('ztc'));





        $bitcoind1->walletpassphrase('123456',10);//解锁账户
        dd($bitcoind1->sendfrom('btp','2MwazGJ5U41oMcgA12QDLPdcGJcQDSui4Tr','0.1'));
        dd($bitcoind1->sendtoaddress('2MwazGJ5U41oMcgA12QDLPdcGJcQDSui4Tr','0.1'));//发起交易
        dd($this->bitcoind->listaccounts());//列出所有的子账户
        dd($this->bitcoind->getaccountaddress('ztc'));//返回一个子账户中的地址，不存在则创建新的地址
        dd($this->bitcoind->getaddressesbyaccount('btp'));//返回子账户中的所有地址
        dd($bitcoind1->getbalance('ztc'));
        //dd($this->bitcoind->getwalletinfo());


    }


}