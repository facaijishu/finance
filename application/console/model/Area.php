<?php

namespace app\console\model;

use think\Model;

class Area extends Model{
    public function getAreaList($pid){
        $pid = isset($pid) ? $pid : 0;
        $result = $this->where(['pid' => $pid])->select();
        return $result;
    }
    public function getAreaById($id){
        if(empty($id)){
            $this->error = '不存在地区id';
            return false;
        }
        $result = $this->where(['area_id' => $id])->find();
        return $result;
    }
}