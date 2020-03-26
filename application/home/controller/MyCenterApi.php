<?php
namespace app\home\controller;
/**
 * 
 */
class MyCenterApi extends BaseApi
{   
    public function __construct()
    {   
        parent::__construct();
        if (!session('FINANCE_USER')) {
            return $this->result('',201,'请先登录','json');
        }
    }
	//获取个人中心发现列表(我的需求)
	public function getMyPublish()
	{   
		//获取当前用户的uid  
		$uid = session('FINANCE_USER.uid');
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

	//删除个人中心一条发现
	public function delMyPublish()
	{
		//获取当前发现的唯一id
		$pub_id = $this->request->param('id')?$this->request->param('id'):0;
		//获取当前用户的uid  
		$uid = session('FINANCE_USER.uid');
		model('Publish')->delMyPublishApi($pub_id,$uid);
	}

	//获取个人中心业务列表(用户发布的项目,资金,投递的项目)
	public function getMyBusiness()
	{
		//获取当前用户的uid  
		$uid = session('FINANCE_USER.uid');
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
	
	//个人中心-删除一条业务(发布项目||发布资金)
	public function delMyBusiness()
	{
		//点击删除的一条业务所属表的类型
		$type = $this->request->param('type')?$this->request->param('type'):'';
		//点击删除的一条业务的唯一id
		$id = $this->request->param('id')?$this->request->param('id'):0;
		//获取当前用户的uid  
		$uid = session('FINANCE_USER.uid');
		switch ($type) {
			case 'project_require':
				model('ProjectRequire')->delMyProjectRequireApi($id,$uid);
				break;
			case 'organize_require':
				model('OrganizeRequire')->delMyOrganizeRequireApi($id,$uid);
				break;
			default:
				$this->code = 223;
				$this->msg = '参数非法';
				return $this->result('',$this->code,$this->msg,'json');
				break;
		}
	} 
	//个人中心-我的关注
	public function showMyFollow()
	{
		//获取当前用户的uid  
		// $uid = session('FINANCE_USER.uid');
		$uid = session('FINANCE_USER.uid');
		//获取当前页码
        if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
		model('FollowUser')->showMyFollowApi($uid,$page,10);
	}

	//个人中心-我的粉丝
	public function showMyFans()
	{
		//获取当前用户的uid  
		// $uid = session('FINANCE_USER.uid');
		$uid = session('FINANCE_USER.uid');
		//获取当前页码
        if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
		model('FollowUser')->showMyFansApi($uid,$page,10);
	}

	//个人中心-我的人脉
	public function showMyConnections()
	{
       //获取当前用户的uid  
		$uid = session('FINANCE_USER.uid');
		//获取当前页码
        if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
       model('DictLabel')->showMyConnectionsApi($uid,$page,10);
	}
	
	
    //个人中心-我的收藏-项目列表
    public function showMyCollectProject()
    {
    	//获取当前用户的uid  
		$uid = session('FINANCE_USER.uid');
		//获取当前页码
        if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
        {
          $page = 1;
        }else{
          $page = $this->request->param('page');
        }
        model('Project')->showMyCollectProjectApi($uid,$page,10);
    }
    
    //个人中心-我的收藏-资金列表
     public function showMyCollectOrganize()
    {
    	//获取当前用户的uid  
		$uid = session('FINANCE_USER.uid');
		//获取当前页码
        if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
        {
          $page = 1;
        }else{
          $page = $this->request->param('page');
        }
        model('Organize')->showMyCollectOrganizeApi($uid,$page,10);
    }
    
    
    //收藏/取消收藏一条项目/资金
    public function setCollect()
    {
    	$type  = $this->request->param('type')?$this->request->param('type'):'';
    	$id    = $this->request->param('id')?$this->request->param('id'):'';
    	$sign  = $this->request->param('sign')?$this->request->param('sign'):true;
    	switch ($type) {
    		case 'project':
    			model('Member')->setCollectProject($id,$sign);
    			break;
    		case 'organize':
    			model('Member')->setCollectOrganize($id,$sign);
    			break;
    			
    		case 'project_require':
    		    model('Member')->setCollectProjectRequire($id,$sign);
    		    break;
    		    
    		case 'organize_require':
    		    model('Member')->setCollectOrganizeRequire($id,$sign);
    		    break;
    		default:
    			$this->code =223;
    			$this->msg = '参数非法';
    			return $this->result('',$this->code,$this->msg,'json');
    			break;
    	}
    	
    }
    
    public function getIndex(){
        $res    = [];
        
        //获取当前用户的uid
        $uid    = session('FINANCE_USER.uid');
        
        $mem    = model("Member")->getMemberInfoById($uid);
        
        $org    = model("Organize")->getOrganizeInfoByTel($mem['userPhone']);
        if($mem['role_type']==2){
            $res['num'] = $mem['service_num'];
        }else{
            if(empty($org)){
                $res['num']     = "0";
                $res['org_url'] = "#";
            }else{
                $res['num']     = "1";
                if($org['is_confirm']==1){
                    $res['org_url']  = "/static/frontend/detailsShowSelectedFunds.html?id=".$org['org_id']."&type=organize";
                }else{
                    $res['org_url']  = "/home/organize_info/edit/id/".$org['org_id'];
                }
            }
        }
        
        
        //我的业务
        $pro    = model('ProjectRequire')->sumProNum($uid);
        
        //我的动态
        $pub    = model('Publish')->sumPubNum($uid);
        
        //我的关注
        $follow = model('FollowUser')->sumMyFollow($uid);
        
        //我的粉丝
        $fans   = model('FollowUser')->sumMyfans($uid);
        
        $res['role']    = $mem['role_type'];
        $res['pro']     = $pro;
        $res['pub']     = $pub;
        $res['follow']  = $follow;
        $res['fans']    = $fans;
        
        return $this->result($res,'200','','json');
    }
    
}