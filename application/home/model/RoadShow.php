<?php

namespace app\home\model;

use think\Model;

class RoadShow extends Model
{
    public function getRoadShowList($status){
        $result = $this->where(['status' => $status])->order("list_order desc")->select();
        $model  = model("dict");
        foreach ($result as $key => $value) {
            $info = $model->getDictInfoById($value['type']);
            $result[$key]['type'] = $info['value'];
            
            $str  = preg_replace('/\n/', '<br>', $value['road_introduce']);
            $road_introduce = explode('<br>', $str);
            $result[$key]['road_introduce'] = $road_introduce;
        }
        return $result;
    }
    public function getCollectionRoadShowList($road_show){
        if(!empty($road_show)){
            $road_show = explode(',', $road_show);
            $arr = [];
            $model = model("dict");
            foreach ($road_show as $key => $value) {
                $result = $this->where(['rs_id' => $value])->find();
                $info = $model->getDictInfoById($result['type']);
                $result['type'] = $info['value'];
                $str = preg_replace('/\n/', '<br>', $result['road_introduce']);
                $road_introduce = explode('<br>', $str);
                $result['road_introduce'] = $road_introduce;
                array_push($arr, $result);
            }
        } else {
            $arr = '';
        }
        return $arr;
    }
}