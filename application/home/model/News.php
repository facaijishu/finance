<?php
namespace app\home\model;

class News extends BaseModel
{
	public function getNewsInfoById($id,$type=1){
        $result = $this->where(['id' => $id])->find();
        $result['news_date'] = substr($result['create_time'],0,10);
        if($type==1){
            $model   = model("dict");
            $list    = $model->getSecDictList($result['industry_pro']);
            $result['dict_list']     = $list;
        }else{
            $model  = model("dict");
            $list   = $model->getSecDictList($result['industry_org']);
            $result['dict_list']     = $list;
        }
        
        return $result;
    }
    
    /**
     * 获取列表
     * @param 页数 $page
     * @return number[]|string[]|NULL[]|unknown[]
     */
    public function getList($length,$type=1){
        $return  = [];
        $year    = date( "Y",time())-1;
        $month   = date( "M",time());
        $day     = date( "d",time());
        $time    = strtotime( $year."-"."$month"."-".$day." 00:00:00");
        $list    = $this->alias('n')
                        ->where(' n.status = 1 and n.create_time > '.$time)
                        ->field("SQL_CALC_FOUND_ROWS n.*")
                        ->order(" n.is_hot desc ,n.id desc ")
                        ->limit($length)->select();
        $result = $this->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        foreach ($list as $key => $value) {
            if($type==1){
                $model      = model("dict");
                $dictList   = $model->getTopDictList($value['industry_pro']);
                $list[$key]['dict_list']    = $dictList;
            }else{
                $model      = model("dict");
                $dictList   = $model->getTopDictList($value['industry_org']);
                $list[$key]['dict_list']    = $dictList;
            }
            $model   = model("MaterialLibrary");
            $top_img = $model->getMaterialInfoById($value['top_img']);
            $list[$key]['top_img']          = $top_img['url'];
            $list[$key]['top_img_compress'] = $top_img['url_compress'];
            
            $list[$key]['short_des']        = subStrLen($value['des'],50);
            $list[$key]['item_url']         = "/home/news/index/id/".$value['id'];
            $list[$key]['news_date']        = substr($value['create_time'],0,10);
        }
        if(empty($list)){
            $return['code']            = 201;
            $return['msg']             = "获取失败";
            $return['data']            = "";
        }else{
            
            $return['code']            = 200;
            $return['msg']             = "";
            $return['data']            = $list;
        }
       
        return $this->result($return['data'],$return['code'],$return['msg']);
       
    }
    
    public function getListApi($length = 0){
        $year    = date( "Y",time())-1;
        $month   = date( "M",time());
        $day     = date( "d",time());
        $time    = strtotime( $year."-"."$month"."-".$day." 00:00:00");
        if($length === 0){
            $result  = $this->alias('n')
                            ->where(['n.status' => 1])
                            ->order("is_hot desc,id desc")
                            ->select();
        } else {
            $result  = $this->alias('n')
                            ->where(' n.status = 1 and n.create_time > '.$time)
                            ->order("is_hot desc,id desc")
                            ->limit($length)
                            ->select();
        }
        $model = model("MaterialLibrary");
        foreach ($result as $key => $value) {
            $top_img = $model->getMaterialInfoById($value['top_img']);
            $result[$key]['top_img']          = $top_img['url'];
            $result[$key]['top_img_compress'] = $top_img['url_compress'];
            $result[$key]['release_date']     = substr($value['create_time'],0,10);
            $result[$key]['source']           = "FA財";
        }
        return $result;
    }
    
    
}