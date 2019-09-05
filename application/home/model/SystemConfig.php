<?php

namespace app\home\model;

use think\Model;

class SystemConfig extends Model{
    public function getSystemConfigInfo(){
        $info = $this->select();
        if(!empty($info)){
            $info = $info[0];
            $model = model("MaterialLibrary");
            if($info['logo_img'] != ''){
                $logo_img_info = $model->getMaterialInfoById($info['logo_img']);
                $info['logo_img_name'] = $logo_img_info['name'];
                $info['logo_img_url'] = $logo_img_info['url'];
            }
            if($info['kf_img'] != ''){
                $kf_img_info = $model->getMaterialInfoById($info['kf_img']);
                $info['kf_img_name'] = $kf_img_info['name'];
                $info['kf_img_url'] = $kf_img_info['url_thumb'];
            }
            if($info['policy'] != ''){
                $policy = $model->getMaterialInfoById($info['policy']);
                $info['policy_name'] = $policy['name'];
                $info['policy_url'] = $policy['url'];
            }
            if($info['question'] != ''){
                $question = $model->getMaterialInfoById($info['question']);
                $info['question_name'] = $question['name'];
                $info['question_url'] = $question['url'];
            }
        } else {
            $info = [];
        }
        return $info;
    }
}
