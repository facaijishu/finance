<?php

namespace app\home\model;

class DictLabel extends BaseModel
{  
   protected $resultSetType = 'collection';
   //获取匹配的标签
   public function getDictLabelApi($uid=0)
   {
   	   if(!$uid){
   	   	   $this->error = '用户uid参数未传入';
   	   	   return false;
   	   }
   	   $dl = $this->field("dict_id,dict_fid,dict_type_id")
                  ->where(['uid'=>$uid,'is_del'=>2])
                  ->select()
                  ->toArray();
      
        return $dl;

   }	
   //添加初次匹配的标签
   public function addDictApi($uid = 0,$dict_id = '')
   {
       if(!$uid || !is_numeric($uid)){
           $this->code = 208;
           $this->msg = '缺少用户id参数';
           return $this->result('',$this->code,$this->msg);
       }
       
       if (!empty($dict_id)) {
              $re = $this->where(['uid'=>$uid,'is_del'=>2])->find();
               if($re){
                  $this->code = 251;
                  $this->msg = '不可重复添加初次匹配标签';
                  return $this->result('',$this->code,$this->msg);
               }
               $map['id'] = array('in',$dict_id);
               $dict = model('Dict')->field('id,dt_id,fid')
                                    ->where($map)
                                    ->where(['status'=>1])
                                    ->select()
                                    ->toArray();
                if (empty($dict)) {
                     $this->code = 216;
                     $this->msg = '数据字典中标签不存在';
                     return $this->result('',$this->code,$this->msg);
                }
                
                $dict_label = [];
                foreach ($dict as $v) {
                     $dict_label[] = [
                                        'uid'=>$uid,
                                        'dict_id'=>$v['id'],
                                        'dict_fid'=>$v['fid'],
                                        'dict_type_id'=>$v['dt_id'],
                                        'create_time'=>time()
                                     ];    
                }

                 
                $this->startTrans();
                try {
                    $this->allowField(true)->saveAll($dict_label);
                    //member表标记是否添加初次匹配标签的状态
                    model('Member')->where(['uid'=>$uid])->update(['is_first_match'=>1]);
                    $member = model('Member')->where(['uid'=>$uid])->find();
                    session('FINANCE_USER',$member);
                    $this->commit();
                    $this->code = 200;
                    $this->msg = '添加初次匹配标签成功';
                    return $this->result('',$this->code,$this->msg);
                } catch (\Exception $e) {
                    $this->code = 250;
                    $this->msg = '添加初次匹配标签失败 '.$e->getMessage();
                    $this->rollback();
                    return $this->result('',$this->code,$this->msg);
                }
       }else{
           $this->code = 200;
           $this->msg = '添加初次匹配标签成功';
           return $this->result('',$this->code,$this->msg); 
       }
       

    }    
    
    //精准匹配
    public function exactMatchDictApi($uid=0,$dict_id='')
    {
        if(!$uid || !is_numeric($uid)){
           $this->code = 208;
           $this->msg = '缺少用户id参数';
           return $this->result('',$this->code,$this->msg);
       }
        $re = $this->field('id,dict_id')
                  ->where(['uid'=>$uid,'is_del'=>2])
                  ->select()
                  ->toArray();
       if (!empty($dict_id)) {
          
           //没有初次匹配,直接添加精准匹配
           if (empty($re)) {
               $map['id'] = array('in',$dict_id);
               $dict = model('Dict')->field('id,dt_id,fid')
                                ->where($map)
                                ->where(['status'=>1])
                                ->select()
                                ->toArray();
                if (empty($dict)) {
                     $this->code = 216;
                     $this->msg = '标签不存在';
                     return $this->result('',$this->code,$this->msg);
                }
                
                $dict_label = [];
                foreach ($dict as $v) {
                     $dict_label[] = [
                                        'uid'=>$uid,
                                        'dict_id'=>$v['id'],
                                        'dict_fid'=>$v['fid'],
                                        'dict_type_id'=>$v['dt_id'],
                                        'create_time'=>time()
                                     ];    
                }

                try {
                    $this->allowField(true)->saveAll($dict_label);
                    $this->code = 200;
                    $this->msg = '精准匹配标签成功';
                    return $this->result('',$this->code,$this->msg);
                } catch (\Exception $e) {
                    $this->code = 254;
                    $this->msg = '精准匹配标签失败 '.$e->getMessage();
             
                    return $this->result('',$this->code,$this->msg);
                }

            }

            //上次匹配过,再次精准匹配
            //(1)接收重新选中的标签id
            //(2)判断重新选中的标签id与原来用户上次选中的是否有交集
            //(3)如果有交集,从重新选中的标签id集合中剔除交集部分的id,剩下的重新选中的标签(重新选中的标签和得到的交集的差集)insert
            //(4)如果有交集,从上次选中的标签id集合中剔除交集部分的id,剩下的为用户已删除(上次选中的标签和得到的交集的差集)update的标签
            //(5)如果没有交集,重新选中的标签id集合全部insert,上次选中的标签id集合全部删除(update)
            //获取原来用户上次选中的标签
             $dict_old_id_arr = array_column($re, 'dict_id');       
            //用户重新提交的标签
             $dict_new_id_arr = [];
             if (strpos($dict_id,',') !== false) {
                 //提交多个标签
                 $dict_new_id_arr = explode(',', $dict_id);                          
             } else {
                 //提交单个标签
                 $dict_new_id_arr = [$dict_id];   
             }
             //求交集
             $dict_same_id_arr = array_intersect($dict_new_id_arr, $dict_old_id_arr);
             if (!empty($dict_same_id_arr)) {
                  //insert
                  $dict_new_diff_id_arr = array_diff($dict_new_id_arr, $dict_same_id_arr);
                  //update
                  $dict_old_diff_id_arr = array_diff($dict_old_id_arr, $dict_same_id_arr);
                   $this->startTrans();
                   try {
                      if (!empty($dict_new_diff_id_arr)) {
                           //insert
                           $dict_new_diff_id_str = implode(',', $dict_new_diff_id_arr);
                           $map['id'] = array('in',$dict_new_diff_id_str);
                           $dict = model('Dict')->field('id,dt_id,fid')
                                            ->where($map)
                                            ->where(['status'=>1])
                                            ->select()
                                            ->toArray();
                            if (empty($dict)) {
                                 $this->code = 216;
                                 $this->msg = '标签不存在';
                                 return $this->result('',$this->code,$this->msg);
                            }
                            
                            $dict_label = [];
                            foreach ($dict as $v) {
                                 $dict_label[] = [
                                                    'uid'=>$uid,
                                                    'dict_id'=>$v['id'],
                                                    'dict_fid'=>$v['fid'],
                                                    'dict_type_id'=>$v['dt_id'],
                                                    'create_time'=>time()
                                                 ];    
                            }

                            $this->allowField(true)->saveAll($dict_label);
                      
                       
                        }

                        if (!empty($dict_old_diff_id_arr)) {
                           //update
                           $dict_old_diff_id_arr = implode(',', $dict_old_diff_id_arr);
                           $map['dict_id'] = array('in',$dict_old_diff_id_arr);
                           $this->where($map)->update(['is_del'=>1]);
                            
                        }
                        $this->commit();
                        $this->code = 200;
                        $this->msg = '精准匹配标签成功';
                        return $this->result('',$this->code,$this->msg);
                   
                   } catch (\Exception $e) {
                        $this->rollback();
                        $this->code = 254;
                        $this->msg = $e->getMessage();
                        return $this->result('',$this->code,$this->msg);                
                   }            
                                                                                       
             } 
                
             //没有交集
             $this->startTrans();
             try {
                //insert
               $map['id'] = array('in',$dict_id);
               $dict = model('Dict')->field('id,dt_id,fid')
                                ->where($map)
                                ->where(['status'=>1])
                                ->select()
                                ->toArray();
                if (empty($dict)) {
                     $this->code = 216;
                     $this->msg = '标签不存在';
                     return $this->result('',$this->code,$this->msg);
                }
                $dict_label = [];
                foreach ($dict as $v) {
                     $dict_label[] = [
                                        'uid'=>$uid,
                                        'dict_id'=>$v['id'],
                                        'dict_fid'=>$v['fid'],
                                        'dict_type_id'=>$v['dt_id'],
                                        'create_time'=>time()
                                     ];    
                }

                $this->allowField(true)->saveAll($dict_label);
                //update
                $map2['dict_id'] = implode(',', $dict_old_id_arr);
                $this->where($map2)->update(['is_del'=>1]);
                $this->commit();
                $this->code = 200;
                $this->msg = '精准匹配标签成功';
                return $this->result('',$this->code,$this->msg);

             } catch (\Exception $e) {
                $this->rollback();
                $this->code = 254;
                $this->msg = '精确匹配标签失败';
                return $this->result('',$this->code,$this->msg);   
             }   
       }else{
         //没有选任何标签的情况下
         //判断之前是否选过标签
         if (empty($re)) {
             //如果没选,直接返回200
            $this->code = 200;
            $this->msg = '精准匹配标签成功';
            return $this->result('',$this->code,$this->msg);
         } 
            //如果选过,批量删除当前登录用户的所有标签
            $id_str = implode(',',array_column($re, 'id'));
            $map['id'] = array('in',$id_str);
            $this->where($map)->update(['is_del'=>1]);
            $this->code = 200;
            $this->msg = '精准匹配标签成功';
            return $this->result('',$this->code,$this->msg);  
       }
                                                                                                               
    }
   
    //我的人脉
    public function showMyConnectionsApi($uid = 0,$page = 1,$num = 4)
    {
        if (empty($uid)) {
          $this->code = 208;
          $this->msg = '缺少当前访问用户参数';
          return $this->result('',$this->code,$this->msg);
        }
        $offset = (intval($page) - 1) * intval($num);
        //查询当前登录用户是否设置偏好的业务,行业标签
        
        $dict_fid = $this->alias('dl')
                        ->join('DictType dt','dl.dict_type_id=dt.dt_id')
                        ->field('dict_fid')
                        ->where(['dl.uid'=>$uid,'dl.is_del'=>2])
                        ->where("dt.sign='service_type' or dt.sign='industry'")
                        ->group('dict_fid')
                        ->select()
                        ->toArray();
        if (empty($dict_fid)) {
            //用户没有设置偏好的业务,行业标签时,提示先去设置[偏好]
            $this->code = 260;
            $this->msg = '选择您的偏好，为您匹配精准人脉';
            return $this->result('',$this->code,$this->msg);    
        }
        //当前登录用户设置偏好的业务,行业标签时,根据业务,行业标签id查询非当前的满足条件的用户uid
        $dict_fid_column = implode(',',array_column($dict_fid,'dict_fid'));
        $map['dl.dict_fid'] = array('in',$dict_fid_column);
        $my_connections = $this->Distinct(true)
                             ->alias('dl')
                             ->join('Member m','dl.uid=m.uid')
                             ->field('dl.uid,m.userPhoto,m.realName,m.company_jc,m.position')
                             ->where("dl.uid <> $uid")
                             ->where($map)
                             ->limit($offset,$num)
                             ->select()
                             ->toArray();
        if (empty($my_connections)) {
            //用户设置了偏好的业务,行业标签时,未匹配到对应人脉
            $this->code = 261;
            $this->msg = '找不到想要的？去发布您的需求吧';
            return $this->result('',$this->code,$this->msg);
        }

        //判断关注情况(已关注,关注)
        //查询当前登录的用户关注的uid
        $fu = model('FollowUser');
        $my_follow_user = $fu->field('to_uid')
                           ->where(['from_uid'=>$uid,'is_del'=>2])
                           ->select()
                           ->toArray();
        if (empty($my_follow_user)) {
            //当前登录用户未关注任何人时
           foreach ($my_connections as &$each_my_connections) {
             $each_my_connections['is_follow'] = '关注';
           }

        }else{
           $my_follow_user_column = array_column($my_follow_user, 'to_uid');
           foreach ($my_connections as &$each_my_connections) {
             if (in_array($each_my_connections['uid'],$my_follow_user_column)) {
                $each_my_connections['is_follow'] = '已关注';     
             }else{
                $each_my_connections['is_follow'] = '关注';
             }
           }    
        }
        
        return $this->result($my_connections);
        
    }
    
    //展示用户选的精准匹配标签
    public function showMatchDictApi($uid = 0)
    {   
        //所有类型标签
        $all_dict = [];
        //业务
        $all_dict['service_type']   = [];
        //行业
        $all_dict['industry']       = [];
        //投融规模
        $all_dict['size']           = [];
        //省份
        $all_dict['to_province']    = [];
        //查询二级业务
        $service_type = $this->alias('dl')
                             ->join('DictType dt','dl.dict_type_id=dt.dt_id')
                             ->where(['dt.sign'=>'service_type','dl.is_del'=>2,'dl.uid'=>$uid])
                             ->where('dl.dict_fid>0')
                             ->field('dl.dict_id')
                             ->select()
                             ->toArray();
        if (!empty($service_type)) {
            $service_type_arr = array_column($service_type,'dict_id');
            $all_dict['service_type'] = $service_type_arr;
        }
        //查询二级行业
        $industry = $this->alias('dl')
                             ->join('DictType dt','dl.dict_type_id=dt.dt_id')
                             ->where(['dt.sign'=>'industry','dl.is_del'=>2,'dl.uid'=>$uid])
                             ->where('dl.dict_fid>0')
                             ->field('dl.dict_id')
                             ->select()
                             ->toArray();
        if (!empty($industry)) {
            $industry_arr = array_column($industry,'dict_id');
            $all_dict['industry'] = $industry;
        }
        //查询投融规模
        $size = $this->alias('dl')
                             ->join('DictType dt','dl.dict_type_id=dt.dt_id')
                             ->where(['dt.sign'=>'size','dl.is_del'=>2,'dl.uid'=>$uid])
                             ->field('dl.dict_id')
                             ->select()
                             ->toArray();
        if (!empty($size)) {
            $size_arr = array_column($size,'dict_id');
            $all_dict['size'] = $size;
        }
        //查询省份
        $to_province = $this->alias('dl')
                     ->join('DictType dt','dl.dict_type_id=dt.dt_id')
                     ->where(['dt.sign'=>'to_province','dl.is_del'=>2,'dl.uid'=>$uid])
                     ->field('dl.dict_id')
                     ->select()
                     ->toArray();
        if (!empty($to_province)) {
            $size_arr = array_column($to_province,'dict_id');
            $all_dict['to_province'] = $to_province;
        }
        return $this->result($all_dict);
    }
    
    
}
