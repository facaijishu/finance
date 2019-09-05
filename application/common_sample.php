<?php
use sys\Wechat;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @param  array  $key  签名密钥
 * @return string       签名
 */
function data_auth_sign($data, $key) {
    //数据类型检测
    if (!is_array($data)) {
        $data = (array) $data;
    }
    ksort($data); //排序
    $query = http_build_query($data); //url编码并生成query字符串
    $code = $query . '&key=' . $key;
    $sign = strtoupper(md5($code)); //生成签名
    return $sign;
}

/**
 * 生成用户唯一ID
 * @return string
 */
function guid() {
    if (function_exists('com_create_guid')) {
        return com_create_guid();
    } else {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
        return $uuid;
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 */
function get_client_ip($type = 0, $adv = false) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) {
        return $ip[$type];
    }

    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }

            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 产生一个指定长度的随机字符串,并返回给用户
 * @param type $len 产生字符串的长度
 * @return string 随机字符串
 */
function genRandomString($len = 6) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    // 将数组打乱
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * 生成编号
 * @param $table_name
 * @param string $type
 * @return bool|string
 */
function getNumberByTable($table_name, $type = '') {
    if (empty($table_name)) {
        $this->error = '该表不存在';
        return false;
    }
    if (!empty($type)) {
        $prefix = config('biz.contract_sn')[$type];
        $first_no = $prefix . '-' . date('Ym', time());
    } else {
        $first_no = date('Ym', time());
    }
    $start_date = date('Y', time()) . '-01-01 00:00:00';
    $end_date = date('Y', time()) . '-12-31 23:59:59';
    $second_no = count(model($table_name)->where('create_date', 'between', [strtotime($start_date), strtotime($end_date)])->select());
    $third_no = count(model($table_name)->select());
    $second_no++;
    $third_no++;
    return $first_no . '-' . $second_no . '-' . $third_no;
}

/*
 * 金额大写
 */

function num_to_rmb($num) {
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    //精确到分后面就不要了，所以只留两个小数位
    $num = round($num, 2);
    //将数字转化为整数
    $num = $num * 100;
    if (strlen($num) > 10) {
        return "金额太大，请检查";
    }
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
            //获取最后一位数字
            $n = substr($num, strlen($num) - 1, 1);
        } else {
            $n = $num % 10;
        }
        //每次将最后一位数字转化为中文
        $p1 = substr($c1, 3 * $n, 3);
        $p2 = substr($c2, 3 * $i, 3);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
            $c = $p1 . $p2 . $c;
        } else {
            $c = $p1 . $c;
        }
        $i = $i + 1;
        //去掉数字最后一位了
        $num = $num / 10;
        $num = (int) $num;
        //结束循环
        if ($num == 0) {
            break;
        }
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        //utf8一个汉字相当3个字符
        $m = substr($c, $j, 6);
        //处理数字中很多0的情况,每次循环去掉一个汉字“零”
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $left = substr($c, 0, $j);
            $right = substr($c, $j + 3);
            $c = $left . $right;
            $j = $j - 3;
            $slen = $slen - 3;
        }
        $j = $j + 3;
    }
    //这个是为了去掉类似23.0中最后一个“零”字
    if (substr($c, strlen($c) - 3, 3) == '零') {
        $c = substr($c, 0, strlen($c) - 3);
    }
    //将处理的汉字加上“整”
    if (empty($c)) {
        return "零元整";
    } else {
        return $c . "整";
    }
}

/*
 * 获取数据表信息查询条件
 */

function getDataTableIdea($data) {
    $result = [];
    $result['draw'] = $data['draw']; //这个值作者会直接返回给前台
    $order_column = $data['order']['0']['column']; //那一列排序，从0开始
    $order_dir = $data['order']['0']['dir']; //ase desc 升序或者降序
    if (isset($order_column)) {
        $orderSql = $data['menu'][$order_column] . ' ' . $order_dir;
    } else {
        $orderSql = '';
    }
    $result['order'] = $orderSql;
    $search = $data['search']['value']; //获取前台传过来的过滤条件
    $start = $data['start']; //从多少开始
    $length = $data['length']; //数据长度
    $limitFlag = isset($_GET['start']) && $length != -1;
    if ($limitFlag) {
        $limitSql = intval($start) . ", " . intval($length);
    } else {
        $limitSql = '';
    }
    $result['limit'] = $limitSql;
    foreach ($data['menu'] as $key => $value) {
        $data['menu'][$key] = $value . " LIKE '%" . $search . "%'";
    }
    $result['menu'] = $data['menu'];
    foreach ($data['columns'] as $key => $value) {
        if (empty($value['search']['value']) && $value['search']['value'] != 0) {
            unset($data['columns'][$key]);
        }
    }
    $result['columns'] = $data['columns'];
    $str = '';
    if (isset($_GET['condition'])) {
        $str .= $_GET['condition'][0] . ' = ' . $_GET['condition'][1];
    }
    if (!empty($result['columns'])) {
        foreach ($result['columns'] as $key => $value) {
            $str .= ' AND ' . $value['data'] . ' LIKE "%' . $value['search']['value'] . '%"';
        }
        $str = trim($str, ' AND ');
    }
    if (isset($_GET['status'])) {
        $str .= ' AND status = ' . $_GET['status'];
    }
    $result['where'] = $str;
    $search = $_GET['search']['value'];
    $result['search'] = $search;
    return $result;
}

/*
 * 商品数组整合函数
 * @Param $data 标准二位数组
 * @Param $array 不需要判断的参数
 */

function getProductDataArray($data, $array = ['prod_num']) {
    $arr = [];
    $result = [];
    foreach ($data as $key => $value) {
        foreach ($array as $keyr => $valuer) {
            unset($value[$valuer]);
        }
//        unset($value['prod_num']);
        if (in_array($value, $arr)) {
            foreach ($arr as $key1 => $value1) {
                if ($value1 == $value) {
                    $result[$key1]['prod_num'] = intval($result[$key1]['prod_num']) + intval($data[$key]['prod_num']);
                    if (isset($data[$key]['prod_money'])) {
                        $result[$key1]['prod_money'] = intval($result[$key1]['prod_money']) + intval($data[$key]['prod_money']);
                    }
                }
            }
        } else {
            $arr[$key] = $value;
            $result[$key] = $data[$key];
        }
    }
    return $result;
}

function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

function httpPost($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 500);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

//与微信交互获取access_token
function getAccessToken() {
    $WXConfig   = config('WeiXinCg');
    $appid      = $WXConfig['appid'];
    $secret     = $WXConfig['appsecret'];
    $url        = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
    $result     = httpGet($url);
    $result     = json_decode($result, true);
    return $result == isset($result['errcode']) ? array() : $result;
}

//从文件中获取access_token到文件
function getAccessTokenFromFile() {
    $tokenFile = config("tokenFile"); // 缓存文件名
    if (!file_exists($tokenFile)) {
        $file = fopen($tokenFile, 'a');
        chmod($tokenFile, 0777);
        fclose($file);
    }
    $data = file_get_contents($tokenFile);
    if (empty($data)) {
        $data = null;
    } else {
        $data = json_decode($data, true);
    }
    if ($data == null || !isset($data['expires_in']) || $data['expires_in'] < time()) {
        $AccessToken  = getAccessToken();
        $access_token = $AccessToken['access_token'];
        if (isset($AccessToken['access_token'])) {
            $arr = [];
            $arr['expires_in']   = intval(time()) + intval($AccessToken['expires_in']); //保存时间
            $arr['access_token'] = $AccessToken['access_token'];
            file_put_contents($tokenFile, json_encode($arr));
        }
    } else {
        $access_token = $data['access_token'];
    }
    return $access_token;
}

//转换时长
function changeLongTime($time){
    $hours = '';$seconds = '';$minutes = '';
    $hour = intval($time/3600);
    $second = intval(($time-$hour*3600)/60);
    $minute = intval($time-$hour*3600-$second*60);
    if($hour == 0){
        $hours = '00:';
    } else {
        if($hour > 9){
            $hours = (string)$hour.':';
        } else {
            $hours = '0'.(string)$hour.':';
        }
    }
    if($second == 0){
        $second = '00:';
    } else {
        if($second > 9){
            $seconds = (string)$second.':';
        } else {
            $seconds = '0'.(string)$second.':';
        }
    }
    if($minute == 0){
        $minutes = '00';
    } else {
        if($minute > 9){
            $minutes = (string)$minute;
        } else {
            $minutes = '0'.(string)$minute;
        }
    }
    return $hours.$seconds.$minutes;
}
//判断是否关注公众号
   function isFollow($openId , $access_token) 
   {
       $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $openId . '&lang=zh_CN';
       $result = httpGet($url);
       $result = json_decode($result, true);
       if ($result['subscribe'] == 0) {
           return false;
       } elseif ($result['subscribe'] == 1) {
           return true;
       }
   }
//发送模板消息
function sendTemplateMessage($template_id , $openId , $url , $array){
    $config = config("WeiXinCg");
    $options = array(
        'token' => config("token"), //填写你设定的key
        'encodingaeskey' => config("encodingaeskey"), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
        'appid' => $config['appid'],
        'appsecret' => $config['appsecret']
    );
    $weObj = new Wechat($options);
    $weObj->checkAuth('', '', getAccessTokenFromFile());
    $data = [
        'touser' => $openId,
        'template_id' => $template_id,
        'url' => $url,
        'topcolor' => '#FF0000',
        'data' => $array
    ];
    $result = $weObj->sendTemplateMessage($data);
    if(!$result){
        $weObj->text('错误原因1：'.$weObj->errCode."-".$weObj->errMsg)->reply();
        return false;
    }
    return $result;
}
//获取模板消息发送结果
function getSendCodeMsg($end) {
    if ($end['errcode'] == 0) {
        return $end['errmsg'].'-'.$end['msgid'];
    } else {
        switch ($end['errcode']) {
            case -1: return $end['errmsg'].'-'.'系统繁忙';break;
            case 40001: return $end['errmsg'].'-'.'验证失败';break;
            case 40002: return $end['errmsg'].'-'.'不合法的凭证类型';break;
            case 40003: return $end['errmsg'].'-'.'不合法的OpenID';break;
            case 40004: return $end['errmsg'].'-'.'不合法的媒体文件类型';break;
            case 40005: return $end['errmsg'].'-'.'不合法的文件类型';break;
            case 40006: return $end['errmsg'].'-'.'不合法的文件大小';break;
            case 40007: return $end['errmsg'].'-'.'不合法的媒体文件id';break;
            case 40008: return $end['errmsg'].'-'.'不合法的消息类型';break;
            case 40009: return $end['errmsg'].'-'.'不合法的图片文件大小';break;
            case 40010: return $end['errmsg'].'-'.'不合法的语音文件大小';break;
            case 40011: return $end['errmsg'].'-'.'不合法的视频文件大小';break;
            case 40012: return $end['errmsg'].'-'.'不合法的缩略图文件大小';break;
            case 40013: return $end['errmsg'].'-'.'不合法的APPID';break;
            case 41001: return $end['errmsg'].'-'.'缺少access_token参数';break;
            case 41002: return $end['errmsg'].'-'.'缺少appid参数';break;
            case 41003: return $end['errmsg'].'-'.'缺少refresh_token参数';break;
            case 41004: return $end['errmsg'].'-'.'缺少secret参数';break;
            case 41005: return $end['errmsg'].'-'.'缺少多媒体文件数据';break;
            case 41006: return $end['errmsg'].'-'.'access_token超时';break;
            case 42001: return $end['errmsg'].'-'.'需要GET请求';break;
            case 43002: return $end['errmsg'].'-'.'需要POST请求';break;
            case 43003: return $end['errmsg'].'-'.'需要HTTPS请求';break;
            case 44001: return $end['errmsg'].'-'.'多媒体文件为空';break;
            case 44002: return $end['errmsg'].'-'.'POST的数据包为空';break;
            case 44003: return $end['errmsg'].'-'.'图文消息内容为空';break;
            case 45001: return $end['errmsg'].'-'.'多媒体文件大小超过限制';break;
            case 45002: return $end['errmsg'].'-'.'消息内容超过限制';break;
            case 45003: return $end['errmsg'].'-'.'标题字段超过限制';break;
            case 45004: return $end['errmsg'].'-'.'描述字段超过限制';break;
            case 45005: return $end['errmsg'].'-'.'链接字段超过限制';break;
            case 45006: return $end['errmsg'].'-'.'图片链接字段超过限制';break;
            case 45007: return $end['errmsg'].'-'.'语音播放时间超过限制';break;
            case 45008: return $end['errmsg'].'-'.'图文消息超过限制';break;
            case 45009: return $end['errmsg'].'-'.'接口调用超过限制';break;
            case 46001: return $end['errmsg'].'-'.'不存在媒体数据';break;
            case 47001: return $end['errmsg'].'-'.'解析JSON/XML内容错误';break;
            default: return $end['errcode'].$end['errmsg'];break;
        }
    }
}
function getImage($url, $save_dir = '', $filename = '', $type = 0) {
    if (trim($url) == '') {
        return array('msg' => 'url地址为空', 'error' => 1);
    }
    if (trim($save_dir) == '') {
        $save_dir = './';
    }
    if (trim($filename) == '') {//保存文件名
        $ext = strrchr($url, '.');
        if ($ext != '.gif' && $ext != '.jpg') {
            return array('msg' => '图片格式不正确', 'error' => 3);
        }
        $filename = time() . $ext;
    }
    if (0 !== strrpos($save_dir, '/')) {
        $save_dir .= '/';
    }
    //创建保存目录
    if (!is_dir($save_dir)) {
        mkdir($save_dir, 0777, true);
//            return array('file_name' => '', 'save_path' => '', 'error' => 5);
    }
    //获取远程文件所采用的方法
    if ($type) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $img = curl_exec($ch);
        curl_close($ch);
    } else {
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
    }
    //$size=strlen($img);
    //文件大小
    if(!$img){
        return array('msg' => '图片格式不正确', 'error' => 3);
    }
    $fp2 = @fopen($save_dir . $filename, 'a');
    fwrite($fp2, $img);
    fclose($fp2);
    unset($img, $url);
    return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
}
function createProEditQrcodeDirection($current = 0 , $type , $data_id , $neeq , $plannoticeddate){
    $model = model("QrcodeDirection");
    $length = $model->count();
    if($current < 95000){
        $id = intval($current)+1;
    } else {
        $id = 1;
    }
    if($length < 95000){
        $info = $model->allowField(true)->save(['type' => $type , 'data_id' => $data_id , 'data_neeq' => $neeq , 'plannoticeddate' => $plannoticeddate]);
        $pro_id = $model->getLastInsID();
        if($info){
            $weObj = new Wechat(config("WeiXinCg"));
            $access_token = getAccessTokenFromFile();
            $result = $weObj->checkAuth('', '', $access_token);
            $result = $weObj->getQRCode($pro_id, 1);
            $result = $weObj->getQRUrl($result['ticket']);
            $filename = $pro_id.genRandomString(10).'.jpg';
            $result = getImage($result, config("QRCodePath"), $filename, 1);
            if($result['error']){
                return false;
            } else {
                $result = $model->where(['id' => $pro_id])->update(['qrcode_path' => $filename]);
            }
        } else {
            return false;
        }
        $id = $pro_id;
    } else {
        $result = $model->where(['id' => $id])->update(['type' => $type , 'data_id' => $data_id , 'data_neeq' => $neeq , 'plannoticeddate' => $plannoticeddate]);
        $id = $id;
    }
    $result1 = model("SystemConfig")->where(['sc_id' => 1])->update(['current' => $id]);
    if($result && $result1){
        return $id;
    } else {
        return false;
    }
}

function createOrgEditQrcodeDirection($current = 0 , $type , $data_id , $neeq , $plannoticeddate){
    $model = model("QrcodeDirection");
    $length = $model->count();
    if($current < 95000){
        $id = intval($current)+1;
    } else {
        $id = 1;
    }
    if($length < 95000){
        $info = $model->allowField(true)->save(['type' => $type , 'data_id' => $data_id , 'data_neeq' => $neeq , 'plannoticeddate' => $plannoticeddate]);
        $org_id = $model->getLastInsID();
        if($info){
            $weObj = new Wechat(config("WeiXinCg"));
            $access_token = getAccessTokenFromFile();
            $result = $weObj->checkAuth('', '', $access_token);

            $result = $weObj->getQRCode($org_id, 1);
           
            $result = $weObj->getQRUrl($result['ticket']);
            $filename = $org_id.genRandomString(10).'.jpg';
            $result = getImage($result, config("QRCodePath2"), $filename, 1);
            
            if($result['error']){
                return false;
            } else {
                $result = $model->where(['id' => $org_id])->update(['qrcode_path' => $filename]);
                
            }

        } else {
            return false;
        }
        $id = $org_id;
    } else {
        $result = $model->where(['id' => $id])->update(['type' => $type , 'data_id' => $data_id , 'data_neeq' => $neeq , 'plannoticeddate' => $plannoticeddate]);
        $id = $id;
    }
    $result1 = model("SystemConfig")->where(['sc_id' => 1])->update(['current' => $id]); 
    if($result && $result1){
        
        return $id;
        
    } else {
        return false;
    }
}
/**
 获取pdf文件名(不包含.pdf)后判断长度是否大于10,大于10截取10的长度
*/
 function substrPdfName($name='')
 {
    if(!$name){
        return null;
    }
    $point = strrpos($name,'.');
    if(!$point){
        return null;
    }
    $name = substr($name,0,$point);
    if(mb_strlen($name,'utf-8')>16){
        $name = mb_substr($name, 0,14,'utf-8').'...'.mb_substr($name,-2,2,'utf-8');
    }
    $name.='.pdf';
    return $name;
 }
 //获取pdf大小,并统一转换为KB
 function changeTokb($pdfSize='')
 {
    if(!$pdfSize){
        return null;
    }
    $pdfSize = round($pdfSize/1024,2);
    $pdfSize.='KB';
    return $pdfSize;
 }
