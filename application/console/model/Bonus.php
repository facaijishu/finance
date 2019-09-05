<?php

namespace app\console\model;

use think\Model;

class Bonus extends Model
{
    public function createBonusDay($data){
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('Bonus');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $info = \app\console\service\User::getInstance()->getInfo();
            $arr = [];
            $arr['time'] = substr($data['time'], 0, 7);
            $arr['total_amount'] = $data['total_amount'];
            $arr['amount'] = $data['amount'];
            $arr['type'] = 'day';
            $arr['create_time'] = time();
            $arr['create_uid'] = $info['uid'];
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
    public function createBonusMonth($data){
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('Bonus1');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $info = \app\console\service\User::getInstance()->getInfo();
            $arr = [];
            $arr['content'] = $data['content'];
//            $arr['time'] = substr($data['time'], 0, 7);
//            $arr['total_amount'] = $data['total_amount'];
//            $arr['amount'] = $data['amount'];
            $arr['type'] = 'month';
            $arr['create_time'] = time();
            $arr['create_uid'] = $info['uid'];
            $arr['status'] = -1;
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
    public function stopBonus($id){
        if(empty($id)){
            $this->error = '该公告id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['b_id' => $id])->update(['status' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function startBonus($id){
        if(empty($id)){
            $this->error = '该公告id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['b_id' => $id])->update(['status' => 1]);
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