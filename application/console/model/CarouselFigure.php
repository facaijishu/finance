<?php

namespace app\console\model;

use think\Model;

class CarouselFigure extends Model
{
    public function getCarouselFigureInfoById($id){
        $result = $this->where(['cf_id' => $id])->find();
        $model = model("MaterialLibrary");
        $img = $model->getMaterialInfoById($result['img']);
        $result['img_url'] = $img['url'];
        return $result;
    }

    public function createCarouselFigure($data){
        if(empty($data)){
            $this->error = '缺少轮播图数据';
            return false;
        }
        //验证数据信息
        $validate = validate('CarouselFigure');
        $scene = isset($data['cf_id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            if(isset($data['cf_id'])){
                $arr = [];
                $arr = $data;
                $this->allowField(true)->save($arr , ['cf_id' => $data['cf_id']]);
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
    public function stopCarouselFigure($id){
        if(empty($id)){
            $this->error = '该轮播图id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['cf_id' => $id])->update(['status' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function startCarouselFigure($id){
        if(empty($id)){
            $this->error = '该轮播图id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
//            $count = $this->where(['status' => 1])->count();
//            if($count >= 4){
//                $this->error = '使用的轮播图已达到上限';
//                return false;
//            } else {
                $this->where(['cf_id' => $id])->update(['status' => 1]);
//            }
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