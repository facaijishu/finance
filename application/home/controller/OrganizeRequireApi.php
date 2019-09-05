<?php 
namespace app\home\controller;
use app\home\validate\OrganizeRequire;
class OrganizeRequireApi extends BaseApi
{   
	//用户登录/身份前置判断
    public function __construct()
    {  
       parent::__construct();
       if (session('FINANCE_USER')) {
               //发布前先判断用户类型
               $userType = session('FINANCE_USER.userType');
           
        } else {
               return $this->result('',201,'请先登录','json');
        }
     
    }
    
    /**
     * 发布一条资金
     */
	public function addOrganizeRequire()
	{
	    //insert organizeRequire表
		$or_data = [];
		$or_data['name']              = $this->request->param('name');//业务名称
		$or_data['uid']               = session('FINANCE_USER.uid');//用户uid
		$or_data['create_time']       = time();//发布项目的时间
		$or_data['business_des']      = $this->request->param('business_des');//业务描述
		$or_data['financing_period']  = $this->request->param('financing_period');//投资期限
		$or_data['invest_scale']      = $this->request->param('invest_scale');//投资规模
		$or_data['fund_type']         = $this->request->param('fund_type');//资金类型(多选)
		$or_data['invest_phase']      = $this->request->param('invest_phase');   //投资阶段(可选)
		$or_data['invest_target']     = $this->request->param('invest_target');//投资标准
		$or_data['validity_period']   = $this->request->param('validity_period');//业务有效期
		$period                       = validity_deadline($or_data['validity_period']);
		$deadline                     = $or_data['create_time']+$period*24*60*60;
		$or_data['deadline']          = $deadline;//业务有效期(天数)
		
		$or_data['contact_status']    = $this->request->param('contact_status');//联系人身份
		$or_data['invest_mode']       = $this->request->param('invest_mode'); //投资方式(多选)
	
		//insert organizeRequireDict表
		$ord_data = [];
		$ord_data['top_dict_id']      = $this->request->param('top_dict_id');//业务一级标签(单选)
	    $ord_data['dict_id']          = $this->request->param('dict_id');//业务二级标签(多选,必填)
        if(empty($ord_data['dict_id'])) {
       	    return $this->result('',217,'请选择业务细分领域','json');
        }
	    //投资省份(多选,必填)
        $ord_data['province']         = $this->request->param('province');
        if (empty($ord_data['province'])) {
       	    return $this->result('',217,'请选择业投资省份','json');
        }

        //查询一级业务类型名称
        $top_dict_value               = model('Dict')->field('value')->where(['id'=>$ord_data['top_dict_id']])->find();
        if(empty($top_dict_value)) {
          return $this->result('',216,'一级业务标签不存在','json');
        }
        $top_dict_value               = $top_dict_value['value'];//一级业务类型名称

        //如果一级业务类型是配资业务,则不用接收行业参数
        if($top_dict_value !== '配资业务') {
       	   //关注的行业（只选1级/选到2级）
           $industry_id               = $this->request->param('industry_id');
           if(empty($industry_id)) {
               return $this->result('',217,'请选择关注的行业','json');
           }
           $top_industry        = '';
           $top_industry2       = [];
           $top_industry2_str   = '';
           $industry            = '';
           if(!empty($industry_id)){
           	   $industry_arr = explode(',', $industry_id);
           	   foreach ($industry_arr as &$value) {
           	   	    $fid = model('Dict')->field('fid')->where(['id'=>$value])->find();
           	   	    if ($fid['fid'] == 0) {
           	   	    	$top_industry  .= $value.',';
           	   	    } else {
           	   	    	$industry      .= $value.',';
                        //根据二级找出对应的一级并去重
                        $top_industry2[] = $fid['fid'];
           	   	    }
           	   	    
           	   }	       	   
           } else {
           	   return $this->result('',216,'行业标签不存在','json');
           }
           //关注的一级行业标签(多选,必填)
           if (!empty($top_industry2)) {
           	    $top_industry2_str      = implode(',',array_unique($top_industry2));
           }
        
           $ord_data['top_industry']    = trim($top_industry.$top_industry2_str,',');
           $ord_data['industry']        = trim($industry,',');//关注的二级行业标签(多选,选填)
        
        } 
	       	   
        $validate = new OrganizeRequire($top_dict_value);
        if(!$validate->scene('save')->check($or_data)){
            $this->code = 217;
            $this->msg  = $validate->getError();
            return $this->result('',$this->code,$this->msg,'json');
        }
        $or = model('OrganizeRequire');
        $or->addOrganizeRequireApi($or_data,$ord_data,$top_dict_value); 
	}

	/**
	 * 根据对应一二级业务标签返回对应发布资金的表单输入框的命名
	 */
    public function showOrganizeRequireForm()
    {
        //业务一级标签(Y)
        $top_dict_id = $this->request->param('top_dict_id')?$this->request->param('top_dict_id'):0;
        $dict = model('Dict');
        
        //根据一级业务类型的名称名称确定不同发布资金表单的input框
        $top_dict_value = $dict->field('value')->where(['id'=>$top_dict_id])->find();
        
        if (empty($top_dict_value)) {
            return $this->result('',216,'标签不存在','json');
        }

        //一级业务类型名称
        $top_dict_value = $top_dict_value['value'];          
        
        //除一级业务为配资返回的表单参数不同,其他业务都相同
        $data['name']               = '业务名称';
        $data['business_des']       = '业务描述';
        $data['province']           = '投资省份';
        if ($top_dict_value !== '配资业务') {
            $data['industry_id']    = '关注的行业';
        }
        $data['financing_period']   = '投资期限';
        if ($top_dict_value !== '配资业务') {
            $data['invest_scale']   = '投资规模';
        } else {
            $data['invest_scale']   = '资金规模';
        }
        
        $data['fund_type']          = '资金类型';
        $data['invest_mode']        = '投资方式';
        $data['invest_phase']       = '投资阶段';
        if ($top_dict_value !== '配资业务') {
            $data['invest_target']  = '投资标准';
        } else {
            $data['invest_target']  = '配资标准';
        }
        $data['validity_period']    = '业务有效期';
        $data['contact_status']     = '联系人身份';
        return $this->result($data,200,'','json'); 
    }
}
