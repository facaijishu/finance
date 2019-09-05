<?php

namespace app\console\model;

use think\Model;

class PcConsultation extends Model
{
    public function getConsultationInfoById($id){
        $result = $this->where(['con_id' => $id])->find();
        $model = model("user");
        $create_user = $model->getUserById($result['create_uid']);
        $result['create_uid'] = $create_user['real_name'];
        $model = model("MaterialLibrary");
        $img = $model->getMaterialInfoById($result['top_img']);
        $result['top_img_url'] = $img['url'];
        return $result;
    }

    public function createConsultation($data){
        if(empty($data)){
            $this->error = '缺少资讯数据';
            return false;
        }
		$data['content'] = $data['summernote'];
        //验证数据信息
        $validate = validate('PcConsultation');
        $scene = isset($data['con_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            if(isset($data['con_id'])){
                $arr = [];
                $arr = $data;
                $this->allowField(true)->save($arr , ['con_id' => $data['con_id']]);
            } else {
                $info = \app\console\service\User::getInstance()->getInfo();
                $data['create_time'] = time();
                $data['create_uid'] = $info['uid'];
                $data['status'] = -1;
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
    public function stopConsultation($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['con_id' => $id])->update(['status' => -1 , 'is_hot' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function startConsultation($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['con_id' => $id])->update(['status' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function hotTrueConsultation($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['con_id' => $id])->update(['is_hot' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function hotFalseConsultation($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['con_id' => $id])->update(['is_hot' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function deleteConsultation($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['con_id' => $id])->update(['is_show' => -1]);
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