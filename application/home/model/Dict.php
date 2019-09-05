<?php

namespace app\home\model;

// use think\Model;

class Dict extends BaseModel
{
    public function getDictInfoById($id){
        if(empty($id)){
            $this->error = '该数据id不存在';
            return false;
        }
        $info = $this->where(['id' => $id])->find();
        return $info;
    }
    public function getDictInfoByIdSet($str)
    {   $info = [];
        if(empty($str)){
            $this->error = '该数据id集合不存在';
            return false;
        }
        $where['id'] = array('in',$str);
        $info[] = $this->where($where)->select();
        return $info;
    }
    public function getDictByType($str = ''){
        if($str == ''){
            $this->error = '该数据类型名称不存在';
            return false;;
        }
        $result = $this->alias("d")
                ->join("dict_type dt" , "d.dt_id=dt.dt_id")
                ->where(['dt.sign' => $str,'d.status'=>1])
                ->order('d.list_order asc')
                ->select()
                ->toArray();
        return $result;
    }

    /**
     * 4.0api
     */
    //根据前端传递的industry,返回一级和二级industry
    public function getIndustryRelation($industry_id=0)
    {   
        
        $industry = $this->field(['id','fid'])->where(['id'=>$industry_id])->find();
        if ($industry['fid']>0) {
            $data['top_industry_id'] = $industry['fid'];
            $data['industry_id'] =  $industry['id'];
            
        } else {
            $data['top_industry_id'] = $industry['id'];
            $data['industry_id'] = 0;
        }
        return $data;
        
    }
    
    //获取业务类型一级和二级标签
    public function getServiceTypeDictApi()
    {   
        //一级
        $dict = $this->alias('d')
                     ->join('DictType dt','d.dt_id=dt.dt_id')
                     ->field('d.id,d.fid,d.value')
                     ->where(['d.fid'=>0,'d.status'=>1,'dt.sign'=>'service_type'])
                     ->order(['list_order'=>'desc'])
                     ->select()
                     ->toArray();
        if(empty($dict)){
            $this->code = 215;
            $this->msg = '业务标签不存在,请先往后台添加或启用';
            return $this->result('',$this->code,$this->msg);
        }
        
        //二级
        // $dict = getTree($dict);
        foreach ($dict as $key => $value) {
            
            $dict2 = $this->alias('d')
                     ->join('DictType dt','d.dt_id=dt.dt_id')
                     ->field('d.id,d.fid,d.value')
                     ->where(['d.fid'=>$value['id'],'d.status'=>1,'dt.sign'=>'service_type'])
                     ->order(['list_order'=>'desc'])
                     ->select()
                     ->toArray();
            if (!empty($dict2)) {
                $dict[$key]['sub'] = $dict2;
            }
            $dict[$key]['idname']  = "type".$value['id'];
            $dict[$key]['allname'] = "all".$value['id'];
            $dict[$key]['disname'] = "dis".$value['id'];
        }
        return $this->result($dict);
    }
    
    //获取所属行业一级和二级标签
    public function getIndustryDictApi()
    {
        $dict = $this->alias('d')
                     ->join('DictType dt','d.dt_id=dt.dt_id')
                     ->field('d.id,d.fid,d.value')
                     ->where(['d.fid'=>0,'d.status'=>1,'dt.sign'=>'industry'])
                     ->order(['list_order'=>'desc'])
                     ->select()
                     ->toArray();
        if(empty($dict)){
            $this->code = 238;
            $this->msg = '行业标签不存在,请先往后台添加或启用';
            return $this->result('',$this->code,$this->msg);
        }
         //二级
        // $dict = getTree($dict);
        foreach ($dict as $key => $value) {
            
            $dict2 = $this->alias('d')
                     ->join('DictType dt','d.dt_id=dt.dt_id')
                     ->field('d.id,d.fid,d.value')
                     ->where(['d.fid'=>$value['id'],'d.status'=>1,'dt.sign'=>'industry'])
                     ->order(['list_order'=>'desc'])
                     ->select()
                     ->toArray();
            if (!empty($dict2)) {
                $dict[$key]['sub'] = $dict2;
            }
            $dict[$key]['idname']  = "type".$value['id'];
            $dict[$key]['allname'] = "all".$value['id'];
            $dict[$key]['disname'] = "dis".$value['id'];
        }
        return $this->result($dict);
    }
    //获取投融规模的标签
    public function getSizeDictApi()
    {
         $dict = $this->alias('d')
                     ->join('DictType dt','d.dt_id=dt.dt_id')
                     ->field('d.id,d.value')
                     ->where(['d.status'=>1,'dt.sign'=>'size'])
                     ->order(['list_order'=>'desc'])
                     ->select()
                     ->toArray();
        if(empty($dict)){
            $this->code = 239;
            $this->msg = '投融规模标签不存在,请先往后台添加或启用';
            return $this->result('',$this->code,$this->msg);
        }
        return $this->result($dict);
    }
    //获取所在省份的标签
    public function getToProvinceDictApi()
    {
       $dict = $this->alias('d')
                     ->join('DictType dt','d.dt_id=dt.dt_id')
                     ->field('d.id,d.value')
                     ->where(['d.status'=>1,'dt.sign'=>'to_province'])
                     ->order(['list_order'=>'desc'])
                     ->select()
                     ->toArray();
        if(empty($dict)){
            $this->code = 240;
            $this->msg = '所在省份标签不存在,请先往后台添加或启用';
            return $this->result('',$this->code,$this->msg);
        }
        return $this->result($dict); 
    }
    public function getDictValueById($id = 0)
    {
        if(!$id){
            $this->code = 206;
            $this->msg = '缺少标签id参数';
            return $this->result('',$this->code,$this->msg);
        }
        $value = $this->field('value')->where(['id'=>$id])->find();
        if(empty($value)){
            $this->code = 216;
            $this->msg = '标签不存在';
            return $this->result('',$this->code,$this->msg);
        }
        return $value['value'];
    }
    public function getDictDtidById($id = 0)
    {
        if(!$id){
           $this->code = 206;
           $this->msg = '缺少标签id参数';
           return $this->result('',$this->code,$this->msg);
        }
        $dt_id = $this->field('dt_id')->where(['id'=>$id])->find();
        if(empty($dt_id)){
            $this->code = 216;
            $this->msg = '数据类型不存在';
            return $this->result('',$this->code,$this->msg);
        }
        return $dt_id['dt_id'];
    }
}