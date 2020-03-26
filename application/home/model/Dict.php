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
    
    /**
     * 获取标签一级集合
     * @param string 标签二级字符串以逗号分割
     * @return string|unknown
     */
    public function getTopDictList($id = '')
    {
        $sql   = "SELECT  id,`value` as dict_name from fic_dict where id in ( SELECT distinct  fid  from fic_dict where id in (". $id .")) ORDER BY list_order desc limit 2 ";
        $list  = $this->query($sql);
        return $list;
    }
    
    /**
     * 获取标签二级集合
     * @param string 行业二级字符串以逗号分割
     * @return string|unknown
     */
    public function getSecDictList($id = '')
    {
        $sql   = "SELECT  id,`value` as dict_name from fic_dict where id in  (". $id .")";
        $list  = $this->query($sql);
        
        return $list;
    }
    
    /**
     * 获取标签名称字符串
     * @param string $id
     * @return string|unknown
     */
    public function getDictStr($id = '',$length=0)
    {
        if($length>0){
            $result  = $this->alias("d")
                            ->field('d.id,d.value')
                            ->where(" id in (" .$id." ) ")
                            ->order("list_order desc")
                            ->limit(0, $length)
                            ->select();
        }else{
            $result  = $this->alias("d")
                            ->field('d.id,d.value')
                            ->where(" id in (" .$id." ) ")
                            ->order("list_order desc")
                            ->select();
        }
        
        $str = '';
        foreach ($result as $key => $item) {
            if($str==''){
                $str  = $item['value'];
            }else{
                $str  = $str."、".$item['value'];
            }
        }
        return $str;
    }
    
    
    //获取相似行业二级标签
    public function getMoreDictApi($content)
    {
        
        $arr2    = array();
        $arr3    = array();
        $sql     = "SELECT d.id,d.`value` FROM fic_dict d  LEFT JOIN fic_dict_type dt on  d.dt_id = dt.dt_id where d.fid >0 and d.`status` =  1 and dt.sign = 'industry' and  d.`value`!='其他' ";
        
        if($content!=""){
            $arr2 = zh_str_split($content,2);
            $arr3 = zh_str_split($content,3);
            $len2 = count($arr2);
            $len3 = count($arr3);
            if($len2>0){
                $sql .= "and (";
                for($i=0;$i<count($arr2);$i++){
                    if($i==0){
                        $sql .= " d.`value` like '%".$arr2[$i]."%' ";
                    }else{
                        $sql .= " or d.`value` like '%".$arr2[$i]."%' ";
                    } 
                }
                
                if($len3>0){
                    for($j=0;$i<count($arr3);$j++){
                        if($j==0){
                            $sql .= " d.`value` like '%".$arr2[$j]."%' ";
                        }else{
                            $sql .= " or d.`value` like '%".$arr2[$j]."%' ";
                        }
                    }
                }
                
                $sql .= ") ";
            }
        }
        $sql .= " limit 18 ";
        $list  = $this->query($sql);
        if(count($list)>0){
            $code  = 200;
            $msg = '';
        }else{
            $code  = 211;
            $msg = '';
        }
        
        return $this->result($list,$code,$msg);
    }
    
    //获取所属行业一级和二级标签
    public function getIndustry()
    {
        $dict    = $this->alias('d')
                        ->join('DictType dt','d.dt_id=dt.dt_id')
                        ->field('d.id,d.fid,d.value')
                        ->where(['d.fid'=>0,'d.status'=>1,'dt.sign'=>'industry'])
                        ->order(['list_order'=>'desc'])
                        ->select();
        //二级
        foreach ($dict as $key => $value) {
            $dict2   = $this->alias('d')
                            ->join('DictType dt','d.dt_id=dt.dt_id')
                            ->field('d.id,d.fid,d.value')
                            ->where(['d.fid'=>$value['id'],'d.status'=>1,'dt.sign'=>'industry'])
                            ->order(['list_order'=>'desc'])
                            ->select();
            if (!empty($dict2)) {
                $dict[$key]['sub']    = $dict2;
                $dict[$key]['idname'] = "inc_".$value['id'];
                $dict[$key]['allname']= "all".$value['id'];
            }
        }
        return $dict;
    }
    
    //获取投资阶段
    public function getStage()
    {
        $dict    = $this->alias('d')
                        ->join('DictType dt','d.dt_id=dt.dt_id')
                        ->field('d.id,d.value')
                        ->where(['d.status'=>1,'dt.sign'=>'invest_stage'])
                        ->order(['list_order'=>'desc'])
                        ->select();
        return $dict;
    }
    
    //获取所在区域的标签
    public function getArea()
    {
        $dict    = $this->alias('d')
                        ->join('DictType dt','d.dt_id=dt.dt_id')
                        ->field('d.id,d.value')
                        ->where(['d.status'=>1,'dt.sign'=>'to_area'])
                        ->order(['list_order'=>'desc'])
                        ->select();
        
        return $dict;
    }
    
    //获取业务类型
    public function getType()
    {
        $dict    = $this->alias('d')
                        ->join('DictType dt','d.dt_id=dt.dt_id')
                        ->field('d.id,d.value')
                        ->where(['d.status'=>1,'dt.sign'=>'business_type'])
                        ->order(['list_order'=>'desc'])
                        ->select();
        
        return $dict;
    }
    
    
}