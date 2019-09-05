<?php

namespace app\console\model;

use think\Model;

class ProjectQa extends Model
{
    public function getProjectQaInfoById($id){
        if(empty($id)){
            $this->error = '该问答id不存在';
            return false;
        }
        $result = $this->where(['qa_id' => $id])->find();
        return $result;
    }

    public function getProjectListByProId($id , $start = '' , $length = ''){
        if(empty($id)){
            $this->error = '该项目id不存在';
            return false;
        }
        if($start == '' && $length == ''){
            $limit = '';
        } else {
            $limit = $start.','.$length;
        }
        $result = $this->where(['pro_id' => $id , 'status' => 1])->limit($limit)->order('qa_id desc')->select();
        return $result;
    }
    public function setProjectQa($id , $action){
        if(empty($id)){
            $this->error = '该问答id不存在';
            return false;
        }
        if(empty($action)){
            $this->error = '请选择需要修改的内容';
            return false;
        }
        $info = $this->where(['qa_id' => $id])->find();
        if($action == 'hide'){
            if($info['hide'] == 0){
                $result = $this->where(['qa_id' => $id])->update(['hide' => 1]);
            } else {
                $result = $this->where(['qa_id' => $id])->update(['hide' => 0]);
            }
        }elseif ($action == 'hot') {
            if($info['hot'] == 0){
                $result = $this->where(['qa_id' => $id])->update(['hot' => 1]);
            } else {
                $result = $this->where(['qa_id' => $id])->update(['hot' => 0]);
            }
        }elseif ($action == 'status') {
            if($info['status'] == 0){
                $result = $this->where(['qa_id' => $id])->update(['status' => 1]);
            } else {
                $result = $this->where(['qa_id' => $id])->update(['status' => 0]);
            }
        }
        return $result;
    }

    public function createProjectQa($data){
        if(empty($data)){
            $this->error = '问答数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('ProjectQa');
        $scene = isset($data['qa_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            if(isset($data['qa_id'])){
                $arr = [];
                $arr['type'] = $data['type'];
                $arr['question'] = $data['question'];
                $arr['answer'] = $data['answer'];
                $this->where(['qa_id' => $data['qa_id']])->update($arr);
            } else {
                $info = \app\console\service\User::getInstance()->getInfo();
                $data['create_time'] = time();
                $data['create_uid'] = $info['uid'];
                $data['status'] = 1;
                $this->allowField(true)->save($data);
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