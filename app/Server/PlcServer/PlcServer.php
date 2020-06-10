<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/26
 * Time: 17:20
 */

namespace App\Server\PlcServer;


class PlcServer
{


    protected $checkIsAddressApi = 'checkIsAddress';
    protected $transferToChatApi = 'ttsRecharge';

    //聊天app服务

    //查询地址是否是plc地址
    public function checkIsAddress($address)
    {
        $url = env('plc_api_ip') . $this->checkIsAddressApi . '/'.$address;

        try{
            $re = $this->sendUrlGetRequest($url);

            if ($re['status_code'] == 200){
                return true;
            }
            return false;
        }catch (\Exception $exception){
            return false;
        }


    }


    //转账到聊天
    public function transferToChat($fromAddress,$toAddress,$amount,$coinName)
    {

        $url = env('plc_api_ip') . $this->transferToChatApi;

        try{
            $params =[
                    'from_address' => $fromAddress,
                    'to_address' => $toAddress,
                    'amount' => $amount,
                    'coin_name' => $coinName
                ];
            $re = $this->sendUrlGetRequest($url,$params);

            if ($re['status_code'] == 200){
                return true;
            }
            return false;
        }catch (\Exception $exception){
            return false;
        }

    }



    //
    protected function sendUrlGetRequest($url,$params = [],$auth = ['u'=>'tts','p'=>'tts_plc_api_147258369'])
    {
        try{
            foreach ($auth as $key => $value){
                if (strpos($url,'?') === false){
                    $url .= '?' . $key . '=' . $value;
                }else{
                    $url .= '&' . $key . '=' . $value;
                }

            }
            foreach ($params as $k => $param){
//                if (strpos($url,'?') === false){
//                    $url .= '?' . $k . '=' . $param;
//                }
                $url .= '&' . $k . '=' . $param;
            }
//dd($url);
            $re = json_decode(file_get_contents($url),true);

            return $re;
        }catch (\Exception $exception){
            return false;
        }
    }





}