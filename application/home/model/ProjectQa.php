<?php

namespace app\home\model;

use think\Model;

class ProjectQa extends Model
{   
    protected $resultSetType = 'collection';
    public function getProjectQaList($id){
        if(empty($id)){
            $this->error = '项目id不存在';
            return false;
        }
        $model = model("dict");
        $qa_type = $model->getDictByType("qa_type");
        $arr = [];
        foreach ($qa_type as $key => $value) {
            $result = $this->where(['pro_id' => $id , 'type' => $value['id'] , 'status' => 1 , 'hide' => 0])->order('hot desc,create_time desc')->select();
            array_push($arr, $result);
        }
        return $arr;
    }
   public function getProjectQa($id){
        if(empty($id)){
            $this->error = '项目id不存在';
            return false;
        }
        $re = $this->alias('pq')
             ->field('pq.question,pq.answer,d.value')
             ->join('Dict d','pq.type=d.id')
             ->where(['pq.pro_id'=>$id,'pq.status'=>1,'pq.hide' => 0])
             ->order('pq.hot desc,pq.create_time desc')
             ->select();
        return $re->toArray();
    }
}
