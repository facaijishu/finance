<?php

namespace app\console\model;

use think\Model;

class SystemConfig extends Model{
    public function getSystemConfigInfo(){
        $info = $this->select();
        if(!empty($info)){
            $info   = $info[0];
            $model  = model("MaterialLibrary");
            if($info['logo_img'] != ''){
                $logo_img_info = $model->getMaterialInfoById($info['logo_img']);
                $info['logo_img_name'] = $logo_img_info['name'];
            }
            if($info['kf_img'] != ''){
                $kf_img_info = $model->getMaterialInfoById($info['kf_img']);
                $info['kf_img_name'] = $kf_img_info['name'];
            }
            if($info['policy'] != ''){
                $policy = $model->getMaterialInfoById($info['policy']);
                $info['policy_name'] = $policy['name'];
            }
            if($info['gz_img'] != ''){
                $gz_img = $model->getMaterialInfoById($info['gz_img']);
                $info['gz_img_name'] = $gz_img['name'];
            }
            if($info['question'] != ''){
                $question = $model->getMaterialInfoById($info['question']);
                $info['question_name'] = $question['name'];
            }
        } else {
            $info = [];
        }
        return $info;
    }
    public function setSystemConfig($data){
        if(empty($data)){
            $this->error = '缺少系统设置数据';
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            $info = $this->getSystemConfigInfo();
            $arr = [];
            $arr['title'] = $data['title'];
            $arr['logo_img'] = $data['logo_img'];
            $arr['sign'] = $data['sign'];
            $arr['kf_img'] = $data['kf_img'];
            $arr['policy'] = $data['policy'];
            $arr['question'] = $data['question'];
            $arr['guanzhu_title'] = $data['guanzhu_title'];
            $arr['gz_img'] = $data['gz_img'];
            $arr['subscribe'] = $data['subscribe'];
            $arr['zfmxes_des'] = $data['zfmxes_des'];
            $arr['zryxes_des'] = $data['zryxes_des'];
            $arr['road_show_des'] = $data['road_show_des'];
            if(empty($info)){
                $this->allowField(true)->save($arr);
            } else {
                $this->where(['sc_id' => 1])->update($arr);
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
}
