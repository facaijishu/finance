<?php

namespace app\home\model;

use think\Model;

class ProjectNews extends Model
{
    public function getProjectNewsInfoById($id){
        if(empty($id)){
            $this->error = '该新闻id不存在';
            return false;
        }
        $result = $this->where(['n_id' => $id])->find();
        $create_time = date("m-d i:s" , intval($result['create_time']));
        $model = model("User");
        $user = $model->getUserInfoById($result['create_uid']);
        $result['create_user'] = $user['real_name'];
        return $result;
    }

    public function getProjectNewsList($id){
        if(empty($id)){
            $this->error = '项目id不存在';
            return false;
        }
        $result = $this->where(['pro_id' => $id , 'status' => 1])->select();
        return $result;
    }
}