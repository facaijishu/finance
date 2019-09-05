<?php

namespace app\home\model;

use think\Model;

class Message extends Model{
    public function createMessage($data){
        if(empty($data)){
            $this->error = '留言数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('Message');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $arr = [];
            $pro_id = session('RequestAddress.id');
            $arr['pro_id'] = $pro_id;
            $arr['pid'] = 0;
            $arr['content'] = $data['content'];
            $arr['create_time'] = time();
            $arr['last_time'] = time();
            $user = session('FINANCE_USER');
            $uid = $user['uid'];
            $arr['create_uid'] = $uid;
            $arr['status'] = 1;
            $this->allowField(true)->save($arr);
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