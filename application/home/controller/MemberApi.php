<?php 
namespace app\home\controller;
/**
 * 
 */
class MemberApi extends BaseApi
{
	public function __construct()
	{   
		parent::__construct();
		if (!session('FINANCE_USER')) {
			return $this->result('',201,'请先登录','json');
		}
	}
	
	//查看一条合伙人的详情
	public function showMemberDetail()
	{   

       $user = session('FINANCE_USER');
       //用户类型判断
       /*if ($user['userType'] == 0) {
          return $this->result('',280,'你当前还不是FA財平台合伙人,认证FA財平台合伙人,你的地盘你做主!','json');    
       }
       if ($user['userType'] == 1) {
          return $this->result('',281,'你的个人信息未完善','json');    
       }*/
        $from_uid = $user['uid'];                 
		$uid = $this->request->param('uid')?$this->request->param('uid'):0;

		model('Member')->showMemberDetailApi($uid,$from_uid);
	}

	/**
	 * TA的业务(用户发布的项目,资金,投递的项目)
	 */
	public function getOtherBusiness()
	{
        //获取一个用户的uid  
		$uid       = $this->request->param('uid')?$this->request->param('uid'):0;
        //当前登录用户的uid
        $login_uid = session('FINANCE_USER.uid');
        //获取当前页码
        if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
        {
            $page = 1;
        }else{
            $page = $this->request->param('page');
        }
        //查询未删除的业务
        model('ProjectRequire')->getMyBusinessApi($uid,$login_uid,$page,10);
	}
	
	//TA的需求(发现)
	public function getOtherPublish()
	{   
		//获取一个用户的uid  
		$uid = $this->request->param('uid')?$this->request->param('uid'):0;
        //获取当前页码
        if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
        //查询未删除的发现
        model('Publish')->getMyPublishApi($uid,$page,10);
        
	}
	//用户查看发布项目/资金,该项目/资金查看人数+1
	public function editViewNum()
	{
		$type = $this->request->param('type')?$this->request->param('type'):'';
		$id = $this->request->param('id')?$this->request->param('id'):'';
		switch ($type) {
			case 'project_require':
				model('ProjectRequire')->editViewNumApi($id);
				break;
			case 'organize_require':
				model('organizeRequire')->editViewNumApi($id);
				break;
			default:
				$this->code = 223;
        		$this->msg = '参数非法';
        		return $this->result('',$this->code,$this->msg,'json');
		}
	}
	//用户电话联系发布项目/资金拥有者,该项目/资金电话联系次数+1
	public function editTelNum()
	{
		$type = $this->request->param('type')?$this->request->param('type'):'';
		$id = $this->request->param('id')?$this->request->param('id'):'';
		switch ($type) {
			case 'member':
				model('Member')->editTelNumApi($id);
				break;
			case 'project_require':
				model('ProjectRequire')->editTelNumApi($id);
				break;
			case 'organize_require':
				model('organizeRequire')->editTelNumApi($id);
				break;
			default:
				$this->code = 223;
        		$this->msg = '参数非法';
        		return $this->result('',$this->code,$this->msg,'json');
		}
	}
}