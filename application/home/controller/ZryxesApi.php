<?php
namespace app\home\controller;
use think\think\db;

class ZryxesApi extends BaseApi
{
    public function conlistApi(){
        $keyword   = $this->request->param('keyword')?$this->request->param('keyword'):'';
        $page      = $this->request->param('page')?$this->request->param('page'):1;
        $direction = $this->request->param('direction')?$this->request->param('direction'):'';
        $profit    = $this->request->param('profit')?$this->request->param('profit'):'';
        $income    = $this->request->param('income')?$this->request->param('income'):'';
        $sublevel  = $this->request->param('sublevel')?$this->request->param('sublevel'):'';
        $num       = $this->request->param('num')?$this->request->param('num'):6;
        
        $offset  = ($page-1) * $num;
        //审核状态通过，融资状态为融资中
        $model   = db('ZryxesEffect')->alias('za')
                                     ->join("Gszls gs" , "gs.neeq=za.neeq")
                                     ->field("SQL_CALC_FOUND_ROWS za.id,za.neeq,za.sec_uri_tyshortname,za.price,za.direction,za.num,za.sublevel,gs.xyfl")
                                     ->where(['za.zryx_exhibition' => 1 ,'za.zryx_financing' => 0]);
         $now = date('Y-m-d 0:00:00' , time());
         $model->where(" za.enddate >= '". $now."'");
         
        if('' !== $keyword){
            $model->where("za.sec_uri_tyshortname like '%".$keyword."%' or za.neeq like '%".$keyword."%'");
        }
        
        //买卖方向
        if('' !== $direction){
            if($direction==1){
                $direction_str = "买入";
            }else{
                $direction_str = "卖出";
            }
            $model->where(['za.direction' => $direction_str]);
        }
        
        //净利润
        if('' !== $profit){
            if($profit==1){
                $model->where("za.lirun <= 5000000 ");
            }else if($profit==2){
                $model->where("za.lirun > 5000000 and za.lirun <=20000000");
            }else if($profit==3){
                $model->where("za.lirun > 20000000 and za.lirun <=50000000");
            }else{
                $model->where("za.lirun > 50000000");
            }
        }
        
        //年营业收入
        if('' !== $income){
            if($income==1){
                $model->where("za.shouru <= 5000000 ");
            }else if($income==2){
                $model->where("za.shouru > 5000000 and za.shouru <=20000000");
            }else if($income==3){
                $model->where("za.shouru > 20000000 and za.shouru <=50000000");
            }else if($income==4){
                $model->where("za.shouru > 50000000 and za.shouru <=100000000");
            }else if($income==5){
                $model->where("za.shouru > 100000000 and za.shouru <=200000000");
            }else{
                $model->where("za.shouru > 200000000 ");
            }
            
        }
        
        //层级
        if('' !== $sublevel){
            if($sublevel==1){
                $sublevel_str = "基础层";
            }else{
                $sublevel_str = "创新层";
            }
            $model->where(['za.sublevel' => $sublevel_str]);
        }
   
        
        $list = $model->order("boutique desc,stick desc,enddate desc")->limit($offset , $num)->select();
        $data = [];
        foreach ($list as $key => $value) {
            $industry               = explode("-",$value['xyfl']);
            $list[$key]['industry'] = $industry[0];
            $list[$key]['url']      = "/home/zryxes_effect_info/index/id/".$value['id'];
            if ($value['num'] >= 100000000) {
                $list[$key]['num'] = round($value['num']/100000000,2).'亿';
            } elseif ($value['num'] >= 10000) {
                $list[$key]['num'] = round($value['num']/10000,2).'万';
            }
            
        }
        if (empty($list)) {
            $code           = 214;
            $msg            = '抱歉没有发现的相关内容';
            return $this->result('',$code,$msg,'json');
        }else{
            $code           = 200;
            $msg            = '';
            $data['zryx'] = $list;
            return $this->result($data,$code,$msg,'json');
        }
    } 
	
}
