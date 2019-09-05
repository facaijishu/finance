<?php

namespace app\console\model;

use think\Model;

class DictType extends Model
{
    public function getDictTypeList($type = ''){
        $where = '';
        if($type != ''){
            $where .= 'where type ='.$type;
        }
        $result = $this->where($where)->select();
        return $result;
    }
    public function getDictTypeInfoById($id){
        if(empty($id)){
            $this->error = '该数据类型id不存在';
            return false;
        }
        $info = $this->where(['dt_id' => $id])->find();
        return $info;
    }

    public function createDictType($data){
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('DictType');
        $scene = isset($data['dt_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            if(isset($data['dt_id'])){
                if(empty($data['dt_id'])){
                    $this->error = '该数据类型id错误';
                    return false;
                }
                $arr = [];
                $arr['type'] = $data['type'];
                $arr['name'] = $data['name'];
                $this->where(['dt_id' => $data['dt_id']])->update($arr);
            } else {
                $info = \app\console\service\User::getInstance()->getInfo();
                $arr = [];
                $arr['type'] = $data['type'];
                $arr['name'] = $data['name'];
                $arr['create_time'] = time();
                $arr['create_uid'] = $info['uid'];
                $arr['status'] = 1;
                $this->allowField(true)->save($arr);
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
    public function stopDictType($id){
        if(empty($id)){
            $this->error = '该数据类型id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['dt_id' => $id])->update(['status' => -1]);
            model("dict")->where(['dt_id' => $id])->update(['status' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function startDictType($id){
        if(empty($id)){
            $this->error = '该数据类型id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['dt_id' => $id])->update(['status' => 1]);
            model("dict")->where(['dt_id' => $id])->update(['status' => 1]);
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