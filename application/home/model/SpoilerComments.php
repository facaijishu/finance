<?php

namespace app\home\model;

use think\Model;

class SpoilerComments extends Model
{
    public function getSpoilerCommentsList($sp_id , $pid = 0){
        $result = [];
        $result = $this->alias("spc")
                        ->join("member m","spc.uid=m.uid")
                        ->field("spc.*,m.userName")
                        ->where(['sp_id' => $sp_id , 'pid' => $pid , 'spc.status' => 1])
                        ->order("create_time asc")
                        ->select();
        return $result;
    }

    public function createSpoilerComments($data){
        if(empty($data)){
            $this->error = '评论数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('SpoilerComments');
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
            $arr['sp_id'] = $data['sp_id'];
            $arr['pid'] = $data['pid'];
            $arr['content'] = $data['content'];
            $arr['uid'] = session('FINANCE_USER.uid');
            $arr['create_time'] = time();
            $result = $this->allowField(true)->save($arr);
            if($result){
                $id = $this->getLastInsID();
                $result = $this->alias("spc")
                        ->join("member m","spc.uid=m.uid")
                        ->field("spc.*,m.userName")
                        ->where(['spc.spc_id' => $id])
                        ->find();
            }
                // 提交事务
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}