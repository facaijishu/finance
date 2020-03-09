<?php 
namespace app\home\controller;
class PublishApi extends BaseApi
{ 
    //用户登录/身份前置判断
    public function __construct()
    {  
       parent::__construct();
       if (!session('FINANCE_USER')) {
           return $this->result('',201,'请先登录','json');   
        } 
     
    }
    
    //发布发现
	public function addPublish()
	{   
        /**
		 * insert Publish表的数据
		 */
        //发布前先判断用户类型
        $userType = session('FINANCE_USER.userType');
        //发布前先判断用户身份
        $roleType = session('FINANCE_USER.role_type');
        //游客和未完善个人信息的绑定用户不能发布一条发现
        if ($userType == 0 || $roleType == 0) {
            return $this->result('',280,'你当前还不是FA財平台合伙人,认证FA財平台合伙人,你的地盘你做主!','json');  
        }
        if ($userType == 1) {
            $this->result('',281,'你的个人信息未完善','json');
        } 
        $content  = trim($this->request->param('content'));
        $tag_name = trim(removeMark($this->request->param('tag_name'),','))?trim(removeMark($this->request->param('tag_name'),',')):'';
        if($content==""){
            $this->result('',281,'请填写需求','json');
        }
        if($tag_name==""){
            $this->result('',281,'请选择标签','json');
        }
		$pb = [];
		$pb['uid']          = session('FINANCE_USER.uid');//用户uid
        $pb['create_time']  = time();//发布的时间
		$pb['content']      = trim($this->request->param('content'));//发布发现的内容
		$pb['img_id']       = trim(removeMark($this->request->param('img_id'),','))?trim(removeMark($this->request->param('img_id'),',')):'';//添加的图片id(可选)
        $tag_name           = trim(removeMark($this->request->param('tag_name'),','))?trim(removeMark($this->request->param('tag_name'),',')):'';//输入个性标签(可选)
        $publish            = model('Publish');
        $publish->addPublishApi($pb,$tag_name);
	}
  
    //发现列表/模糊查询
	public function publishList()
	{
        $keyword = trim($this->request->param('keyword'))?trim($this->request->param('keyword')):'';
       
        if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
        {
            $page = 1;
        }else{
            $page = $this->request->param('page');
        }
       
        $uid = session('FINANCE_USER.uid');
        model('Publish')->publishListSearch($keyword,$page,$uid,4);
	}

    //点赞/取消点赞一条发现
    public function pointPublish()
    {   
        $pub_id = trim($this->request->param('id'))?trim($this->request->param('id')):0;//请求点赞/取消点赞发现的id
        $uid    = session('FINANCE_USER.uid');//当前用户uid
        $point  = model('Point');
        $point->pointPublishApi($uid,$pub_id);
    } 
      
    //反馈一条发现
    public function reportPublish()
    {   
        $pub_id = trim($this->request->param('id'))?trim($this->request->param('id')):0;//反馈的发现的id
        $uid    = session('FINANCE_USER.uid');//当前用户uid
        $report = trim($this->request->param('report'))?trim($this->request->param('report')):''; //接收用户选中的反馈
        model('Report')->reportPublishApi($pub_id,$uid,$report);
    }
      
    //当前发现的详情(发现内容)
    public function publishDetail()
    {
        $userType = session('FINANCE_USER.userType');
        //游客和未完善个人信息的绑定用户不能发布一条发现
        if ($userType == 0) {
            return $this->result('',280,'你当前还不是FA財平台合伙人,认证FA財平台合伙人,你的地盘你做主!','json');  
        }
        if ($userType == 1) {
            $this->result('',281,'你的个人信息未完善','json');
        }
        
        $uid    = session('FINANCE_USER.uid');//当前用户uid
        $pub_id = trim($this->request->param('id'))?trim($this->request->param('id')):0;//当前发现的id
        model('Publish')->showDetail($uid,$pub_id); 
    }
      
    //当前发现的评论
    public function publishComment()
    {
        $uid    = session('FINANCE_USER.uid');//当前用户uid
        $pub_id = $this->request->param('id')?$this->request->param('id'):0;//当前发现的id
        model('Comment')->showComment($uid,$pub_id);
    }
      
    //评论当前发现/对当前发现的评论回复
    public function addCommentPublish()
    {
        $userType = session('FINANCE_USER.userType');
        //游客和未完善个人信息的绑定用户不能发布一条发现
        if ($userType == 0) {
            return $this->result('',280,'你当前还不是FA財平台合伙人,认证FA財平台合伙人,你的地盘你做主!','json');  
        }
        if ($userType == 1) {
            $this->result('',281,'你的个人信息未完善','json');
        }
        
        $data['fid']            = is_numeric($this->request->param('id'))?$this->request->param('id'):0;//评论的上级id
        $data['uid']            = session('FINANCE_USER.uid');//当前用户uid
        $data['content']        = trim($this->request->param('content'))?trim($this->request->param('content')):'';//评论的内容
        $data['pub_id']         = is_numeric($this->request->param('pub_id'))?$this->request->param('pub_id'):0;//评论的来源发现id
        $data['create_time']    = time();//评论的时间
        model('Comment')->addCommentPublishApi($data);
    }
}