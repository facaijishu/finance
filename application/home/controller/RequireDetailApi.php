<?php
namespace app\home\controller; 
class RequireDetailApi extends BaseApi
{   
    //用户登录/身份前置判断
    public function __construct()
    {  
       parent::__construct();
       if (session('FINANCE_USER')) {
              $user = session('FINANCE_USER');
              $user = model('Member')->getMemberInfoById($user['uid']);
              session('FINANCE_USER',$user);
                        
        } else {
              
              return $this->result('',201,'请先登录','json');

        }
     
    }
	//项目的详情(发布项目='project_require')
	public function showProjectDetail()
	{   
        
        //当前用户收藏的发布项目id集合
        $data['collection_project_require'] = session('FINANCE_USER.collection_project_require');  
		//接收表的类型
	    $table_type = trim($this->request->param('type'))?trim($this->request->param('type')):'';
	    //唯一id
	    $data['id'] = trim($this->request->param('id'))?trim($this->request->param('id')):0;
        //当前登录用户uid
        $data['uid'] = session('FINANCE_USER.uid');
        $user = model('Member')->getMemberInfoById($data['uid']);
        session('FINANCE_USER',$user);
        
        switch ($table_type) {
        	
            case 'project_require':
        		model('ProjectRequire')->getProjectRequireDetail($data);
        		break;

        	default:
        		$this->code = 223;
        		$this->msg = '参数非法';
        		return $this->result('',$this->code,$this->msg,'json');
        		break;
        }
	}
	
	
    //精选资金='organize', 发布资金='organize_require'
    public function showOrganizeDetail()
    {
        //唯一id
        $data['id'] = trim($this->request->param('id'))?trim($this->request->param('id')):0;
        
        $uid  = session('FINANCE_USER.uid');
        $user = model('Member')->getMemberInfoById($uid);
        session('FINANCE_USER',$user);
        
        //当前用户收藏的精选资金id集合
        $data['collection_organize']            = $user['collection_organize'];
        //当前用户收藏的发布资金id集合
        $data['collection_organize_require']    = $user['collection_organize_require'];
        //当前登录的用户
        $data['uid']                            = $uid;  
        //接收表的类型
        $table_type = trim($this->request->param('type'))?trim($this->request->param('type')):'';
        switch ($table_type) {
            case 'organize':
                model('Organize')->getOrganizeDetail($data);
                break;
            case 'organize_require':
                model('OrganizeRequire')->getOrganizeRequireDetail($data);
                break;
            default:
                $this->code = 223;
                $this->msg = '参数非法';
                return $this->result('',$this->code,$this->msg,'json');
                break;
        }
    }
}