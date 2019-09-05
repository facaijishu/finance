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
?>
