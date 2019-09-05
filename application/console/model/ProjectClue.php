<?php

namespace app\console\model;

use think\Model;

class ProjectClue extends Model
{
    public function getProjectClueInfoById($id){
        if(empty($id)){
            $this->error = '该线索id不存在';
            return false;
        }
        $result = $this->where(['clue_id' => $id])->find();
        return $result;
    }
    public function setProjectClueStatus($id , $status){
        if(empty($id)){
            $this->error = '该线索id不存在';
            return false;
        }
        if(!is_numeric($status)){
            $this->error = '线索状态不为整数';
            return false;
        }
        $data = [];
        $data['status'] = $status;
        $result = $this->where(['clue_id' => $id])->update($data);
        return $result;
    }
}