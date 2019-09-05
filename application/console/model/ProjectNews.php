<?php

namespace app\console\model;

use think\Model;

class ProjectNews extends Model
{
    public function getProjectNewsInfoById($id){
        if(empty($id)){
            $this->error = '该新闻id不存在';
            return false;
        }
        $result = $this->where(['n_id' => $id])->find();
        $model = model("MaterialLibrary");
        $top_img = $model->getMaterialInfoById($result['top_img']);
        $result['top_img_name'] = $top_img['name'];
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
        $result = $this->where(['pro_id' => $id , 'status' => 1])->limit($limit)->order('n_id desc')->select();
        return $result;
    }
    public function createProjectNews($data){
        if(empty($data)){
            $this->error = '新闻数据不存在';
            return false;
        }
        //验证数据信息
        $validate = validate('ProjectNews');
        $scene = isset($data['n_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            if(isset($data['n_id'])){
                $arr = [];
                $arr['title'] = $data['title'];
                $arr['source'] = $data['source'];
                $arr['author'] = $data['author'];
                $arr['top_img'] = $data['top_img'];
                $arr['content'] = $data['content'];
                $this->where(['n_id' => $data['n_id']])->update($arr);
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
    public function deleteProjectNews($id){
        if(empty($id)){
            $this->error = '该新闻id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['n_id' => $id])->update(['status' => -1]);
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