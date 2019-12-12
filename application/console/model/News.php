<?php

namespace app\console\model;

use think\Model;

class News extends Model
{
    public function getNewsInfoById($id){
        $result         = $this->where(['id' => $id])->find();
        
        $model          = model("user");
        $create_user    = $model->getUserById($result['create_uid']);
        $result['create_uid'] = $create_user['real_name'];
        
        $model  = model("MaterialLibrary");
        $img    = $model->getMaterialInfoById($result['top_img']);
        $result['top_img_url'] = $img['url'];
        return $result;
    }

    public function createNews($data){
        faLog(json_encode($data));
        if(empty($data)){
            $this->error = '缺少资讯数据';
            return false;
        }
		$data['content_pc']   = $data['summernote'];
		$data['content_h5']   = $data['summernote_h5'];
		$data['industry_pro'] = $data['inc_industry_pro']; 
		$data['industry_org'] = $data['inc_industry_org']; 
		
        //验证数据信息
        $validate = validate('News');
        $scene    = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            if(isset($data['id'])){
                $arr = [];
                $arr = $data;
                $this->allowField(true)->save($arr , ['id' => $data['id']]);
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
    public function stopNews($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['status' => -1 , 'is_hot' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    
    public function startNews($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['status' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    public function hotTrueNews($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['is_hot' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function hotFalseNews($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['is_hot' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function deleteNews($id){
        if(empty($id)){
            $this->error = '该资讯id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['id' => $id])->update(['is_show' => -1]);
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