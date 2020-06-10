<?php
/**
 * Created by PhpStorm.
 * user: Administrator
 * Date: 2018/6/27
 * Time: 13:39
 */

namespace App\Http\Controllers\Web;
use Achse\GethJsonRpcPhpClient\JsonRpc\GuzzleClient;
use Achse\GethJsonRpcPhpClient\JsonRpc\GuzzleClientFactory;
use GuzzleHttp\Client;
use Web3\Eth;
use Web3\Personal;
use Web3\Web3;
use Web3\Contract;
use Web3p\EthereumTx\Transaction;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Support\Facades\DB;
use App\Model\WalletDetail;
class GethController extends BaseController
{
    public $web3;
    public $personal;
    public $eth;
    public $contract;
    public $client;
    public $walletDetail;

    public function __construct(WalletDetail $walletDetail)
    {
        $this->walletDetail = $walletDetail;
        $provider = env('GETH_HOST');
        $this->client = new Client();
        $this->web3 = new Web3($provider);
        $this->personal = new Personal($provider);
        $this->eth = new Eth($provider);
        $this->contract = new Contract($provider,'[{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"}],"name":"approve","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_value","type":"uint256"}],"name":"burn","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_value","type":"uint256"}],"name":"burnFrom","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transfer","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"},{"name":"_extraData","type":"bytes"}],"name":"approveAndCall","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"address"}],"name":"allowance","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"inputs":[{"name":"initialSupply","type":"uint256"},{"name":"tokenName","type":"string"},{"name":"tokenSymbol","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":true,"name":"to","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Burn","type":"event"}]');
//        $this->web3 = new Web3(new HttpProvider(new HttpRequestManager('http://123.207.2.221:8545')));
//        $this->contract = new Contract('http://192.168.1.116:8545','[
//    {
//      "constant": false,
//      "inputs": [],
//      "name": "sayHello",
//      "outputs": [
//        {
//          "name": "",
//          "type": "string"
//        }
//      ],
//      "payable": false,
//      "stateMutability": "nonpayable",
//      "type": "function"
//    }
//  ]');
    }

    public function contract()
    {
        $this->personal->listAccounts(function ($err, $account) use (&$records) {
            if ($err !== null) {
                // do something
                dd($err);
            }
            $records = $account;
        });

        foreach ($records as $record){

            $this->contract->at('0x59953E3699c22Ef9c4C4a97ceC0b7C7dCb8143e8')->call('balanceOf',$record,function ($err,$data){
//                $this->walletDetail
//                echo(bcdiv($data[0]->value,'1000000000000000000',8) . PHP_EOL);
            });

        }exit;
//        $opts = [
//            'json' => [
//                'jsonrpc' => '2.0',
//                'method' => 'personal_newAccount',
//                'params' => ["940419"],
//                'id' => time()
//            ]
//        ];
//        $rsp = $this->client->post('http://47.75.195.249:6585',$opts);
//        $res = $rsp->getBody();dd(\GuzzleHttp\json_decode($res));




        $this->contract->at('0x59953E3699c22Ef9c4C4a97ceC0b7C7dCb8143e8')->call('balanceOf','0xd565dac7184dee32b26113760946d459476f2bb7',function ($err,$data){
            dd($data[0]->value);
        });

//        $this->personal->unlockAccount('0x5d6f0e205131b0d051b0aa1bc289a380c37ba80e','123456',function ($err, $account) {
//            if ($err !== null) {
//                // do something
//                dd($err);
//                return;
//            }
//            dd($account);
//
//        });exit;


//        define('fromAddress','0x5d6f0e205131b0d051b0aa1bc289a380c37ba80e');
//        $this->contract->at('0x19ec4dccd928fc59b999902c5a9b8df01dde3311')->send('transfer','0x2b5496d5caf8c608dc4a0f0dff44c4651d5e1f42',"50000000000000000000",function ($err,$data){
//            dd($err);
//        });



        $this->eth->getBalance('0x69906e08e172d8560783df59f7a6a57704498b07',function ($err,$data){
            dd($data->value + 1522021550);
        });

        $opts = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'eth_getBalance',
                'params' => ['0xc83fa75f32a32457e6e03abda52a8a6bd7fa3a2a',"latest"],
                'id' => time()
            ]
        ];
        $rsp = $this->client->post('http://47.75.195.249:6585',$opts);
        $res = $rsp->getBody();
        dd(number_format(hexdec(\GuzzleHttp\json_decode($res)->result),0,'','') + 18888);


        $this->personal->newAccount('940419',function ($err,$data){
            dd($err);
        });

//      dd($this->contract->at('0xcbf360e478f005f2eb6376b1584df610da809ef1'));
//        $this->contract->at('0xcbf360e478f005f2eb6376b1584df610da809ef1')->call('approve','0x399d1Dfd605cDf94b040EBCA7864317774d2c143',10, function ($err,$data){
//            dd($data);
//        });
       $this->contract->at('0x59953E3699c22Ef9c4C4a97ceC0b7C7dCb8143e8')->call('symbol',function ($err,$data){
     dd($data);
       });
        $this->contract->at('0xcbf360e478f005f2eb6376b1584df610da809ef1')->send('transferFrom','0x399d1Dfd605cDf94b040EBCA7864317774d2c143','0x8Cb7C0735ED62D731eA537DE5a7c90A8438a5430',"10", function ($err,$data){
            dd($data);
        });

        $this->eth->getBalance('0x399d1Dfd605cDf94b040EBCA7864317774d2c143',function ($err,$data){
            dd($data->value);
        });


        $this->web3->eth->getBalance('0x7b8172e885fba4f0fd593ede603c067a7fb17971',function ($err,$result){
            if($err != null){
                dd($err);return;
            }
            dd($result);
        });

                $this->personal->newAccount('940419',function ($err, $account) {
            if ($err !== null) {
                // do something
                dd($err);
                return;
            }
            dd($account);

        });exit;

//        dd($this->eth->eth_compileSolidity());
        $this->contract->bytecode('0xB8c77482e45F1F44dE1745F52C74426C631bDD52')->new('', function ($err,$data){
            dd($data);
        });

        dd($this->eth->eth_compileSolidity('',function ($err,$data){
        dd($err);
        }));

        $this->contract->at('0x123438d379BAbD07134d1d4d7dFa0BCbd56ca3F3')->call('name',[],function ($err,$data){
            dd($err);
        });


    }

    public function personal()
    {
//        dd(dechex('30400'));
//          dd(hexdec('0x9184e72a000'));

//        $this->web3->clientVersion(function ($err, $version) {
//            if ($err !== null) {
//                // do something
//                dd($err);
//                return;
//            }
//            dd($version);
//            if (isset($client)) {
//                echo 'Client version: ' . $version;
//            }
//        });exit;

//        $this->personal->newAccount('940419',function ($err, $account) {
//            if ($err !== null) {
//                // do something
//                dd($err);
//                return;
//            }
//            dd($account);
//
//        });exit;

//        $this->personal->listAccounts(function ($err, $account) {
//            if ($err !== null) {
//                // do something
//                dd($err);
//                return;
//            }
//            dd($account);
//
//        });exit;

//        dd($this->personal);
        $transaction = [
            "from"=>"0x5d6f0e205131b0d051b0aa1bc289a380c37ba80e",
            "to"=>"0x2b5496d5caf8c608dc4a0f0dff44c4651d5e1f42",
//            "gas"=>"0x76c0",
//            "gasPrice"=>"0x9184e72a000",
            "value"=>"0xde0b6b3a7640000",
            "data"=>"0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675",

        ];
//        $this->personal->unlockAccount('0x7b8172e885fba4f0fd593ede603c067a7fb17971','123456',function ($err,$re){
//
//        });
        $this->personal->sendTransaction($transaction,'123456',function ($err, $account) {
            if ($err !== null) {
                // do something
                dd($err);
                return;
            }
            dd($account);

        });

    }

    public function balance()
    {
        $this->eth->getBalance('0x7b8172e885fba4f0fd593ede603c067a7fb17971',function ($err,$result){
           if($err != null){
               dd($err);return;
           }
           dd($result);
        });
    }


    public function transaction()
    {
        $transaction = new Transaction([
            'nonce' => '0x01',
            'from' => '0x7b8172e885fba4f0fd593ede603c067a7fb17971',
            'to' => '0x3c119f11ea139cc9432cc07d79d239c31acbb857',
            'gas' => '0x76c0',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x1',
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
        ],'http://123.207.2.221:8545');
//        dd($transaction);
        $signedTransaction = $transaction->sign('123456');
        $transaction = new Transaction($signedTransaction);
        dd($transaction->sign('123456'));
    }


    public function transaction1()
    {

        $httpClient = new GuzzleClient(new GuzzleClientFactory(), 'http://123.207.2.221', 8545);
        $client = new Client($httpClient);
        $result = $client->callMethod('eth_getBalance', ['0x7b8172e885fba4f0fd593ede603c067a7fb17971', '123456']);
        dd($result);
    }



}