<?php

namespace app\home\model;

use think\Model;

class RoadShowInfo extends Model
{
    public function getRoadShowInfoById($id){
        $model  = model("RoadShow");
        $result = $model->where(['rs_id' => $id])->find();
        $model  = model("dict");
        $info   = $model->getDictInfoById($result['type']);
        $result['type'] = $info['value'];
        if($result['road_introduce'] != ''){
            $str = preg_replace('/\n/', '<br>', $result['road_introduce']);
            $road_introduce = explode('<br>', $str);
            $result['road_introduce'] = $road_introduce;
        } else {
            $result['road_introduce'] = [];
        }
        if($result['road_detail'] != ''){
            $str = preg_replace('/\n/', '<br>', $result['road_detail']);
            $road_detail = explode('<br>', $str);
            $result['road_detail'] = $road_detail;
        } else {
            $result['road_detail'] = [];
        }
        if($result['speaker_introduce'] != ''){
            $str = preg_replace('/\n/', '<br>', $result['speaker_introduce']);
            $speaker_introduce = explode('<br>', $str);
            $result['speaker_introduce'] = $speaker_introduce;
        } else {
            $result['speaker_introduce'] = [];
        }
        if($result['review'] != ''){
            $str = preg_replace('/\n/', '<br>', $result['review']);
            $review = explode('<br>', $str);
            $result['review'] = $review;
        } else {
            $result['review'] = [];
        }
        return $result;
    }
}