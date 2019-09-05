<?php

namespace app\home\controller;
use think\Controller;
use \sys\RBAC;
use sys\Wechat;
use think\Lang;

class Base extends Controller
{
    //微信配置信息
    protected $WXConfig = array();
    //用户信息
    protected $jsGlobal = array();
    //控制器和方法
    protected $Request = array();
    //微信分享js配置
    protected $WXJSUrl = array();
    //上级用户id
    protected $Superior = 0;
    //项目分享标签
    protected $Sign = '';
    //参数ID
    protected $ID = '';
    protected $UID = '';
    protected $OpenID = '';
    protected $neeq = '';
    protected $plannoticeddate = '';
    protected $ggid = '';

    public function __construct(){
        parent::__construct();
        //获取公众号配置信息
        $this->WXConfig = config('WeiXinCg');
        $this->getAddress();
        //初始化上级id
        $this->initSpread();
        
        //验证是否登录
        $this->isLogin();
        $this->ispullback();
        $this->isFollow();
        $this->isThrough();
        $this->isStudy();
        //创建分享链接
        $this->createShareLink();
        
        //配置微信js分享接口
        $this->readyWxJs();
//        if(cookie('Request') !== null){
//            $url = url(cookie('Request.controller').'/'. cookie('Request.action'));
//            header("Location: $url");
//            die;
//        }
        $this->assign('title0' , 'FA財');
    }
    public function initSpread(){
        $this->Superior         = (int) $this->request->param('upuid',0);
        $this->Sign             = (string) $this->request->param('sign','');
        $this->OpenID           = (string) $this->request->param('openid','');
        $this->ID               = (int) $this->request->param('id',0);
        $this->UID              = (int) $this->request->param('uid',0);
        $this->neeq             = (string) $this->request->param('neeq','');
        $this->plannoticeddate  = (string) $this->request->param('plannoticeddate','');
        $this->ggid             = (string) $this->request->param('ggid','');
        if($this->Superior>0) {
            cookie('superior', $this->Superior);
        }
        if($this->Sign != ''){
            cookie('sign', $this->Sign);
        }
        if($this->OpenID != ''){
            cookie('openid', $this->OpenID);
        }
        if($this->neeq != ''){
            cookie('neeq', $this->neeq);
        }
        if($this->plannoticeddate != ''){
            cookie('plannoticeddate', $this->plannoticeddate);
        }
        if($this->ID > 0){
            cookie('id', $this->ID);
        }
        if($this->UID > 0){
            cookie('uid', $this->UID);
        }
        if($this->ggid != ''){
            cookie('ggid', $this->ggid);
        }
    }

    public function isLogin(){
		$user = session('FINANCE_USER');
		if (empty($user) || !isset($user['pay_activity']) || !isset($user['balance']) || !isset($user['collection_zryxes'])){
            cookie('Request' , $this->Request);
//            $superior = session('superior') !== null ? session('superior') : 0;
            //重新授权时，清空所有session
            session(null);
//            session('superior' , $superior);
            $url = url('WeiXin/getAuthorize');
            header("Location: $url");
            die;
		}
		
		$model    = model("Member");
		$user_ref = $model->getNewInfo($user['openId']);
		
        //初始化用户全局信息
        $member = array();
        $member['uid']                  = $user_ref['uid'];
        $member['userName']             = $user_ref['userName'];
        $member['openId']               = $user_ref['openId'];
        //$member['userSex']              = $user['userSex'];
        $member['userPhone']            = $user_ref['userPhone'];
        $member['userPhoto']            = $user_ref['userPhoto'];
        $member['userType']             = $user_ref['userType'];
        $member['superior']             = $user_ref['superior'];
        $member['collection']           = $user_ref['collection'];
        $member['collection_zfmxes']    = $user_ref['collection_zfmxes'];
        $member['collection_zryxes']    = $user_ref['collection_zryxes'];
        $member['collection_activity']  = $user_ref['collection_activity'];
        $member['pay_activity']         = $user_ref['pay_activity'];
        $member['last_month']           = $user_ref['last_month'];
        $member['last_dividend']        = $user_ref['last_dividend'];
        $member['balance']              = $user_ref['balance'];
        $member['road_show']            = $user_ref['road_show'];
        $member['forward_project']      = $user_ref['forward_project'];
        $member['customerService']      = $user_ref['customerService'];
        $member['pullback']             = $user_ref['pullback'];
        //$member['createTime']           = $user['createTime'];
        //$member['lastTime']             = $user['lastTime'];
        //$member['lastIP']               = $user['lastIP'];
        //$member['status']               = $user['status'];
        $this->jsGlobal['member']       = $member;
        
    }
    
    
    public function ispullback(){
        $this->assign('isback',$this->jsGlobal['member']['pullback']);
    }
    
    
    public function isFollow(){
        $url    = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . getAccessTokenFromFile() . '&openid=' . $this->jsGlobal['member']['openId'] . '&lang=zh_CN';
        $result = httpGet($url);
        $result = json_decode($result, true);
        
        //更新关注公众号的状态
        $model = model("Member");
        $model->updateFollow($this->jsGlobal['member']['openId'],$result['subscribe']);
        
        if ($result['subscribe'] == 0) {
            $this->assign('follow' , false);
			
        } elseif ($result['subscribe'] == 1) {
            $this->assign('follow' , true);
			
        }
		//标题
		$system_config = db("system_config")->where(['sc_id' => 1])->find();
        if (empty($system_config) || $system_config['guanzhu_title'] == '') {
            $this->assign('guanzhu_title', '亲,您还未关注,请先关注哦!');
        } else {
            $module =  request()->module();//获取当前模块
            $controller = request()->controller();//获取当前控制器
            $action = request()->action();//获取当前方法
            $thisUrl = $module.'/'.$controller.'/'.$action;
            if($thisUrl == 'home/Spoiler/index'){
                $this->assign('guanzhu_title','关注FA財，把握一手财经爆料！');
            }else{
                $this->assign('guanzhu_title', $system_config['guanzhu_title']);
            }

            //$this->assign('guanzhu_title', $system_config['guanzhu_title']);
        }
        $gz_img = model('MaterialLibrary')->getMaterialInfoById($system_config['gz_img']);
        $this->assign('gz_img', $gz_img['url']);
		$id = (int)$this->request->param('on',2);
        $this->assign('on',$id);
    }
	
    public function isStudy(){
        $user  = session('FINANCE_USER');
        $model = model("Study");
        $info  = $model->getStudyInfoByOpenId($user['openId']);
        $this->jsGlobal['study'] = $info;
        $this->assign('study' , $info);
    }

    public function isThrough(){
        $user   = session('FINANCE_USER');
        $model  = model("Through");
        $info   = $model->getThroughInfoById($user['uid']);
        if(!empty($info)){
            $model   = model("ThroughDefaultInfo");
            $default = $model->getDefaultNewInfo($user['uid']);
            $info['default'] = $default;
        }
        $this->jsGlobal['through'] = $info;
        $this->assign('through' , $info);
    }
    
    public function createShareLink(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host     = $protocol.$_SERVER['HTTP_HOST'];
        $request  = $_SERVER['REQUEST_URI'];
        if(strpos($request, '?') > 0) {
            $request = $request;
        }else{
            $request = $request;
        }
        $link = $host.$request;
        $this->WXJSUrl['link'] = $link;
        $this->WXJSUrl['root_path'] = $host;
//        $this->assign('link', $link);
    }
    
    
    public function getAddress(){
        $request = \think\Request::instance();
        $this->Request['controller']    = $request->controller();
        $this->Request['action']        = $request->action();
        $this->assign('address' , $this->Request);
    }
    
    
    public function readyWxJs(){
            $AccessToken            = getAccessTokenFromFile();
            $readyWxJs['appId']     = $this->WXConfig['appid'];
            $readyWxJs['timestamp'] = time();
            $readyWxJs['noncestr']  = 'Wm3WZYTPz0wzccnW';
            if(isset($this->jsGlobal['through']) && $this->jsGlobal['through']['status'] == 1){
                if($this->Request['controller'] == 'ProjectInfo' || $this->Request['controller'] == 'PlatForm'){
                    $readyWxJs['url'] = $this->WXJSUrl['link'].'/upuid/'. session('FINANCE_USER.uid').'/sign/project';
                }else {
                    $readyWxJs['url'] = $this->WXJSUrl['link'].'/upuid/'. session('FINANCE_USER.uid');
                }
            } else {
                $readyWxJs['url'] = $this->WXJSUrl['link'];
            }
            $readyWxJs['root_path'] = $this->WXJSUrl['root_path'];
            
            $weObj  = new Wechat($this->WXConfig);
            $result = $weObj->checkAuth('', '', $AccessToken);
            if($ticket = $weObj->getJsTicket()){
                $readyWxJs['jsapi_ticket'] = $ticket;
                $string = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $readyWxJs['jsapi_ticket'], $readyWxJs['noncestr'], $readyWxJs['timestamp'], $this->WXJSUrl['link']);
                $readyWxJs['signature'] = sha1($string);
                $this->assign('wx' , $readyWxJs);
            } else {
                echo 'ticket获取失败';
                return false;
            }
    }
	
}
