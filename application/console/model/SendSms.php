<?php

namespace app\console\model;

use think\Model;

class SendSms extends Model{
    
    
    public function addSms($mobile,$content){
        $data['mobile']      = $mobile;
        $data['content']     = $content;
        $res =  $this->createSms($data);
        return $res;
    }
    
    public function createSms($data){
        faLog(json_encode($data));
        if(empty($data)){
            $this->error = '数据为空，无法插入表';
            return false;
        }
        //验证数据信息
        $validate   = validate('SendSms');
        $scene      = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error  = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $arr                = [];
            $arr['mobile']      = $data['mobile'];
            $arr['content']     = $data['content'];
            $arr['create_time'] = time();
            $arr['status']      = 0;
            $aa = $this->allowField(true)->save($arr);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}