<?php

namespace app\home\model;

use think\Model;

class User extends Model{
    public function getUserInfoById($id){
        if(empty($id)){
            $this->error = '用户id不存在';
            return false;
        }
        $result = $this->where(['uid' => $id])->find();
        return $result;
    }
}