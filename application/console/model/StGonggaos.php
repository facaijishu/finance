<?php

namespace app\console\model;

use think\Model;

class StGonggaos extends Model
{
    public function getStGonggaosInfoById($id){
        $info = $this->where(['id' => $id])->find();
        return $info;
    }
    public function setStGonggaosExamine($id){
        if(empty($id)){
            $this->error = '相关公告id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            $status = db("st_gonggaos_status")->where(['ggid' => $info['ggid']])->find();
            if(empty($status)){
                db("st_gonggaos_status")->insert(['ggid' => $info['ggid'] , 'examine' => 1]);
            } else {
                db("st_gonggaos_status")->where(['ggid' => $info['ggid']])->update(['examine' => 1]);
            }
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function setStGonggaosExaminefail($id){
        if(empty($id)){
            $this->error = '相关公告id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = $this->where(['id' => $id])->find();
            $status = db("st_gonggaos_status")->where(['ggid' => $info['ggid']])->find();
            if(empty($status)){
                db("st_gonggaos_status")->insert(['ggid' => $info['ggid'] , 'examine' => 2]);
            } else {
                db("st_gonggaos_status")->where(['ggid' => $info['ggid']])->update(['examine' => 2]);
            }
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