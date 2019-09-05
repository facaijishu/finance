<?php
namespace app\console\model;
use think\Model;
class ProjectRequire extends Model 
{   
	//读取项目管理列表页数据
	public function readProjectRequire($start = 0,$length = 20,$pr_id = '',$real_name = '',$is_show = '',$status = '')
	{    
		 $return = [];
         $this->alias('pr')
              ->join('Member m','pr.uid=m.uid')
              ->join('Dict d','pr.top_dict_id = d.id')
              ->join('Dict d2','pr.dict_id = d2.id')
              ->field('SQL_CALC_FOUND_ROWS pr.pr_id,pr.uid,m.realName,pr.create_time,pr.deadline,d.value,d2.value as value2,pr.business_des,pr.is_show,pr.status,pr.rank,pr.is_top,pr.is_care')
              ->order('pr.pr_id desc');
         
         if ('' != $pr_id) {
             $this->where(['pr.pr_id' => $pr_id]);
         }
         
         if ('' != $real_name) {
             $map['m.realName'] = array('like',"%$real_name%");
         	 $this->where($map);
         }
         
         //显示状态
         if ('' != $is_show) {
             $this->where(['pr.is_show' => $is_show]);
         }
         
         //有效状态
         if ('' != $status) {
             $now = time();
             if($status==2){
                 $this->where("pr.deadline < $now ");
             }else{
                 $this->where("pr.deadline >= $now" );
             }
         }
         
         
         $this->where(['pr.status' => 1]);
         
         $list      = $this->limit($start,$length)->select();
         $result    = $this->query('SELECT FOUND_ROWS() AS count');
         $total     = $result[0]['count'];
         foreach ($list as $key => $item) {
             $list[$key]['deadline'] = date('Y-m-d H:i:s', $item['deadline']);
             $data[] = $item->toArray();
         }
         $return['data']            = $list;
         $return['recordsFiltered'] = $total;
         $return['recordsTotal']    = $total;
         return $return;

	}
	
	//导出项目管理数据到excel表
	public function excelProjectRequire($pr_id = '',$real_name = '',$is_show = '',$status = '')
	{
         $this->alias('pr')
              ->join('Member m','pr.uid=m.uid')
              ->join('Dict d','pr.top_dict_id = d.id')
              ->join('Dict d2','pr.dict_id = d2.id')
              ->field('SQL_CALC_FOUND_ROWS pr.pr_id,pr.uid,pr.name,m.realName,pr.deadline,pr.create_time,d.value,d2.value as value2,pr.financing_scale,pr.ly_net_profit,pr.validity_period,pr.financing_scale,pr.inner_cs,pr.financing_purpose,pr.contact_status,pr.business_des,pr.is_show,pr.status,pr.top_dict_id,pr.dict_id,pr.top_industry_id,pr.industry_id,pr.province_id,pr.pro_highlight')
              ->order('pr.pr_id desc');
         if ('' != $pr_id) {
         	$this->where(['pr.pr_id' => $pr_id]);
         }
         
         if ('' != $real_name) {
            $map['m.realName'] = array('like',"%$real_name%");
            $this->where($map);
         }
         
         //显示状态
         if ('' != $is_show) {
             $this->where(['pr.is_show' => $is_show]);
         }
         
         //有效状态
         if ('' != $status) {
             $now = time();
             if($status==2){
                 $this->where("pr.deadline < $now ");
             }else{
                 $this->where("pr.deadline >= $now" );
             }
         }
         $this->where(['pr.status' => 1]);
         $list = $this->select();
         foreach ($list as &$v) {
             if($v['is_show']==1){
                 $v['is_show']            = "展示中";
             }elseif($v['is_show']==2){
                 $v['is_show']            = "已隐藏";
             }else{
                 $v['is_show']            = "已删除";
             }
             
             $v['contact_status']     = contact_status($v['contact_status']);
             $v['validity_period']    = validity_period($v['validity_period']);
             $dict                    = model('Dict');
             $top_dict                = $dict->getDictInfoById($v['top_dict_id']);
             $son_dict                = $dict->getDictInfoById($v['dict_id']);
             $province                = $dict->getDictInfoById($v['province_id']);
             $top_industry            = $dict->getDictInfoById($v['top_industry_id']);
             $son_industry            = $dict->getDictInfoById($v['industry_id']);
             $v['province']           = $province['value'];
             $v['top_dict']           = $top_dict['value'];
             $v['dict']               = $top_dict['value'].' - '.$son_dict['value'];
             $v['province']           = $province['value'];
             $v['top_industry']       = $top_industry['value'];
             $v['industry']           = $top_industry['value'].' - '.$son_industry['value'];
             $v['deadline']           = date('Y-m-d H:i:s', $v['deadline']);
         }
         
         
         return $list;
	}
	
    //发布项目详情页数据
    public function projectRequireDetail($id = '')
    {
         if(!$id){
            $this->error = '项目id不存在';
            return false;
         }
         $pr                    = $this->where(['pr_id' => $id])->find();
         $through               = model('Through');
         $realName              = $through->readThroughByUid($pr['uid']);
         $dict                  = model('Dict');
         $top_dict              = $dict->getDictInfoById($pr['top_dict_id']);
         $son_dict              = $dict->getDictInfoById($pr['dict_id']);
         $province              = $dict->getDictInfoById($pr['province_id']);
         $top_industry          = $dict->getDictInfoById($pr['top_industry_id']);
         $son_industry          = $dict->getDictInfoById($pr['industry_id']);
         $pr['realName']        = $realName;
         $pr['top_dict']        = $top_dict['value'];
         $pr['dict']            = ' - '.$son_dict['value'];
         $pr['province']        = $province['value'];
         $pr['top_industry']    = $top_industry['value'];
         $pr['industry']        = ' - '.$son_industry['value'];
         $pr['is_show']         = is_show($pr['is_show']);
         $pr['status']          = status($pr['status']);
         return $pr;
    }
    
    //隐藏一条项目需求
    public function stopProjectRequire($id = 0)
    {
        if(!$id){
            $this->error = '发布项目id不存在';
            return false;
        }
        $this->startTrans();
        try {
            $data['is_show'] = 2;
            $this->where(['pr_id' => $id])->update($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return false;
        }

    }
    
    //展开一条项目需求
    public function startProjectRequire($id)
    {
        if(!$id){
            $this->error = '发布项目id不存在';
            return false;
        }
        $this->startTrans();
        try {
            $data['is_show'] = 1;
            $this->where(['pr_id' => $id])->update($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return false;
        }
    }
    
    
    //排序
    public function setProjectRequireOrderList($data = [])
    {
        if(empty($data)){
            $this->error = '缺少相关数据';
            return false;
        }
        //验证数据信息
        $validate = validate('ProjectRequire');
        $scene = isset($data['id']) ? 'edit' : 'add';
        if(!$validate->scene($scene)->check($data)){
            $error = $validate->getError();
            $this->error = $error;
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['pr_id' => $data['id']])->update(['rank' => $data['list_order']]);
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
    public function setProjectRequireTopFalse($id = '')
    {
        if(!$id){
            $this->error = '发布id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['pr_id' => $id])->update(['is_top' => 2]);
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
    public function setProjectRequireTopTrue($id = '')
    {
        if(!$id){
            $this->error = '发布id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['pr_id' => $id])->update(['is_top' => 1]);
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
    public function setProjectRequireCareFalse($id = '')
    {
        if(!$id){
            $this->error = '发布id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['pr_id' => $id])->update(['is_care' => 2]);
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
    public function setProjectRequireCareTrue($id = '')
    {
        if(!$id){
            $this->error = '发布id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['pr_id' => $id])->update(['is_care' => 1]);
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
    public function editProjectRequireCs($pr_id = 0,$inner_cs = 0)
    {
        if(!$pr_id){
            $this->error = '项目id不存在';
            return false;
        }
        //开启事务
        $this->startTrans();
        try{
            $this->where(['pr_id' => $pr_id])->update(['inner_cs' => $inner_cs]);
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