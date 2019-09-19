<?php

namespace app\home\model;

//use think\Model;

class Consultation extends BaseModel
{
    public function getConsultationInfoById($id){
        $result = $this->where(['con_id' => $id])->find();
        $model = model("User");
        $user = $model->getUserInfoById($result['create_uid']);
        $result['create_uid'] = $user['real_name'];
        $result['time'] = substr($result['create_time'], 5, 11);
        return $result;
    }

    public function getConsultationList($length = 0){
        if($length === 0){
            $result = $this->alias('con')
                    ->join("dict d" , 'con.type = d.id')
                    ->where(['con.status' => 1 , 'd.des' => ''])
                    ->order("is_hot desc,con_id desc")
                    ->select();
        } else {
            $result = $this->alias('con')
                    ->join("dict d" , 'con.type = d.id')
                    ->where(['con.status' => 1 , 'd.des' => ''])
                    ->order("is_hot desc,con_id desc")
                    ->limit($length)
                    ->select();
        }
        $model = model("MaterialLibrary");
        foreach ($result as $key => $value) {
            $top_img = $model->getMaterialInfoById($value['top_img']);
            $result[$key]['top_img'] = $top_img['url'];
            $result[$key]['top_img_compress'] = $top_img['url_compress'];
        }
        return $result;
    }
    public function getConsultationNoticeList(){
        $result = $this->alias('con')
                    ->join("dict d" , 'con.type = d.id')
                    ->where(['con.status' => 1 , 'd.des' => 'notice'])
                    ->order("is_hot desc,con_id desc")
                    ->select();
            return $result;
    }
    /**
     *
     *
     * FA財4.0API
     */
    public function getConsultationListApi($length = 0)
    {
       if(!$length){
          $this->code = 207;
          $this->msg = '数据展示条数参数不正确';
         return $this->result('',$this->code,$this->msg);
         
       }
       $result = $this->alias('con')
                      ->join("Dict d" , 'con.type = d.id')
                      ->join("MaterialLibrary ml",'con.top_img = ml.ml_id')
                      ->field('con.con_id,con.title,con.sort_name,con.release_date,ml.url_compress')
                      ->where(['con.status' => 1 , 'd.des' => ''])
                      ->order("is_hot desc,con_id desc")
                      ->limit($length)
                      ->select();
        if(empty($result)){
            $this->code = 201;
            $this->msg = '数据不存在';
           return $this->result('',$this->code,$this->msg);
           
        }
        $this->code = 200;
        $this->msg = '';
       return $this->result($result,$this->code,$this->msg);
       
    }
}
