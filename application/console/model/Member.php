<?php

namespace app\console\model;

use think\Model;

class Member extends Model
{
    public function getMemberInfoById($id){
        $result = $this->where(['uid' => $id])->find();
        return $result;
    }
    public function getMemberList($status = 2){
        $result = $this->where(['userType' => $status])->select();
        return $result;
    }
}