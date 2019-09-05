<?php

namespace app\home\model;

//use think\Model;

class CarouselFigure extends BaseModel
{
    public function getCarouselFigureList(){
        $result = $this->where(['status' => 1])->order("list_order desc")->select();
        $model = model("MaterialLibrary");
        foreach ($result as $key => $value) {
            $img = $model->getMaterialInfoById($value['img']);
            $result[$key]['img_url'] = $img['url'];
        }
        return $result;
    }
    //获取轮播图
    public function getCarouselFigureListApi()
    {
        $data = $this->alias('cf')
                 ->join('MaterialLibrary ml','cf.img=ml.ml_id')
                 ->field('cf.url,ml.url as img_url')
                 ->where(['cf.status'=>1])
                 ->order('cf.list_order desc,cf.cf_id desc')
                 ->select();
        if(empty($data)){
            $this->code = 201;
            $this->msg = '数据不存在';
         return $this->result('',$this->code,$this->msg);
         
        }        
       return $this->result($data);
        
    }
}
