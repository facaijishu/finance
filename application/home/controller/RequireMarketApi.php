<?php 
namespace app\home\controller;
/**
 * 
 */
class RequireMarketApi extends BaseApi
{   
	//用户登录/身份前置判断(游客不可进)
    public function __construct()
    {  
       parent::__construct();
       if (!session('FINANCE_USER')) {
               // //发布前先判断用户类型
               // $userType = session('FINANCE_USER.userType');
               // //游客和未完善个人信息的绑定用户不能发布一条项目
               // if ($userType == 0) {
                    
               //      return $this->result('',280,'你当前还不是FA財平台合伙人,认证FA財平台合伙人,你的地盘你做主!','json');  
               // }
            return $this->result('',201,'请先登录','json');
        } 
        
    }
    
    
    //市场-精选栏
    public function getCareMarketList()
    {    
        //用户身份
        $role_type      = session('FINANCE_USER.role_type');
        //用户uid
        $data['uid']    = session('FINANCE_USER.uid');
        //当前页
        $data['page']   = is_numeric($this->request->param('page'))?$this->request->param('page'):1;
        //每页显示条数
        $data['length'] = 12;
        //偏移量
        $data['offset'] = (intval($data['page'])-1)*$data['length'];
 
        switch (intval($role_type)) {
            case 1:
                model('ProjectRequire')->getCareProjectAndProjectRequireMarketList($data);
                break;
            case 2:
                model('OrganizeRequire')->getCareOrganizeAndOrganizeRequireMarketList($data);
                break;
            default:
                model('ProjectRequire')->getCareProjectAndProjectRequireMarketList($data);
                break;
        }
    }
    
       
    //市场-股权栏
    public function getStockRightMarketList()
    {   
        //用户身份
        $role_type      =  session('FINANCE_USER.role_type');
        //用户uid
        $data['uid']    = session('FINANCE_USER.uid');
        //当前页
        $data['page']   = is_numeric($this->request->param('page'))?$this->request->param('page'):1;
        //每页显示条数
        $data['length'] = 12;
        //偏移量
        $data['offset'] = (intval($data['page'])-1)*$data['length'];  
        switch (intval($role_type)) {
            case 1:
                model('ProjectRequire')->getStockRightProjectRequireMarketList($data);
                break;
            case 2:
                 model('OrganizeRequire')->getStockRightOrganizeRequireMarketList($data);
                 break;
            default:
                 model('ProjectRequire')->getStockRightProjectRequireMarketList($data);
                 break;
            }   
    }

       //市场-债权栏
       public function getClaimsMarketList()
       {    
            //用户身份
            $role_type =  session('FINANCE_USER.role_type');
            //用户uid
            $data['uid'] = session('FINANCE_USER.uid');
            //当前页
            $data['page'] = is_numeric($this->request->param('page'))?$this->request->param('page'):1;
            //每页显示条数
            $data['length'] = 12;
            //偏移量
            $data['offset'] = (intval($data['page'])-1)*$data['length']; 
           switch (intval($role_type)) {
                     case 1:
                      model('ProjectRequire')->getClaimsProjectRequireMarketList($data);
                     break;
                     case 2:
                      model('OrganizeRequire')->getClaimsRightOrganizeRequireMarketList($data);
                     break;
                     default:
                      model('ProjectRequire')->getClaimsRightProjectRequireMarketList($data);
                     break;
              } 
           // model('ProjectRequire')->getClaimsProjectRequireAndOrganizeRequireMarketList($data);      
       }

        //市场-创业栏
       public function getChuangYeMarketList()
       {    
            //用户身份
            $role_type =  session('FINANCE_USER.role_type');
            //用户uid
            $data['uid'] = session('FINANCE_USER.uid');
            //当前页
            $data['page'] = is_numeric($this->request->param('page'))?$this->request->param('page'):1;
            //每页显示条数
            $data['length'] = 12;
            //偏移量
            $data['offset'] = (intval($data['page'])-1)*$data['length']; 
            switch (intval($role_type)) {
                     case 1:
                      model('ProjectRequire')->getChuangYeProjectRequireMarketList($data);
                     break;
                     case 2:
                      model('OrganizeRequire')->getChuangYeRightOrganizeRequireMarketList($data);
                     break;
                     default:
                      model('ProjectRequire')->getChuangYeRightProjectRequireMarketList($data);
                     break;
              } 
            // model('ProjectRequire')->getChuangYeProjectRequireAndOrganizeRequireMarketList($data);  
       }

        //市场-上市栏
       public function getListMarketList()
       {    
            //用户身份
            $role_type =  session('FINANCE_USER.role_type');
            //用户uid
            $data['uid'] = session('FINANCE_USER.uid');
            //当前页
            $data['page'] = is_numeric($this->request->param('page'))?$this->request->param('page'):1;
            //每页显示条数
            $data['length'] = 12;
            //偏移量
            $data['offset'] = (intval($data['page'])-1)*$data['length']; 
           //偏移量
           $data['offset'] = (intval($data['page'])-1)*$data['length'];  
           switch (intval($role_type)) {
                     case 1:
                      model('ProjectRequire')->getListProjectRequireMarketList($data);
                     break;
                     case 2:
                      model('OrganizeRequire')->getListRightOrganizeRequireMarketList($data);
                     break;
                     default:
                      model('ProjectRequire')->getListRightProjectRequireMarketList($data);
                     break;
              }
           // model('ProjectRequire')->getListProjectRequireAndOrganizeRequireMarketList($data);   
       }
        //市场-其他栏(配资业务+金融产品)
       public function getOtherMarketList()
       {    
            //用户身份
            $role_type =  session('FINANCE_USER.role_type');
            //用户uid
            $data['uid'] = session('FINANCE_USER.uid');
            //当前页
            $data['page'] = is_numeric($this->request->param('page'))?$this->request->param('page'):1;
            //每页显示条数
            $data['length'] = 12;
            //偏移量
            $data['offset'] = (intval($data['page'])-1)*$data['length']; 
           //偏移量
           $data['offset'] = (intval($data['page'])-1)*$data['length'];  
           switch (intval($role_type)) {
                     case 1:
                      model('ProjectRequire')->getOtherProjectRequireMarketList($data);
                     break;
                     case 2:
                      model('OrganizeRequire')->getOtherRightOrganizeRequireMarketList($data);
                     break;
                     default:
                      model('ProjectRequire')->getOtherRightProjectRequireMarketList($data);
                     break;
              } 
           // model('ProjectRequire')->getOtherProjectRequireAndOrganizeRequireMarketList($data);   
       }	
}