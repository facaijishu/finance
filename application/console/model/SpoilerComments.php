<?php

namespace app\console\model;

use think\Model;

class SpoilerComments extends Model
{
    public function deleteSpoilerComments($id){
        //开启事务
        $this->startTrans();
        try{
            $admin = \app\console\service\User::getInstance()->getInfo();
            $arr = [];
            $arr['status'] = -1;
            $arr['delete_uid'] = $admin['uid'];
            $this->where(['spc_id' => $id])->update($arr);
            $this->where(['pid' => $id])->update($arr);
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