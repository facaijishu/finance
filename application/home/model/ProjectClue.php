<?php

namespace app\home\model;

use think\Model;

class ProjectClue extends Model
{
    public function getProjectClueInfoById($id){
        if(empty($id)){
            $this->error = '项目线索id不存在';
            return false;
        }
        $result = $this->where(['clue_id' => $id])->find();
        if(!empty($result['img'])){
            $result['img'] = explode(',', $result['img']);
        } else {
            $result['img'] = '';
        }
        $model = model("Dict");
        $capital_plan = $model->getDictInfoById($result['capital_plan']);
        $result['capital_plan'] = $capital_plan['value'];
        return $result;
    }

    public function getProjectClueList($uid){
        if(empty($uid)){
            $this->error = '用户uid不存在';
            return false;
        }
        $result = $this->where(['create_uid' => $uid])->order("status asc")->select();
        foreach ($result as $key => $value) {
            $result[$key]['img'] = explode(',', $value['img']);
        }
        return $result;
    }

    public function createProjectClue($data){
        if(empty($data)){
            $this->error = '项目数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('ProjectClue');
        $scene = 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $data['create_time'] = time();
            $data['img'] = trim($data['img'], ',');
            $data['create_uid'] = session('FINANCE_USER.uid');
            $data['status'] = 0;
            $this->allowField(true)->save($data);
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