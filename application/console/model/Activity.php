<?php

namespace app\console\model;

use think\Model;

class Activity extends Model
{
    /**
     * 依据活动ID查询活动详细系信息
     * @param 活动编号  $id
     * @return 数据集
     */
    public function getActivityInfoById($id){
        $result = $this->where(['a_id' => $id])->find();
        return $result;
    }

    /**
     * 创建新活动
     * @param 页面提交的数据 $data
     * @param 状态 $id
     * @return boolean
     */
    public function createActivity($data,$id){
        if(empty($data)){
            $this->error = '提交异常请重新填写！';
            return false;
        }
        
        $data['content'] = $data['summernote'];
        
        //验证数据信息
        $validate = validate('Activity');
        $scene    = isset($data['a_id']) ? 'edit' : 'add';
        
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        
        $start      = strtotime($data['start_time'].":00");
        $end        = strtotime($data['end_time'].":00");
        $sign_start = strtotime($data['sign_start_time'].":00");
        $sign_end   = strtotime($data['sign_end_time'].":00");
        if($start>$end){
            $this->error = "活动结束时间不能小于活动开始时间";
            return false;
        }
        if($sign_end<$sign_start){
            $this->error = "报名结束时间不能小于报名开始时间";
            return false;
        }
        if($sign_end>$end){
            $this->error = "报名结束时间不能大于活动结束时间";
            return false;
        }
        if($sign_start>$end){
            $this->error = "报名开始时间不能大于活动结束时间";
            return false;
        }
        $data['sign_start_time'] = $sign_start;
        $data['sign_end_time']   = $sign_end;

        //开启事务
        $this->startTrans();
        try{
			if(isset($data['a_id'])){
			    $this->allowField(true)->save($data,['a_id'=> $data['a_id']]);
			} else {
				$data['create_time'] = time();
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
    
    /**
     * 下架活动
     * @param 活动编号 $id
     * @return boolean
     */
    public function stopActivity($id){
        if(empty($id)){
            $this->error = '该活动id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['a_id' => $id])->update(['is_show' => 0]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    /**
     * 上架活动
     * @param 活动编号 $id
     * @return boolean
     */
    public function startActivity($id){
        if(empty($id)){
            $this->error = '该活动id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['a_id' => $id])->update(['is_show' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    
    public function stopActivitySign($id){
        if(empty($id)){
            $this->error = '该活动id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['a_id' => $id])->update(['is_sign_up' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    public function startActivitySign($id){
        if(empty($id)){
            $this->error = '该活动id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['a_id' => $id])->update(['is_sign_up' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    public function showActivityDisabled($id){
        if(empty($id)){
            $this->error = '该活动id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['a_id' => $id])->update(['is_show' => -1]);
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