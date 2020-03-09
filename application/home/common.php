<?php
    /**
     * 日志写入
     * @param 信息内容  $message
     */ 
    function faLog($message)
    {
        if (is_array($message) && count($message) > 0) {
            $message = json_encode($message);
        }
        error_log("[".date('Y-m-d H:i:s')."] ".$message."\r\n", 3, LOG_PATH."/fa.log");
    }
    
    
    function subStrLen($str,$length)
    {
        $val = "";
        $len = mb_strlen($str);
        if($len>=$length){
            $val = mb_substr($str,0,$length,'utf-8').'...';
        }else{
            $val = $str;
        }  
        return $val;
    }
    
    /**
     * 中文字符串分割
     * @param 要分割的字符串 $str
     * @param 截取几位分割 $count
     * @return 数组
     */
    function zh_str_split($str,$count){
        $leng   = strlen($str)/3;     //中文长度
        $arr    = array();
        for ($i=0; $i < $leng; $i+=$count) {
            $arr[] = mb_substr($str, $i, $count,'utf-8');
        }
        return $arr;
    }
    
?>
