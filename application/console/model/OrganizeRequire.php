<?php 
namespace app\console\model;
use think\Model;
class OrganizeRequire extends Model 
{   
    // protected $resultSetType = 'collection';
    //读取资金管理列表页数据
	public function readOrganizeRequire($start = 0,$length = 20,$or_id = '',$real_name = '',$is_show = '',$status = '')
	{    
		 $return = [];
         $this->alias('or')
              ->join('Member m','or.uid=m.uid')
              ->join('OrganizeRequireDict ord','or.or_id=ord.or_id')
              ->join('Dict d','ord.dict_id=d.id')
              ->join('DictType dt','ord.dict_type_id=dt.dt_id')
              ->field('SQL_CALC_FOUND_ROWS or.or_id,or.uid,m.realName,or.create_time,or.deadline,d.value as business_type,or.business_des,or.is_show,or.status,or.rank,or.is_top,or.is_care')
              ->where(['ord.dict_fid'=>0,'dt.sign'=>'service_type'])
              ->order('or.or_id desc');
         if ('' != $or_id) {
         	$this->where(['or.or_id' => $or_id]);
         }
         
         if ('' != $real_name) {
            $map['m.realName'] = array('like',"%$real_name%");
            $this->where($map);
         }
         //显示状态
         if ('' != $is_show) {
             $this->where(['or.is_show' => $is_show]);
         }
         
         //有效状态
         if ('' != $status) {
             $now = time();
             if($status==2){
                 $this->where("or.deadline < $now ");
             }else{
                 $this->where("or.deadline >= $now" );
             }
         }
         
         
         $this->where(['or.status' => 1]);
         
         $list = $this->limit($start,$length)->select();
         
         $result = $this->query('SELECT FOUND_ROWS() AS count');
         $total = $result[0]['count'];
         foreach ($list as $key => $item) {
             $list[$key]['deadline'] = date('Y-m-d H:i:s', $item['deadline']);
             $data[] = $item->toArray();
         }
         $return['data'] = $list;
         $return['recordsFiltered'] = $total;
         $return['recordsTotal'] = $total;
         return $return;
    }
    
    
    //导出资金管理数据到excel表
    public function excelOrganizeRequire($or_id = '',$real_name = '',$is_show = '',$status = '')
	{
        $this->alias('or')
             ->join('Member m','or.uid=m.uid')
             ->field('SQL_CALC_FOUND_ROWS or.or_id,or.uid,m.realName,or.name,or.fund_type,or.financing_period,or.invest_target,or.status,or.is_show,or.business_des,or.invest_scale,or.invest_mode,or.contact_status,or.inner_cs,or.create_time,or.deadline,or.validity_period')
             ->order('or_id desc');

        if (0 != $or_id) {
        $this->where(['or.or_id' => $or_id]);
        }
        
        if ('' != $real_name) {
            $map['m.realName'] = array('like',"%$real_name%");
            $this->where($map);
        }
        
        //显示状态
        if ('' != $is_show) {
            $this->where(['or.is_show' => $is_show]);
        }
        
        //有效状态
        if ('' != $status) {
            $now = time();
            if($status==2){
                $this->where("or.deadline < $now ");
            }else{
                $this->where("or.deadline >= $now" );
            }
        }
        
        
        $this->where(['or.status' => 1]);
        
        $list = $this->select();
        if (!empty($list)) {
            foreach ($list as &$value) {
            
                //所属业务
                //一级业务
                $top_dict = model('OrganizeRequireDict')->alias('ord')
                                                    ->join('Dict d','ord.dict_id=d.id')
                                                    ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                    ->field('d.id,d.value')
                                                    ->where([
                                                           'ord.or_id'=>$value['or_id'],
                                                           'dt.sign'=>'service_type'
                                                           ])
                                                           ->find();
                if (!empty($top_dict)) {
                    $value['service_type'] = $top_dict['value'];
                    //二级业务
                    $dict = model('OrganizeRequireDict')->alias('ord')
                                                        ->join('Dict d','ord.dict_id=d.id')
                                                        ->field('d.value')
                                                        ->where([
                                                               'ord.or_id'      => $value['or_id'],
                                                               'ord.dict_fid'   => $top_dict['id']
                                                               ])
                                                               ->select();
                    if (!empty($dict)) {
                        $value['service_type'].= ' - '.implode(',',array_column($dict, 'value'));
                    } 
                       
                } else {
                     $value['service_type'] = '';
                }
                //所属省份
                $province = db('OrganizeRequireDict')->alias('ord')
                                                     ->join('Dict d','ord.dict_id=d.id')
                                                     ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                     ->where(['ord.or_id'=>$value['or_id'],'ord.dict_fid'=>0,'d.status'=>1,'dt.sign'=>'to_province'])
                                                     ->field(['d.value'])
                                                     ->select();
                if (!empty($province)) {
                    $value['province'] = implode(',', array_column($province, 'value'));
                } else {
                    $value['province'] = '';
                }
                                                  
                //所属行业
                if ($top_dict['value'] !== '配资业务') {
                      //一级行业+二级行业
                      $industry_value= [];
                      
                      $top_industry = db('OrganizeRequireDict')->alias('ord')
                                                              ->join('Dict d','ord.dict_id=d.id')
                                                              ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                              ->where(['ord.or_id'=>$value['or_id'],'ord.dict_fid'=>0,'dt.sign'=>'industry'])
                                                              ->select();
                      if (!empty($top_industry)) {
                          foreach ($top_industry as &$v) {
                              $industry = db('OrganizeRequireDict')->alias('ord')
                              ->join('Dict d','ord.dict_id=d.id')
                              ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                              ->where(['ord.or_id'=>$value['or_id'],'ord.dict_fid'=>$v['dict_id'],'dt.sign'=>'industry'])
                              ->field(['d.value'])
                              ->select();
                              
                              if (!empty($industry)) {
                                  $industry_value[] = $v['value'].' - '.implode(',',array_column($industry, 'value'));
                              } else {
                                  $industry_value[] = $v['value'];
                              }
                          }
                      }
                      $industry_value = implode(',',$industry_value);
                      $value['industry'] = $industry_value;
                }else{
                    $value['industry'] = "";
                }
                        
                if($value['is_show']==1){
                    $value['is_show']            = "展示中";
                }elseif($value['is_show']==2){
                    $value['is_show']            = "已隐藏";
                }else{
                    $value['is_show']            = "已删除";
                }
                
                //资金类型
                $value['fund_type']         = getFundType($value['fund_type']);
                //投资期限
                $value['financing_period']  = getFinancingPeriod($value['financing_period']);
                //投资方式
                $value['invest_mode']       = getInvestMode($value['invest_mode']);
                //业务截止日期
                $value['deadline']          = deadline($value['create_time'],$value['validity_period']);
                //联系人身份
                $value['contact_status']    = contact_status($value['contact_status']);
                                                  
            }
            
        }
        
        return $list;
	}
	
	//隐藏一条资金需求
    public function stopOrganizeRequire($id = 0)
    {
        if(!$id){
            $this->error = '发布资金id不存在';
            return false;
        }
        $this->startTrans();
        try {
            $data['is_show'] = 2;
            $this->where(['or_id' => $id])->update($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return false;
        }

    }
    
    //展示一条资金需求
    public function startOrganizeRequire($id = 0)
    {
        if(!$id){
            $this->error = '发布资金id不存在';
            return false;
        }
        $this->startTrans();
        try {
            $data['is_show'] = 1;
            $this->where(['or_id' => $id])->update($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return false;
        }

    }
    
    //一条资金需求的详情
    public function organizeRequireDetail($id = 0)
    {
    	if(!$id){
            $this->error = '发布资金id不存在';
            return false;
         }
         $or = $this->where(['or_id' => $id])->find();
         $through = model('Through');
         $realName = $through->readThroughByUid($or['uid']);
         $or['realName'] = $realName;
         //一级业务
         $top_dict = db('OrganizeRequireDict')->alias('ord')
                                              ->join('Dict d','ord.dict_id=d.id')
                                              ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                              ->where(['ord.or_id'=>$or['or_id'],'ord.dict_fid'=>0,'dt.sign'=>'service_type'])
                                              ->field(['d.id','d.value'])
                                              ->find();
         $or['top_dict'] = $top_dict['value'];
         //二级业务
         $son_dict = db('OrganizeRequireDict')->alias('ord')
                                              ->join('Dict d','ord.dict_id=d.id')
                                              ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                              ->where(['ord.or_id'=>$or['or_id'],'ord.dict_fid'=>$top_dict['id'],'dt.sign'=>'service_type'])
                                              ->field('d.value')
                                              ->select();

        if (!empty($son_dict)) {
 
            $or['son_dict'] = ' - '.implode(',',array_column($son_dict, 'value'));
        } else {
            $or['son_dict'] = '';
        }

        //如果一级业务类型不是配资业务,则显示行业内容
        if ($or['top_dict'] !== '配资业务') {
            //一级行业+二级行业
            $industry_value = [];
            $top_industry = db('OrganizeRequireDict')->alias('ord')
                                                     ->join('Dict d','ord.dict_id=d.id')
                                                     ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                     ->where(['ord.or_id'=>$or['or_id'],'ord.dict_fid'=>0,'dt.sign'=>'industry'])
                                                     ->select();
            if (!empty($top_industry)) {
                foreach ($top_industry as &$value) {
                    $industry = db('OrganizeRequireDict')->alias('ord')
                                                         ->join('Dict d','ord.dict_id=d.id')
                                                         ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                                         ->where(['ord.or_id'=>$or['or_id'],'ord.dict_fid'=>$value['dict_id'],'dt.sign'=>'industry'])
                                                         ->field(['d.value'])
                                                         ->select();

                    if (!empty($industry)) {
                        $industry_value[] = $value['value'].' - '.implode(',',array_column($industry, 'value')); 
                    } else {
                        $industry_value[] = $value['value'];
                    }
                    
                }
            } 
            $or['industry'] = $industry_value;
        }
        
        //省份
         $province = db('OrganizeRequireDict')->alias('ord')
                                              ->join('Dict d','ord.dict_id=d.id')
                                              ->join('DictType dt','ord.dict_type_id=dt.dt_id')
                                              ->where(['ord.or_id'=>$or['or_id'],'ord.dict_fid'=>0,'d.status'=>1,'dt.sign'=>'to_province'])
                                              ->field(['d.value'])
                                              ->select();
         if (!empty($province)) {
             $or['province'] = implode(',', array_column($province, 'value'));
         } else {
             $or['province'] = '';
         }
         
         $or['is_show'] = is_show($or['is_show']);
         $or['status'] = status($or['status']);
         //资金类型
         $or['fund_type'] = getFundType($or['fund_type']);
         //投资期限
         $or['financing_period'] = getFinancingPeriod($or['financing_period']);
         //投资方式
         $or['invest_mode'] = getInvestMode($or['invest_mode']);
         //业务截止日期
         $or['deadline'] = deadline($or['create_time'],$or['validity_period']);
         //联系人身份
         $or['contact_status'] =contact_status($or['contact_status']);
         return $or;
    }

    //排序
    public function setOrganizeRequireOrderList($data = [])
    {
         if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('OrganizeRequire');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['or_id' => $data['id']])->update(['rank' => $data['list_order']]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    //取消置顶
    public function setOrganizeRequireTopFalse($id = '')
    {
        if(!$id){
            $this->error = '资金id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['or_id' => $id])->update(['is_top' => 2]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    //置顶
    public function setOrganizeRequireTopTrue($id = '')
    {
        if(!$id){
            $this->error = '资金id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['or_id' => $id])->update(['is_top' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    //取消致精
    public function setOrganizeRequireCareFalse($id = '')
    {
        if(!$id){
            $this->error = '资金id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['or_id' => $id])->update(['is_care' => 2]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    //致精
    public function setOrganizeRequireCareTrue($id = '')
    {
        if(!$id){
            $this->error = '资金id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['or_id' => $id])->update(['is_care' => 1]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
    
    //修改内部客服id
    public function editOrganizeRequireCs($or_id = 0,$inner_cs = 0)
    {
        if(!$or_id){
            $this->error = '资金id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['or_id' => $or_id])->update(['inner_cs' => $inner_cs]);
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return false;
        }
    }
}