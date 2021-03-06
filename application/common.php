<?php
use sys\Wechat;
set_time_limit(0);
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
        $AccessToken    = getAccessToken();
        $access_token   = $AccessToken['access_token'];
        if (isset($AccessToken['access_token'])) {
            $arr = [];
            $arr['expires_in'] = intval(time()) + intval($AccessToken['expires_in']); //保存时间
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

    
    /**
     * 是否关注公众号
     * @param unknown $openId
     * @param unknown $access_token
     * @return boolean
     */
    function isFollow($openId , $access_token) 
    {
        $url    = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $openId . '&lang=zh_CN';
        $result = httpGet($url);
        $result = json_decode($result, true);
        if ($result['subscribe'] == 0) {
            return false;
        } elseif ($result['subscribe'] == 1) {
            return true;
        }
    }
   
    /**
     * 微信消息发送
     * @param 模板ID $template_id
     * @param openId $openId
     * @param 跳转link $url
     * @param 主题内容 $array
     * @return 微信发送结果集合
     */
    function sendTemplateMessage($template_id , $openId , $url , $array){
        $config  = config("WeiXinCg");
        $options = array(
            'token'          => config("token"), //填写你设定的key
            'encodingaeskey' => config("encodingaeskey"), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid'          => $config['appid'],
            'appsecret'      => $config['appsecret']
        );
        $weObj = new Wechat($options);
        $weObj->checkAuth('', '', getAccessTokenFromFile());
        $data = [
            'touser'        => $openId,
            'template_id'   => $template_id,
            'url'           => $url,
            'topcolor'      => '#FF0000',
            'data'          => $array
        ];
        $result = $weObj->sendTemplateMessage($data);
        
        if(!$result){
            //$weObj->text('错误原因1：'.$weObj->errCode."-".$weObj->errMsg)->reply();
            $error['errcode']   = $weObj->errCode;
            $error['errmsg']    = $weObj->errMsg;
            $error['tmp_id']    = $template_id;
            $error['openId']    = $openId;
            return $error;
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
    $model  = model("QrcodeDirection");
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
    //echo 111;
    if($length < 95000){
        //echo 222;
        $info = $model->allowField(true)->save(['type' => $type , 'data_id' => $data_id , 'data_neeq' => $neeq , 'plannoticeddate' => $plannoticeddate]);
        $org_id = $model->getLastInsID();
        if($info){
            //echo 333;
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
            //echo 444;
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
 * [createUserEditQrcodeDirection 生成已完善用户信息的认证合伙人专属二维码]
 * @param  integer $current         []
 * @param  [type]  $type            [表类型through]
 * @param  [type]  $data_id         [关联id]
 * @param  [type]  $neeq            [description]
 * @param  [type]  $plannoticeddate [description]
 * @return [type]                   [生成的二维码的唯一id]
 */
function createUserEditQrcodeDirection($current = 0 , $type , $data_id , $neeq , $plannoticeddate){
    $model = model("QrcodeDirection");
    $length = $model->count();
    if($current < 95000){
        $id = intval($current)+1;
    } else {
        $id = 1;
    }
    //echo 111;
    if($length < 95000){
        //echo 222;
        $info = $model->allowField(true)->save(['type' => $type , 'data_id' => $data_id , 'data_neeq' => $neeq , 'plannoticeddate' => $plannoticeddate]);
        $qr_id = $model->getLastInsID();
        if($info){
            //echo 333;
            $weObj = new Wechat(config("WeiXinCg"));
            $access_token = getAccessTokenFromFile();
            $result = $weObj->checkAuth('', '', $access_token);

            $result = $weObj->getQRCode($qr_id, 1);
           
            $result = $weObj->getQRUrl($result['ticket']);
            $filename = $qr_id.genRandomString(10).'.jpg';
            $result = getImage($result, config("QRCodePath3"), $filename, 1);
            
            if($result['error']){
                return false;
            } else {
                $result = $model->where(['id' => $qr_id])->update(['qrcode_path' => $filename]);
                
            }

        } else {
            //echo 444;
            return false;
        }
        $id = $qr_id;
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
 /**
 * 前台展示状态
 * @param  int $is_show
 * @return str $result
 */
function is_show($is_show = 0)
{
   switch ($is_show) {
    case 1:
        $result = '展示中';
        break;
    case 2:
        $result = '已过期';
        break;
    case 3:
        $result = '已删除';
        break;
    default:
        $result = '未知';
        break;
   }
   return $result;
}
/**后台操作状态
 * @param  int $status
 * @return str $result
 */
function status($status = 0)
{
   
   switch ($status) {
    case 1:
        $result = '展示';
        break;
    case 2:
        $result = '隐藏';
        break;
    default:
        $result = '未知';
        break;
   }
   return $result;   
}
/**通用'是否'转换
 * @param int $value
 * @return str $result
 */
function YesOrNo($value = 0)
{
   switch ($value) {
    case 1:
        $result = '是';
        break;
    case 2:
        $result = '否';
        break;
    default:
        $result = '未知';
        break;
    }
    return $result;
}
/**
 * [所属层级]
 * @param  [int] $value [description]
 * @return [str] $result [description]
 */
function level($value = 0)
{
    switch ($value) {
        case 1:
            $result = '基础层';
            break;
        case 2:
            $result = '创新层';
            break;
        default:
            $result = '未知';
            break;
    }
    return $result;
} 
/**
 * [trans_mode 交易方式]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function trans_mode($value = 0)
{
    switch ($value) {
        case 1:
            $result = '做市';
            break;
        case 2:
            $result = '竞价';
            break;
        default:
            $result = '未知';
            break;
    }
    return $result;
}
/**
 * [period 融资/管理期限]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function period($value = 0)
{
    switch ($value) {
        case 1:
            $result = '一年以内';
            break;
        case 2:
            $result = '二年以内';
            break;
        case 3:
            $result = '三年以内';
            break;  
        default:
            $result = '未知';
            break;
    }
    return $result;
}

/**
 * [validity_period 业务有效期]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function validity_period($value = 0)
{
    switch ($value) {
        case 1:
            $result = '30天';
            break;
        case 2:
            $result = '60天';
            break;
        case 3:
            $result = '90天';
            break;
        case 4:
            $result = '180天';
            break;      
        default:
            $result = '未知';
            break;
    }
    return $result;
}

function validity_deadline($value = 0)
{
    switch ($value) {
        case 1:
            $result = 30;
            break;
        case 2:
            $result = 60;
            break;
        case 3:
            $result = 90;
            break;
        case 4:
            $result = 180;
            break;
        default:
            $result = 0;
            break;
    }
    return $result;
}


/**
 * [contact_status 发布项目联系人身份]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function contact_status($value = 0)
{
    switch ($value) {
        case 1:
            $result = '融资方';
            break;
        case 2:
            $result = '转让方';
            break;
        case 3:
            $result = '代理方';
            break;
        case 4:
            $result = '质押方';
            break;
        case 5:
            $result = '出售方';
            break;  
        case 6:
            $result = '票据方';
            break;  
        case 7:
            $result = '资产方';
            break;  
        case 8:
            $result = '创业方';
            break; 
        case 9:
            $result = '发起方';
            break;
        default:
            $result = '未知';
            break;
    }
    return $result;
}

/**
 * [contact_status 发布资金联系人身份]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function contact_status_org($value = 0)
{
    switch ($value) {
        
        case 1:
            $result = '发起方';
            break;
        case 2:
            $result = '代理人';
            break;  
        default:
            $result = '未知';
            break;
    }
    return $result;
}
/**
 * [investor_preference 投资者偏好]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function investor_preference($value = 0)
{
    switch ($value) {
        case 1:
            $result = '上市公司';
            break;
        case 2:
            $result = '其他机构';
            break;
        default:
            $result = '未知';
            break;
    }
    return $result;
}

/**
 * [inc_credit_way 增信方式]
 * @param  integer $value [description]
 * @return [type]         [description]
 */
function inc_credit_way($value = 0)
{
    switch ($value) {
        case 1:
            $result = '抵押';
            break;
        case 2:
            $result = '质押';
            break;
        case 3:
            $result = '保证';
            break;
        case 4:
            $result = '信用';
            break;
        case 5:
            $result = '多重信用';
            break;  
        default:
            $result = '未知';
            break;
    }
    return $result;
}

/**
 * [acceptor 承兑人]
 * @param  integer $value [description]
 * @return [type]         [description]
 */
function acceptor($value = 0)
{
    switch ($value) {
        case 1:
            $result = '商票';
            break;
        case 2:
            $result = '国股';
            break;
        case 3:
            $result = '承商';
            break;
        case 4:
            $result = '三农';
            break;
        case 5:
            $result = '财票';
            break;  
        default:
            $result = '其他';
            break;
    }
    return $result;
}

/**
 * [bill_type 票据类型]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function bill_type($value = 0)
{
     switch ($value) {
        case 1:
            $result = '商票';
            break;
        case 2:
            $result = '银票';
            break;
        default:
            $result = '未知';
            break;
     }
     return $result;
}

/**
 * [lc_type 上市公司类型]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function lc_type($value = 0)
{
    switch ($value) {
        case 1:
            $result = '主板';
            break;
        case 2:
            $result = '中小板';
            break;
        case 3:
            $result = '创业板';
            break;
        case 4:
            $result = '其他';
            break;
        default:
            $result = '未知';
            break;
    }
    return $result;
}

/**
 * [product_status 产品状态]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function product_status($value = 0)
{
    switch ($value) {
        case 1:
            $result = '预售期';
            break;
        case 2:
            $result = '存续中';
            break;
        default:
            $result = '其他';
            break;
    }
    return $result;
}
/**
 * [role_type 合伙人身份]
 * @param  integer $value [description]
 * @return [str]         [description]
 */
function role_type($value = 0)
{
    switch ($value) {
        case 1:
            $result = '投资人';
            break;
        case 2:
            $result = '项目方';
            break;
        default:
            $result = '未知';
            break;
    }
    return $result;
}
/**
 * [getTree 向下遍历子孙节点的递归树]
 * @param  array   $data  [数据表原始数据]
 * @param  integer $fid   [父节点]
 * @param  integer $level [层级]
 * @return [array/object]         [处理后的数据]
 */
function getTree($data =[],$fid = 0,$level = 0)
{   
    static $list = [];
    foreach ($data as $k => $v) {
        if ($v['fid'] == $fid){
           $v['level'] = $level;
           $list[] = $v;
           unset($data[$k]);
           getTree($data,$v['id'],$level+1);
        }
    }
    return $list;

}

/**
 * 友好时间显示
 * @param $time
 * @return bool|string
 */
function friend_date($time)
{
    if (!$time)
        return false;
    $fdate = '';
    $d = time() - intval($time);
    $ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
    $md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
    $byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y年m月d日', $time);
                break;
            case $d < $td:
                $fdate = '后天' . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = '明天' . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . '秒前';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '分钟前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '小时前';
                break;
            case $d < $yd:
                $fdate = '昨天' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前天' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m月d日 H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m月d日', $time);
                break;
            default:
                $fdate = date('Y年m月d日', $time);
                break;
        }
    }
    return $fdate;
}
//联系人身份
 function getContactStatus($id)
{
    switch (intval($id)) {
        case 1:
            $contact_status = '资金方';
            break;
        case 2:
            $contact_status = '代理人';
            break;
        case 3:
            $contact_status = '发起方';
            break;
        case 4:
            $contact_status = '融资方';
            break;
        case 5:
            $contact_status = '票方';
            break;
        case 6:
            $contact_status = '资方';
            break;    
        default:
            $contact_status = '未知';
            break;
    }
    return $contact_status;
}
//资金类型
function getFundType($id)
{
    switch (intval($id)) {
        case 1:
            $fund_type = '非金融机构';
            break;
        case 2:
            $fund_type = '金融机构';
            break;
        case 3:
            $fund_type = '个人投资机构';
            break;
        default:
            $fund_type = '未知';
            break;
    }
    return $fund_type;
}
//投融资期限
function getFinancingPeriod($id)
{
    switch (intval($id)) {
        case 1:
            $financing_period = '一年以内';
            break;
        case 2:
            $financing_period = '二年以内';
            break;
        case 3:
            $financing_period = '三年以内';
            break;
        default:
            $financing_period = '未知';
            break;
    }
    return $financing_period;
}
//投资方式
function getInvestMode($id)
{
    switch (intval($id)) {
        case 1:
            $invest_mode = '优先级';
            break;
        case 2:
            $invest_mode = '劣后级';
            break;
        case 3:
            $invest_mode = '夹层';
            break;
        case 4:
            $invest_mode = '平层';
            break;
        case 5:
            $invest_mode = '其他';
            break;
        default:
            $invest_mode = '未知';
            break;
    }
    return $invest_mode;
}
//发布项目和资金状态判断
function prorStatus($create_time,$validity_period)
{
    switch (intval($validity_period)) {
        case 1:
            //30天
            
            $end_time = $create_time+30*24*3600;
            if (time()<$end_time) {
                $tips = '进行中';
            } else {
                $tips = '已结束';
            }
            
            break;
        case 2:
            //60天
            
            $end_time = $create_time+60*24*3600;
            if (time()<$end_time) {
                $tips = '进行中';
            } else {
                $tips = '已结束';
            }
            break;
        case 3:
            //90天
            $end_time = $create_time+90*24*3600;
            if (time()<$end_time) {
                $tips = '进行中';
            } else {
                $tips = '已结束';
            }

            break;
        case 4:
            //180天
            $end_time = $create_time+180*24*3600;
            if (time()<$end_time) {
                $tips = '进行中';
            } else {
                $tips = '已结束';
            }
            break;
        default:
            $tips = '未知';
            break;
    }
    return $tips;
}
//发布项目和资金状态判断2
function prorStatus2($create_time,$validity_period)
{
    switch (intval($validity_period)) {
        case 1:
            //30天
            
            $end_time = strtotime($create_time)+30*24*3600;
            if (time()<$end_time) {
                $tips = true;
            } else {
                $tips = false;
            }
            
            break;
        case 2:
            //60天
            
            $end_time = strtotime($create_time)+60*24*3600;
            if (time()<$end_time) {
                $tips = true;
            } else {
                $tips = false;
            }
            break;
        case 3:
            //90天
            $end_time = strtotime($create_time)+90*24*3600;
            if (time()<$end_time) {
                $tips = true;
            } else {
                $tips = false;
            }

            break;
        case 4:
            //180天
            $end_time = strtotime($create_time)+180*24*3600;
            if (time()<$end_time) {
                $tips = true;
            } else {
                $tips = false;
            }
            break;
        default:
            $tips = false;
            break;
    }
    return $tips;
}
//发布项目/资金截止日期(xxxx-xx-xx)
function deadline($start,$length)
{   
    $is_int = is_int($start);
    if (!$is_int) {
        $start = strtotime($start);
    }
    
    $day = 24*3600;
    switch (intval($length)) {
        case 1:
            $length = 30*$day;
            break;
        case 2:
            $length = 60*$day;
            break;
        case 3:
            $length = 90*$day;
            break;
        case 4:
            $length = 180*$day;
            break;
        default:
            $length = 0;
            break;
    }
    $date = date('Y-m-d',$start+$length);
    return $date;
}
//去除字符串中的双引号
function removeMark($str='')
{
    $str = str_replace('"', '', $str);
    return $str;
}

/**
 * 日志写入
 * @param 信息内容  $message
 */
function wechatLog($message)
{
    if (is_array($message) && count($message) > 0) {
        $message = json_encode($message);
    }
    error_log("[".date('Y-m-d H:i:s')."] ".$message."\r\n", 3, LOG_PATH."/wechat.log");
}
