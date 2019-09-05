<?php

namespace app\console\model;

use think\Model;

class StYanbaosEffect extends Model
{
    public function getStYanbaosEffectInfoById($id){
        $info = $this->where(['id' => $id])->find();
        return $info;
    }
    public function getStYanbaosEffectInfoByByid($yb_id){
        $info = $this->where(['yb_id' => $yb_id])->find();
        return $info;
    }
    public function createStYanbaosEffect($data){
        if(empty($data)){
            $this->error = '缺少研报数据';
            return false;
        }
        //验证数据信息
        $validate = validate('StYanbaos');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $arr = [];
            $arr['title'] = $data['title'];
            $arr['codename'] = $data['codename'];
            $arr['org'] = $data['org'];
            $arr['authors'] = $data['authors'];
            $arr['rate'] = $data['rate'];
            $arr['change'] = $data['change'];
            $arr['report_date'] = $data['report_date'];
            $arr['relation'] = $data['relation'];
            $arr['text'] = trim($data['text'], ' ');
            if(isset($data['id'])){
                $this->allowField(true)->where(['id' => $data['id']])->update($arr);
            } else {
                $this->allowField(true)->save($arr);
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
    public function releaseStYanbaosEffect($id){
        if(empty($id)){
            $this->error = '相关大研报id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $info = db("st_yanbaos")->where(['id' => $id])->find();
            $result = $this->getStYanbaosEffectInfoByByid($info['yb_id']);
            $authors = db("st_yb_authors")->where(['yb_id' => $info['yb_id']])->select();
            $author = '';
            foreach ($authors as $key => $value) {
                $author .= ','.$value['auth'];
            }
            $author = trim($author, ',');
            $user = \app\console\service\User::getInstance()->getInfo();
            if(empty($result)){
                $arr = [];
                $arr['yb_id'] = $info['yb_id'];
                $arr['change'] = $info['change'];
                $arr['code'] = $info['code'];
                $arr['codename'] = $info['codename'];
                $arr['cprice'] = $info['cprice'];
                $arr['date'] = $info['date'];
                $arr['dprice'] = $info['dprice'];
                $arr['industry'] = $info['industry'];
                $arr['industrycode'] = $info['industrycode'];
                $arr['kcode'] = $info['kcode'];
                $arr['kname'] = $info['kname'];
                $arr['ktype'] = $info['ktype'];
                $arr['org'] = $info['org'];
                $arr['orgcode'] = $info['orgcode'];
                $arr['orgprizeinfo'] = $info['orgprizeinfo'];
                $arr['rate'] = $info['rate'];
                $arr['report_date'] = $info['report_date'];
                $arr['rtype'] = $info['rtype'];
                $arr['rtypecode'] = $info['rtypecode'];
                $arr['space'] = $info['space'];
                $arr['srating_name'] = $info['srating_name'];
                $arr['title'] = $info['title'];
                $arr['text'] = $info['text'];
                $arr['authors'] = $author;
                $arr['relation'] = '';
                $arr['examine'] = 1;
                $arr['create_time'] = time();
                $arr['create_uid'] = $user['uid'];
                $this->allowField(true)->save($arr);
            } else {
                $this->error = '该研报已发布,无法重复发布';
                return false;
            }
            // 提交事务
            $this->commit();
            return $id;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function setStYanbaosEffectExhibitionTrue($id){
        if(empty($id)){
            $this->error = '研报id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->allowField(true)->where(['id' => $id])->update(['examine' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function setStYanbaosEffectExhibitionFalse($id){
        if(empty($id)){
            $this->error = '研报id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->allowField(true)->where(['id' => $id])->update(['examine' => 0]);
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