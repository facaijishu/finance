<?php

namespace app\console\model;

use think\Model;

class Spoiler extends Model
{
    public function getSpoilerInfoById($id){
        $result = $this->where(['sp_id' => $id])->find();
        return $result;
    }

    public function createSpoiler($data){
        if(empty($data)){
            $this->error = '缺少剧透数据';
            return false;
        }
		$data['content'] = $data['summernote'];
        //验证数据信息
        $validate = validate('Spoiler');
        $scene = isset($data['sp_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $img = explode(',' , trim($data['img'], ','));
            $material = '';
            foreach ($img as $key => $value) {
                $arr = explode('-', $value);
                $material .= $arr[0].',';
            }
            $data['img'] = trim($material, ',');
            if(isset($data['sp_id'])){
                $arr = [];
                $arr = $data;
                $this->allowField(true)->save($arr , ['sp_id' => $data['sp_id']]);
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
    public function stopSpoiler($id){
        if(empty($id)){
            $this->error = '该剧透id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['sp_id' => $id])->update(['status' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function startSpoiler($id){
        if(empty($id)){
            $this->error = '该剧透id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['sp_id' => $id])->update(['status' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function deleteSpoiler($id){
        if(empty($id)){
            $this->error = '该剧透id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['sp_id' => $id])->update(['is_show' => -1]);
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