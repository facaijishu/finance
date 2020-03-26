<?php
namespace app\home\controller;
use think\db;

class Organize extends Base
{   
   //判断是否是已认证的合伙人
   public function OrganizeApi()
   {
     $url = url('Organize/orglist');
     $this->result($url);
   } 
   
   //扫码时判断是否是已认证的合伙人
   public function _initialize()
    {
        $user = session('FINANCE_USER');   
        $model = model("Through");
        $info = $model->getThroughInfoById($user['uid']);
        if(empty($info) || $info['status'] != 1){
            $this->redirect('Index/index');
        }
    }
    
    //机构中心列表
    public function orglist(){
        $this->assign('title' , ' - 机构中心');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最具体的机构中心!');
        $organize = model("Organize");
        $infos = $organize->getOrganizeLimitList(0 , config("ins_num"));
        $data = [];
        $dict = model('Dict');
        $material = model('MaterialLibrary');
      
        foreach ($infos as  $k => $info) {
        	 $unit         = $dict->getDictInfoById($info['unit']);
             $info['unit'] = $unit['value'];
             //处理inc_industry(1,76,77,78,79,80,82,110)
            $str_industry  = '';
            $industry      = $dict->getDictInfoByIdSet($info['inc_industry']);
            $industry      = $industry[0];
            foreach ($industry as $value) {
                   $str_industry.=$value['value'].'、';
            }
            $info['inc_industry'] = trim($str_industry,'、');
            //处理inc_funds
            $str_funds  = '';
            $funds      = $dict->getDictInfoByIdSet($info['inc_funds']);
            $funds      = $funds[0];
            foreach ($funds as $value) {
                   $str_funds.=$value['value'].'/';
            }
            $info['inc_funds']  = trim($str_funds,'/');
            //处理top_img
            $top_img            = $material->getMaterialInfoById($info['top_img']);
            $info['top_img']    = $top_img['url'];
            //处理微信头像
            $info['wx_img']     = '';
            if(!empty($info['wx_uid'])){
                $member         = model('member')->getMemberInfoById($info['wx_uid']);
                $info['wx_img'] = $member['userPhoto']; 
            }  
            $data[] = $info->toArray();
        }
        //增加的代码
        $model = model("dict");
        $funds_nature = $model->getDictByType('funds_nature');
        $this->assign('funds_nature' , $funds_nature);
        $this->assign('num' , count($data));
        $this->assign('organize' , $data);
        //客服logo
        $model = model('Project');
        $project = $model->getProjectLimitOne();
        $this->assign('project',$project);
        return view();
    }
   
	
	//机构中心加载更多,模糊搜索,筛选
	public function loadOrgInfo(){
        $data = input();
        if(!array_key_exists('inc_funds', $data)){
            $data['inc_funds'] = '';
    
        }
        if(!array_key_exists('scale', $data)){
            $data['scale'] = '';
            
        }
		$id = input("id");
		$model = model('OrganizeDict');
        $model ->alias('od')
               ->join('Organize o','od.org_id=o.org_id')
               ->join('Dict d','od.id=d.id')
               ->field(" SQL_CALC_FOUND_ROWS o.*,group_concat(d.value separator '、') value ")
               ->group('o.org_name')
               ->order('o.list_order desc,o.org_id desc')
               ->where(['o.status' => 2 , 'o.flag' => 1]);      
        //搜索条件
        if ('' !== trim($data['text'])) {
		     $model->where("org_name like '%".$data['text']."%' or value like '%".$data['text']."%'");
	    }
        //资金类型
        $shai_funds = trim($data['inc_funds'],',');
        if ('' !== $shai_funds) {
             $model->where("inc_funds regexp replace('$shai_funds',',','|')");
        }
        
        //自有规模
        $shai_scale = trim($data['scale'],',');
        if('' !== $shai_scale){
            $arr_scale = [];
            if(!strpos($shai_scale,',')){
                $arr_scale = [$shai_scale];
            }else{
                $arr_scale = explode(',', $shai_scale);
            }
            $arr = [
               1=>['min'=>0,'max'=>1000,'unit'=>'万'],
               2=>['min'=>1000,'max'=>5000,'unit'=>'万'],
               3=>['min'=>0.5,'max'=>1,'unit'=>'亿'],
               4=>['min'=>1,'max'=>5,'unit'=>'亿'],
               5=>['min'=>5,'max'=>10,'unit'=>'亿'],
               6=>['min'=>10,'max'=>'','unit'=>'亿'], 
            ];
            
            foreach ($arr as $k => $v) {
                $re = model('Dict')->where(['value'=>$v['unit']])->find();
                $arr[$k]['unit'] = $re['id']; 
             } 
             
            $str_scale = '';  
            foreach ($arr_scale as $value) {
                $tmp = $arr[$value];
                if($tmp['max']){
                    $str_scale .= " (o.scale_min >=".$tmp['min']." and o.scale_max <= ".$tmp['max']."  and o.unit= ".$tmp['unit'].") or";
                }else{
                    $str_scale .= " (o.scale_min >=".$tmp['min']." and o.unit= ".$tmp['unit'].") or";
                }
               
           }
            $str_scale = rtrim($str_scale,'or ');
            $model->where($str_scale);
        }
        
		if(isset($id)){
			$result = $model->limit($id, config('ins_num'))->select();
		}else{
			$result = $model->limit(0, config('ins_num'))->select();
		}
		if(empty($result)){
			$this->result('' , 0 , '不存在数据1' , 'json');
		}

		//处理查询的结果
        $data       = [];
        $dict       = model('Dict');
        $material   = model('MaterialLibrary');
        foreach ($result as  $info) {
            $unit = $dict->getDictInfoById($info['unit']);
            $info['unit']       = $unit['value'];
            //处理inc_funds
            $str_funds = '';
            $funds              = $dict->getDictInfoByIdSet($info['inc_funds']);
            $funds              = $funds[0];
            foreach ($funds as $value) {
                $str_funds      .=$value['value'].'、';
            }
            $info['inc_funds']  = trim($str_funds,'、');
            
            //处理top_img
            $top_img            = $material->getMaterialInfoById($info['top_img']);
            $info['top_img']    = $top_img['url'];  
            
            $data[]             = $info->toArray();
            //处理微信头像
            if(!empty($info['wx_uid'])){
                $member         = model('member')->getMemberInfoById($info['wx_uid']);
                $info['wx_img'] = $member['userPhoto']; 
            }
        }

        if($data){
            $this->result($data , 1 , '搜索成功' , 'json');
        } else {
            $this->result('' , 0 , '不存在数据' , 'json');
        }
    }
    
    //机构详情
    public function detail(){   
        //$this->assign('title' , '- 机构中心');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最具体的大宗转让!');
        $data   = input();
        //dump($data);die;
        $org_id = intval($data['id']);
        $show   = intval($data['show']);

        if(!is_numeric($org_id) || !is_numeric($show)){
            return false;
        }

        $info   = model('Organize')->find($org_id);
        $this->assign('title' , '- '.$info['org_name']);    
        
        $model  = model("dict");
        //单位
        $unit = $model->getDictInfoById($info['unit']);
        $info['unit'] = $unit['value'];
        
        //投资企业+所属行业
        $arr_target = [];
        if(strpos($info['inc_target'], '-')){
            $inc_targets = explode('-', $info['inc_target']);
            foreach ($inc_targets as $key => $value){
                $inc_targets2 = explode('+', $value);
                if(count($inc_targets2) == 2){
                    $arr_target[] = array(
                        'target_company'  => $inc_targets2[0],
                        'target_industry' => $inc_targets2[1]
                    );
               }  
            }
        }else{
            if(strpos($info['inc_target'],'+') !== false){
                $inc_targets2 = explode('+', $info['inc_target']);
                if(count($inc_targets2) == 2){
                    $arr_target[] = array(
                        'target_company'  =>$inc_targets2[0],
                        'target_industry' => $inc_targets2[1]
                    );
                }
            }
        }
        $info['inc_target'] = $arr_target;
        
        // dump($info['inc_target']);die;
        
        //投资方向
        $industry             = explode(',', $info['inc_industry']);
        $inc_industry         = '';
        foreach ($industry as $key => $value) {
            $dict             = $model->getDictInfoById($value);
            $inc_industry    .= $dict['value'].'  ';
        }
        $info['inc_industry'] = trim($inc_industry , '  ');
        
        //所属区域
        $area               = explode(',', $info['inc_area']);
        $inc_area           = '';
        foreach ($area as $key => $value) {
            $dict           = $model->getDictInfoById($value);
            $inc_area      .= $dict['value'].' 、 ';
        }
        $info['inc_area']   = trim($inc_area , ' 、');
        
        //资金性质
        $funds               = explode(',', $info['inc_funds']);
        $inc_funds           = '';
        foreach ($funds as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_funds      .= $dict['value'].'、';
        }
        $info['inc_funds']   = trim($inc_funds , '、');
        
        //资本类型
        $capital             = explode(',', $info['inc_capital']);
        $inc_capital         = '';
        foreach($capital as $key => $value) {
            $dict            = $model->getDictInfoById($value);
            $inc_capital    .= $dict['value'].' , ';
        }
        $info['inc_capital'] = trim($inc_capital , ' , ');
        
        $model               = model("MaterialLibrary");
        $top_img             = $model->getMaterialInfoById($info['top_img']);
        $info['top_img']     = $top_img['url'];
        
        //是否获取微信头像
        $info['wx_img'] = '';
        $model          = model('member');
        $member         = $model->getMemberInfoById($info['wx_uid']);
        if($member) {
           $info['wx_img'] = $member['userPhoto']; 
        }else{
           if($show == 1){
             $wxInfo = $this->jsGlobal;
             $wx_uid = $wxInfo['member']['uid'];
             $re     = model('Organize')->editOrganizeWxuid($org_id,$wx_uid);
             if(!$re){
                return false;
             }
             $org               = model('Organize')->find($org_id);
             $member            = $model->getMemberInfoById($org['wx_uid']);
             $info['wx_img']    = $member['userPhoto'];  
           }

        }         
        $this->assign('info',$info); 
        return view();

    }
    //搜索框监听input的模糊查询
    public function search()
    {
        $data = input();
        $arr  = [];
        //机构中心的结果
        $model = model('OrganizeDict');
        $model ->alias('od')
               ->join('Organize o','od.org_id=o.org_id')
               ->join('Dict d','od.id=d.id')
               ->field("SQL_CALC_FOUND_ROWS o.org_id,o.org_name,group_concat(d.value separator '、') value")
               ->group('o.org_name')
               ->order('o.list_order desc,o.org_id desc')
               ->where(['o.status' => 2 , 'o.flag' => 1]);      
        //搜索条件
        if('' !== $data['value']){
            $organize = $model->where("org_name like '%".$data['value']."%' or value like '%".    $data['value']."%'")->select();
        }
        
        $organize = empty($organize) ? null : $organize;
        if(!empty($organize)){
            foreach ($organize as $key => $value){
                $arr[] = $value->toArray();
            }
        }
        
        if(!empty($arr)){
            $this->result($arr,200,'数据查询成功','json');
            return false;
        }else{
            $this->result(0,404,'数据查询为空','json');
            return false;
        }
    }
}
