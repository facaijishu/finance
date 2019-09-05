<?php

namespace app\console\model;

use think\Model;

class SendList extends Model
{
    public function getSendListByActId($id , $result , $start = '' , $length = '') {
        if(empty($id)){
            $this->error = '该推送id不存在';
            return false;
        }
        if($start == '' && $length == ''){
            $limit = '';
        } else {
            $limit = $start.','.$length;
        }
        $result = $this->where(['send_id' => $id , 'result' => $result])->limit($limit)->order('id asc')->select();
        return $result;
    }

    public function addSendListInfo($data) {
        if(empty($data)){
            $this->error = '缺少消息推送详细数据';
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $id = $this->saveAll($data);
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
