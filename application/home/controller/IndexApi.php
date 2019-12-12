<?php
namespace app\home\controller;

class IndexApi extends BaseApi
{   
	public function __construct()
	{
		parent::__construct();
        if (!session('FINANCE_USER')) {
              return $this->result('',201,'请先登录','json');                        
        } 
	}
	//首页搜索-用户输入关键字
	public function indexSearch()
	{
       $keyword = $this->request->param('keyword')?$this->request->param('keyword'):'';
       $keyword = trim($keyword);
       if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }

       $uid = session('FINANCE_USER.uid');
       
       if($keyword!=''){
           //保存用户搜索内容
           $data = [];
           $data['uid']      = $uid;
           $data['keyword']  = $keyword;
           $data['add_time'] = time();
           $data['type']     = 1;
           model('SearchKeywords')->createSearchKeywords($data);
       }
       
        //每页显示数
       $length = 4;
       //计算偏移量
       $offset = ($page-1)*$length;
       $model  = model('ProjectRequire');
       $model->getRequireLikeKeyword($keyword,$page,$uid,$length,$offset);

	}
    //首页搜索-指定内容-业务
    public function indexSearchBusiness()
    {  
       $keyword = $this->request->param('keyword')?$this->request->param('keyword'):'';
       $keyword = trim($keyword);
       //当前页码
       if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
      
       //当前登录的用户
       $uid = session('FINANCE_USER.uid');
       
       if($keyword!=""){
           //保存用户搜索内容
           $data = [];
           $data['uid']      = $uid;
           $data['keyword']  = $keyword;
           $data['add_time'] = time();
           $data['type']     = 2;
           model('SearchKeywords')->createSearchKeywords($data);
       }
       
        //每页显示数
       $length = 4;
       //计算偏移量
       $offset = ($page-1)*$length; 
       $model = model('ProjectRequire');
       $model->getIndexSearchBusiness($keyword,$page,$uid,$length,$offset);
       

    }
    //首页搜索-指定内容-项目
    public function indexSearchProject()
    {  
       $keyword = $this->request->param('keyword')?$this->request->param('keyword'):'';
       $keyword = trim($keyword);
       //当前页码
       if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
      
       //当前登录的用户
       $uid = session('FINANCE_USER.uid');
       
       if($keyword!=''){
           //保存用户搜索内容
           $data = [];
           $data['uid']      = $uid;
           $data['keyword']  = $keyword;
           $data['add_time'] = time();
           $data['type']     = 3;
           model('SearchKeywords')->createSearchKeywords($data);
       }
       
       
        //每页显示数
       $length = 4;
       //计算偏移量
       $offset = ($page-1)*$length; 
       $model = model('ProjectRequire');
       $model->getIndexSearchProject($keyword,$page,$uid,$length,$offset);
       
    }
    //首页搜索-指定内容-资金
    public function indexSearchOrganize()
    {  
       $keyword = $this->request->param('keyword')?$this->request->param('keyword'):'';
       $keyword = trim($keyword);
       //当前页码
       if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
      
       //当前登录的用户
       $uid = session('FINANCE_USER.uid');
       
       if($keyword!=''){
           //保存用户搜索内容
           $data = [];
           $data['uid']      = $uid;
           $data['keyword']  = $keyword;
           $data['add_time'] = time();
           $data['type']     = 4;
           model('SearchKeywords')->createSearchKeywords($data);
       }
       
        //每页显示数
       $length = 4;
       //计算偏移量
       $offset = ($page-1)*$length; 
       $model = model('OrganizeRequire');
       $model->getIndexSearchOrganize($keyword,$page,$uid,$length,$offset);
       
    }
    
     //首页搜索-指定内容-行业
    public function indexSearchIndustry()
    {  
       $keyword = $this->request->param('keyword')?$this->request->param('keyword'):'';
       $keyword = trim($keyword);
       //当前页码
       if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
      
       //当前登录的用户
       $uid = session('FINANCE_USER.uid');
       
       if($keyword!=''){
           //保存用户搜索内容
           $data = [];
           $data['uid']      = $uid;
           $data['keyword']  = $keyword;
           $data['add_time'] = time();
           $data['type']     = 5;
           model('SearchKeywords')->createSearchKeywords($data);
       }
       
        //每页显示数
       $length = 4;
       //计算偏移量
       $offset = ($page-1)*$length; 
       $model  = model('ProjectRequire');
       $model->getIndexSearchIndustry($keyword,$page,$uid,$length,$offset);
       
    }
    //首页搜索-指定内容-发现(財圈)
    public function indexSearchPublish()
    {  
       $keyword = $this->request->param('keyword')?$this->request->param('keyword'):'';
       $keyword = trim($keyword);
       //当前页码
       if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
      
       //当前登录的用户
       $uid = session('FINANCE_USER.uid');
       
       if($keyword!=''){
           //保存用户搜索内容
           $data = [];
           $data['uid']      = $uid;
           $data['keyword']  = $keyword;
           $data['add_time'] = time();
           $data['type']     = 6;
           model('SearchKeywords')->createSearchKeywords($data);
       }
       
        //每页显示数
       $length = 4;
       //计算偏移量
       $offset = ($page-1)*$length; 
       $model = model('Publish');
       $model->getIndexSearchPublish($keyword,$page,$uid,$length,$offset);
       
    }
    //首页搜索-指定内容-人脉
    public function indexSearchConnections()
    {  
       $keyword = $this->request->param('keyword')?$this->request->param('keyword'):'';
       $keyword = trim($keyword); 
       //当前页码
       if(!is_numeric($this->request->param('page')) || ($this->request->param('page')<=0) || ($this->request->param('page') == ''))
       {
          $page = 1;
       }else{
          $page = $this->request->param('page');
       }
      
       //当前登录的用户
       $uid = session('FINANCE_USER.uid');
       
       if($keyword!=''){
           //保存用户搜索内容
           $data = [];
           $data['uid']      = $uid;
           $data['keyword']  = $keyword;
           $data['add_time'] = time();
           $data['type']     = 7;
           model('SearchKeywords')->createSearchKeywords($data);
       }
       
        //每页显示数
       $length = 4;
       //计算偏移量
       $offset = ($page-1)*$length; 
       $model = model('Through');
       $model->getIndexSearchConnections($keyword,$page,$uid,$length,$offset);
    }
    
    //搜索历史词条
    public function indexSearchHistory()
    {
        $uid    = session('FINANCE_USER.uid');
        $model  = model("SearchKeywords");
        $search = $model->getSearchByUidApi($uid);
        $this->result($search['data'],$search['code'],$search['msg'],'json');   
    }
    
    //获取历史词条总数
    public function getSearCount()
    {
        $uid        = session('FINANCE_USER.uid');
        $model      = model("SearchKeywords");
        $res        = $model->getSearchCountApi($uid);
        $this->result($res['data'],$res['code'],$res['msg'],'json');
    }
    
    //删除历史搜索词条
    public function delSearchHistory()
    {
        $keywords   = trim($this->request->param('keywords'))?trim($this->request->param('keywords')):'';
        $uid        = session('FINANCE_USER.uid');
        $model      = model("SearchKeywords");
        $res        = $model->delSearchByUidApi($uid,$keywords);
        $this->result('',$res['code'],$res['msg'],'json');
    }
    
    //删除全部历史搜索词条
    public function delAllSearchHistory()
    {
        $keywords   = trim($this->request->param('keywords'))?trim($this->request->param('keywords')):'';
        $uid        = session('FINANCE_USER.uid');
        $model      = model("SearchKeywords");
        $res        = $model->delAllSearchByUidApi($uid);
        $this->result('',$res['code'],$res['msg'],'json');
    }
    
	//首页轮播图
    public function displayRotaryMap()
    {
        $model = model("CarouselFigure");
        $model->getCarouselFigureListApi();
        
    }
    //首页精选前三条展示
    public function displayCareList()
    {   
        $role_type  = session('FINANCE_USER.role_type');
        $uid        = session('FINANCE_USER.uid');
        $length     = 5;
        switch (intval($role_type)) {
            case 1:
                //资金方
                $model = model("Project");
                $info = $model->getProjectLimitListApi($role_type,$uid,$length);
                break;
            case 2:
                //项目方
                $model = model("Organize");
                $info  = $model->getOrganizeLimitListApi($role_type,$uid,$length);
                break;
            default:
                $model = model("Project");
                $info  = $model->getProjectLimitListApi($role_type,$uid,$length);
                break;
        }
    	$this->result($info['data'],$info['code'],$info['msg'],'json');       
    }
    
    //首页智能推荐前八条展示
    public function displayBrainPower()
    {
       $role_type =  session('FINANCE_USER.role_type');
       $uid =  session('FINANCE_USER.uid');
       $length = 8;
       switch (intval($role_type)) {
            case 1:
                //资金方
                $model = model("ProjectRequire");
                $model->getProjectRequireLimitListApi($role_type,$uid,$length);
                break;
            case 2:
                //项目方
                $model = model("OrganizeRequire");
                $model->getOrganizeRequireLimitListApi($role_type,$uid,$length);
                break;
            default:
                $model = model("ProjectRequire");
                $model->getProjectRequireLimitListApi($role_type,$uid,$length);
                break;
        }


    }
    //热门头条
    public function displayHotHeadline()
    {
    	$model = model("News");
    	$model->getList(5);
    }
    //客服图标的跳转链接
    public function serviceUrl()
    {
        
        $role_type =  session('FINANCE_USER.role_type');
        // dump($role_type);die;
        $uid =  session('FINANCE_USER.uid');
        $length = 1;
        $pre_url =  input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME').'/home/';
        switch (intval($role_type)) {
            case 1:
                //资金方
                $model = model("Project");
                $info = $model->getProjectLimitListApi($role_type,$uid,$length);
                if (empty($info['data'])) {
                  $info2 = $model->field('pro_id')
                                 ->where(['status'=>2,'flag'=>1])
                                 ->order('pro_id desc')
                                 ->find();               
                  $id = $info2['pro_id'];
                } else {
                   //拼接url
                  $id = $info['data'][0]['id'];
                  
                }
                if ($id) {
                     $url = $pre_url.'customer_service/index/id/'.$id;
                } else {
                     $url = '';
                }
            
                break;
            case 2:
                //项目方
                $model = model("Organize");
                $info  = $model->getOrganizeLimitListApi($role_type,$uid,$length);
                if (empty($info['data'])) {
                    $info2 = $model->field('org_id')
                                   ->where(['status'=>2,'flag'=>1])
                                   ->order('org_id desc')
                                   ->find();
                    $id = $info2['org_id'];
                } else {
                     //拼接url
                    $id = $info['data'][0]['id'];
                }

                if ($id) {
                   $url = $pre_url.'organize_info/service/org_id/'.$id;
                } else {
                   $url = '';
                }
                break;
            default:
                $model = model("Project");
                $info  = $model->getProjectLimitListApi($role_type,$uid,$length);
                if (empty($info['data'])) {
                  $info2 = $model->field('pro_id')
                                 ->where(['status'=>2,'flag'=>1])
                                 ->order('pro_id desc')
                                 ->find();               
                  $id = $info2['pro_id'];
                } else {
                   //拼接url
                  $id = $info['data'][0]['id'];
                  
                }
                if ($id) {
                     $url = $pre_url.'customer_service/index/id/'.$id;
                } else {
                     $url = '';
                }
                
                break;
        }
        
        $this->result($url,200,'','json'); 
    }
}