<?php
/**
 * Created by PhpStorm.
 * User: 77507
 * Date: 2019/8/9
 * Time: 15:15
 */


function ssss()
{
    return 'test';
}

//获取文章分类无限子分类
function get_tree_child($data, $fid) {
    $result = array();
    $fids = array($fid);
    do {
        $cids = array();
        $flag = false;
        foreach($fids as $fid) {
            for($i = count($data) - 1; $i >=0 ; $i--) {
                $node = $data[$i];
                if($node['parents_id'] == $fid) {
                    array_splice($data, $i , 1);
                    $result[] = $node['id'];
                    $cids[] = $node['id'];
                    $flag = true;
                }
            }
        }
        $fids = $cids;
    } while($flag === true);
    return $result;
}

/**
 * 只保留字符串首尾字符，隐藏中间用*代替
 * @param string $user_name 姓名
 * @return string 格式化后的姓名
 */
function substr_cut($user_name){
    $strlen    = mb_strlen($user_name);
    if($strlen>7){
        $firstStr  = mb_substr($user_name, 0, 3,'utf-8');
        $lastStr   = mb_substr($user_name, -4, 4,'utf-8');
        return $firstStr . str_repeat("*", 4) . $lastStr;
    }else{
        $firstStr  = mb_substr($user_name, 0, 2,'utf-8');
        $lastStr   = mb_substr($user_name, -2, 2,'utf-8');
        return $firstStr . str_repeat("*", 2) . $lastStr;
    }
}

//当前用户
function current_user()
{
    return \App\Model\User::find(request('user_id'));
}


//api返回
function api_response()
{
    return new \App\Http\Response\ResponseHelper();
}


//根据给定的时间戳获取当前距离改时间相差多少天;如给定今天的时间戳则返回1
function get_left_days($timestamps)
{
   $d1 = time();
    $d2=get_today_zero_timestamps($timestamps);
    $Days=ceil(($d1-$d2)/3600/24);//dd($Days);

    return (int)$Days;
}


//获取当日0点的时间戳
function get_today_zero_timestamps($timestamps)
{
    $date = date('Y-m-d',$timestamps);
    $timestamp = strtotime($date);

    return $timestamp;
}




//计算百分比
function get_percent($num,$base_num,$float = 0)
{
    $re = ($num/$base_num)*100;
    if ($float == 0){

        if ($re > 0 && $re < 1){
            $re = ceil($re);
        }else{
            $re = floor($re);
        }
        return (int)$re;
    }else{
//        $re = round($re,2);
        $re = floor($re*100)/100;
//        dd($re);

        return $re;
    }






}



//获取倒计时,输入过去某点的时间戳和持续的天数
function get_daojishi($start_timestamps,$days)
{
    $end_time = get_today_zero_timestamps($start_timestamps) + $days*24*60*60;
//dd($end_time);
    $now = time();
    $d = '0';$h = '0';$m = '0';$s = '0';

    $left = $end_time - $now;
    if ($now < $end_time){
        $ds = ($left)/3600/24;
        if ($ds>0){
            $d = (string)floor($ds);
            $left -= $d * 3600*24;
        }
        $hs = $left/3600;
        if ($hs > 0){
            $h = (string)floor($hs);
            $left -= $h*3600;
        }
        $ms = $left/60;
        if ($ms > 0){
            $m = (string)floor($ms);
            $left -= $m * 60;
        }
        $s = (string)$left;
    }

    $res = $d . ':' . $h . ':' . $m . ':' . $s;
    return $res;
}

//获取倒计时,输入过去某点的时间戳和持续的天数
function get_daojishi1($timestamps)
{
    $d = '0';$h = '0';$m = '0';$s = '0';

    $left = $timestamps;
    if ($left > 0){
        $ds = ($left)/3600/24;
        if ($ds>0){
            $d = (string)floor($ds);
            $left -= $d * 3600*24;
        }
        $hs = $left/3600;
        if ($hs > 0){
            $h = (string)floor($hs);
            $left -= $h*3600;
        }
        $ms = $left/60;
        if ($ms > 0){
            $m = (string)floor($ms);
            $left -= $m * 60;
        }
        $s = (string)$left;
    }

    $res = $d . ':' . $h . ':' . $m . ':' . $s;
    return $res;
}

//求随机涨跌数
function getRandFloatNumber($number,$minPercent,$maxPercent)
{
    $num = rand($minPercent,$maxPercent);

    $float = $number * ($num/100);

    return $number + $float;
}


//当前时间
function datetime()
{
    return date('Y-m-d H:i:s');
}

//比较当日的时分时间大小09:00< 10:00返回:0相等-1com时间小于base时间1com时间大于base时间
function compare_today_time($compare_time,$base_time)
{

    $base_timestamp = strtotime($base_time);
    $compare_timestamp = strtotime($compare_time);

    if ($compare_timestamp > $base_timestamp) return 1;
    if ($compare_timestamp < $base_timestamp) return -1;
    return 0;

}
//比较当日此时的时分时间大小09:00< 10:00返回:0相等-1com时间小于base时间1com时间大于base时间
function compare_time_with_now($base_time)
{

    $base_timestamp = strtotime($base_time);
    $compare_timestamp = time();

    if ($compare_timestamp > $base_timestamp) return 1;
    if ($compare_timestamp < $base_timestamp) return -1;
    return 0;

}


/**
 * @desc 生成n个随机手机号
 * @param int $num 生成的手机号数
 * symbol 手机号中间四位是否替换成制定字符,空则不替换
 * @author niujiazhu
 * @return array
 */
function randMobile($num = 1,$symbol = ''){
    //手机号2-3为数组
    $numberPlace = array(30,31,32,33,34,35,36,37,38,39,50,51,58,59,89);
    for ($i = 0; $i < $num; $i++){
        $mobile = 1;
        $mobile .= $numberPlace[rand(0,count($numberPlace)-1)];
        $mobile .= str_pad(rand(0,99999999),8,0,STR_PAD_LEFT);
        $result[] = $mobile;
    }
    if ($symbol != ''){
        foreach ($result as $k=>$value){
            $result[$k] = substr_replace($value,$symbol.$symbol.$symbol.$symbol,3,4);
        }
    }
    return $result;
}
