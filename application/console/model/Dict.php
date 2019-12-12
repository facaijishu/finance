<?php

namespace app\console\model;

use think\Model;

class Dict extends Model
{
    public function getDictInfoById($id){
        if(empty($id)){
            $this->error = '该数据id不存在';
            return false;
        }
        $info = $this->where(['id' => $id])->find();
        if(!empty($info)){
            if($info['fid'] > 0 && $info['dt_id'] == 1){
                $info['parentIndustry'] = $this->getParentIndustry($info['dt_id']);
            }
        }
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
                ->select();
        return $result;
    }
    
    //获取一级行业
    public function getTopIndustry()
    {
        $result  = $this->alias("d")
                        ->join("dict_type dt" , "d.dt_id=dt.dt_id")
                        ->where(['dt.sign' => 'industry','d.status' => 1,'d.fid'=>0])
                        ->order('d.list_order desc')
                        ->select();
        return $result;
    }
    
    //获取二级行业
    public function getIndustry($id = 0)
    {
        $result  = $this->alias("d")
                        ->join("dict_type dt" , "d.dt_id=dt.dt_id")
                        ->where(['dt.sign' => 'industry','d.status' => 1,'d.fid'=>$id])
                        ->order('d.list_order desc')
                        ->select();
        return $result;
    }
    
    //获取投资阶段
    public function getStage()
    {
        $result  = $this->alias("d")
                        ->join("dict_type dt" , "d.dt_id=dt.dt_id")
                        ->where(['dt.sign' => 'invest_stage','d.status' => 1,'d.fid'=>0])
                        ->order('d.list_order desc')
                        ->select();
        return $result;
    }
    
    //获取区域
    public function getArea()
    {
        $result  = $this->alias("d")
                        ->join("dict_type dt" , "d.dt_id=dt.dt_id")
                        ->where(['dt.sign' => 'to_area','d.status' => 1,'d.fid'=>0])
                        ->order('d.list_order desc')
                        ->select();
        return $result;
    }
    
    //获取所属行业一级和二级标签
    public function getSubDict($fid)
    {
            
        $dict   = $this->alias('d')
                        ->join('DictType dt','d.dt_id=dt.dt_id')
                        ->field('d.id,d.fid,d.value')
                        ->where(['d.fid'=>$fid,'d.status'=>1])
                        ->order(['list_order'=>'desc'])
                        ->select();
           
        
        return $dict;
    }
    
    
    //获取一级业务
    public function getTopService()
    {
        $result = $this->alias("d")
        ->join("dict_type dt" , "d.dt_id=dt.dt_id")
        ->where(['dt.sign' => 'service_type','d.status' => 1,'d.fid'=>0])
        ->order('d.list_order asc')
        ->select();
        return $result;
    }
    
    //获取二级业务
    public function getService($id = 0)
    {
        $result = $this->alias("d")
        ->join("dict_type dt" , "d.dt_id=dt.dt_id")
        ->where(['dt.sign' => 'service_type','d.status' => 1,'d.fid'=>$id])
        ->order('d.list_order asc')
        ->select();
        return $result;
    }
    
    
    public function createDict($data){
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('Dict');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }

        //开启事务
        $this->startTrans();
        try{
            if(isset($data['id'])){
                if(empty($data['id'])){
                    $this->error = '该数据id错误';
                    return false;
                }
                $arr = [];
                if(isset($data['firstType2'])){
                   $arr['fid'] = $data['firstType2'];
                }
                $arr['dt_id'] = $data['type'];
                $arr['value'] = $data['value'];
                $arr['des'] = $data['des'];
                $arr['list_order'] = $data['list_order'];
                $this->where(['id' => $data['id']])->update($arr);
            } else {
                $info = \app\console\service\User::getInstance()->getInfo();
                $arr = [];
                if(isset($data['firstType'])){
                   $arr['fid'] = $data['firstType'];
                }
                $arr['dt_id'] = $data['type'];
                $arr['value'] = $data['value'];
                $arr['des'] = $data['des'];
                $arr['list_order'] = $data['list_order'];
                $arr['create_time'] = time();
                $arr['create_uid'] = $info['uid'];
                $arr['status'] = 1;
                $this->allowField(true)->save($arr);
            }
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function stopDict($id){
        if(empty($id)){
            $this->error = '该数据id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            //停用的标签是所属行业或者业务类型的一级标签时,停用附带的二级标签,再停用一级标签;
            $dict = $this->where(['id' => $id])->find();
            if($dict['dt_id'] == 1 || $dict['dt_id'] == 25){
                if($dict['fid'] == 0){
                    $this->where(['fid'=>$id])->update(['status' => -1]);
                }
            }
            $this->where(['id' => $id])->update(['status' => -1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function startDict($id){
        if(empty($id)){
            $this->error = '该数据id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            //启用的标签是所属行业或者业务类型的一级标签时,启用附带的二级标签,再启用一级标签;
            $dict = $this->where(['id' => $id])->find();
            if($dict['dt_id'] == 1 || $dict['dt_id'] == 25){
                if($dict['fid'] == 0){
                   $this->where(['fid'=>$id])->update(['status' => 1]);
                }
                if($dict['fid'] > 0){
                   $father = $this->where(['id'=>$dict['fid']])->find();
                   if($father['status'] == -1){
                     return false;
                   }
                }
            }

            $this->where(['id' => $id])->update(['status' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    public function getParentIndustry($dt_id=0)
    {
       if(!$dt_id){
         $this->error = '数据归属不存在';
         return false;
       }
       $re = $this->field('id,dt_id,fid,value,status')->where(['dt_id'=>$dt_id,'fid'=>0,'status'=>1])->select(); 
       return $re;

    }
   public function getSingleDict($value='',$dt_id=0)
   {
     if(!$value || !$dt_id){
        $this->error = '数据错误';
        return false;
     }
     $re = $this->where(['dt_id'=>$dt_id,'value'=>$value,'status'=>1])->find();
     return $re;
   }
   
   //获取业务类型的一级标签
   public function getParentServiceType($dt_id=0)
   {
     if(!$dt_id){
         $this->error = '数据归属不存在';
         return false;
       }
       $re = $this->field('id,dt_id,fid,value,status')->where(['dt_id'=>$dt_id,'fid'=>0,'status'=>1])->select(); 
       return $re;
   }
   
   //获取业务类型一级和二级(业务细分)的数据集
   public function getServiceTypeDict()
   {
       //一级
       $dict = $this->alias('d')
                    ->join('DictType dt','d.dt_id=dt.dt_id')
                    ->field('d.id,d.fid,d.value')
                    ->where(['d.fid'=>0,'d.status'=>1,'dt.sign'=>'service_type'])
                    ->order(['list_order'=>'desc'])
                    ->select();
       if(empty($dict)){
           $this->error = '业务细分不存在,请先往后台添加或启用';
           return false;
       }
       
       //二级
       foreach ($dict as $key => $value) {
           $dict2 = $this->alias('d')
                         ->join('DictType dt','d.dt_id=dt.dt_id')
                         ->field('d.id,d.fid,d.value')
                         ->where(['d.fid'=>$value['id'],'d.status'=>1,'dt.sign'=>'service_type'])
                         ->order(['list_order'=>'desc'])
                         ->select();
           if (!empty($dict2)) {
               $dict[$key]['sub'] = $dict2;
           }
       }
       return $dict;
   }
   
   //获取所属行业一级和二级（行业细分）的数据集
   public function getIndustryDict()
   {
       $dict = $this->alias('d')
                     ->join('DictType dt','d.dt_id=dt.dt_id')
                     ->field('d.id,d.fid,d.value')
                     ->where(['d.fid'=>0,'d.status'=>1,'dt.sign'=>'industry'])
                     ->order(['list_order'=>'desc'])
                     ->select();
       if(empty($dict)){
           $this->error = '行业标签不存在,请先往后台添加或启用';
           return false;
       }
       
       //二级
       foreach ($dict as $key => $value) {
           
           $dict2 = $this->alias('d')
                         ->join('DictType dt','d.dt_id=dt.dt_id')
                         ->field('d.id,d.fid,d.value')
                         ->where(['d.fid'=>$value['id'],'d.status'=>1,'dt.sign'=>'industry'])
                         ->order(['list_order'=>'desc'])
                         ->select();
           if (!empty($dict2)) {
               $dict[$key]['sub'] = $dict2;
           }
       }
       
       return $dict;
   }
   
   //获取投融规模的数据集
   public function getSizeDict()
   {
       $dict = $this->alias('d')
                    ->join('DictType dt','d.dt_id=dt.dt_id')
                    ->field('d.id,d.value')
                    ->where(['d.status'=>1,'dt.sign'=>'size'])
                    ->order(['list_order'=>'desc'])
                    ->select();
       if(empty($dict)){
           $this->error = '投融规模标签不存在,请先往后台添加或启用';
           return false;
       }
       return $dict;
   }
   
  
   public function getidList($id = '')
   {
       $result  = $this->alias("d")
                       ->field('d.id,d.value')
                       ->where(" id in (" .$id." ) ")
                       ->select();
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
   
   
   
   /**
    * 获取标签二级集合
    * @param string 行业二级字符串以逗号分割
    * @return string|unknown
    */
   public function getDictList($id = '')
   {
       $result      = $this->alias("d")
                           ->field('d.id, d.fid, d.value as dict_name')
                           ->where(" id = " .$id)
                           ->find();
       if($result['dict_name']=="其他"){
           $dict  = $this->getInfo($result['fid']);
           $result['dict_name'] = "其他(".$dict['value'].")";
       }
       return $result;
   }
   
   //获取分类名称
   public function getInfo($id)
   {
       $dict    = $this->alias('d')->field('d.id,d.value')->where(['d.status'=>1,'d.id'=>$id])->find();
       return $dict;
   }

}