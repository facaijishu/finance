<?php 
namespace app\home\controller;
use think\Controller;
use think\Image;
use think\Request;
use \sys\RBAC;
use sys\Wechat;
use think\Lang;
/**
 * 
 */
class BaseApi extends Controller
{   
	//微信配置信息
	protected $WXConfig = array();
	 //用户信息
    protected $jsGlobal = array();
    
	//请求地址
	public function __construct()
	{
		parent::__construct();
		//获取公众号配置信息
        $this->WXConfig = config('WeiXinCg');

	}
    

	/**
	 * 获取前端传递的用户授权码, 返回微信用户授权登录后的用户信息
	 */
	public function getWeixinUserInfo() {
		$referer_url = $this->request->param('referer_url')?$this->request->param('referer_url'):'http://fin.jrfacai.com/static/frontend/index.html';
		//初始化上级用户uid
		$pu = parse_url($referer_url);
		if (isset($pu['query'])) {
			$ru_param = explode('&', $pu['query']);
			foreach ($ru_param as $v) {
				if ($re = strchr($v,'superior=')) {
					$re2 =explode('=', $re);
					$superior = (int)$re2[1];
					if (!cookie('superior')) {
						cookie('superior',$superior);
				    } 
				}
			}
		}
		
		//检查用户是否登录
		$user = session('FINANCE_USER');
		
		//如果登录了直接返回用户信息给客户端
        if (!empty($user)) {
            $model    = model("Member");
            $user_ref = $model->getNewInfo($user['openId']);
            if($user_ref['pullback']==1){
                return $this->result("帐号异常",999,'','json');
            }
        	//用户信息
	        $member = array();
	        $member['uid']                         = $user_ref['uid'];
	        $member['userPhoto']                   = $user_ref['userPhoto'];
	        $member['userName']                    = $user_ref['userName'];
	        $member['realName']                    = $user_ref['realName'];
	        $member['company_jc']                  = $user_ref['company_jc'];
	        $member['position']                    = $user_ref['position'];
	        $member['openId']                      = $user_ref['openId'];
	        $member['userPhone']                   = $user_ref['userPhone'];
	        $member['userType']                    = $user_ref['userType'];
	        $member['role_type']                   = $user_ref['role_type'];
	        $member['qr_code']                     = $user_ref['qr_code'];
	        $member['service_num']                 = $user_ref['service_num'];
            $member['require_num']                 = $user_ref['require_num'];
            $member['my_fans_num']                 = $user_ref['my_fans_num'];
            $member['view_me_num']                 = $user_ref['view_me_num'];
            $member['msg_num']                     = $user_ref['msg_num'];
	        $member['superior']                    = $user_ref['superior'];
	        $member['collection']                  = $user_ref['collection'];
	        $member['collection_zfmxes']           = $user_ref['collection_zfmxes'];
	        $member['collection_zryxes']           = $user_ref['collection_zryxes'];
	        $member['collection_activity']         = $user_ref['collection_activity'];
	        $member['collection_study']            = $user_ref['collection_study'];
	        $member['pay_activity']                = $user_ref['pay_activity'];
	        $member['last_month']                  = $user_ref['last_month'];
	        $member['last_dividend']               = $user_ref['last_dividend'];
	        $member['balance']                     = $user_ref['balance'];
	        $member['road_show']                   = $user_ref['road_show'];
	        $member['forward_project']             = $user_ref['forward_project'];
	        $member['customerService']             = $user_ref['customerService'];
	        $member['is_first_match']              = $user_ref['is_first_match'];
            $member['company']                     = $user_ref['company'];
	        $member['weixin']                      = $user_ref['weixin'];
	        $member['email']                       = $user_ref['email'];
	        $member['company_address']             = $user_ref['company_address'];
	        $member['website']                     = $user_ref['website'];
	        $member['person_label']                = $user_ref['person_label'];
	        $member['collection_project_require']  = $user_ref['collection_project_require'];
			$member['collection_organize']         = $user_ref['collection_organize'];
			$member['collection_organize_require'] = $user_ref['collection_organize_require'];
			$member['pullback']                    = $user_ref['pullback'];
			$member['is_follow']                   = $user_ref['is_follow'];
	        $this->jsGlobal['member']              = $member;
	        
	        $this->isFollow();
	        
	        return $this->result($member,200,'','json');
        }

        
		$userInfo = array();
		//1：网页获取用户授权
		if (!isset($_GET['code'])){
		   //如果没有,需要跳转的微信页面地址响应回来给前端(网页获取用户授权码失败)
		   $appid = $this->WXConfig['appid'];	
           // return $this->result('',235,'网页授权码不存在','json');
           //触发微信返回code码
            $redirect_uri = 'http://' . $_SERVER['SERVER_NAME'] . '/home/base_api/getWeixinUserInfo?referer_url='.urlencode($referer_url);
			$url          = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
			$weixin_auth_url['weixin_auth_url'] = $url;
            return $this->result($weixin_auth_url,235,'用户未授权登录','json');
		}
		//获取code码，以获取access_token
		$code   = $_GET['code'];
		//包含有access_token的数组
		$result = $this->getAccessToken('get', $code);
		if(!empty($result)) {
			//使用refresh_token进行刷新
		    $refresh_result = $this->getAccessToken('refresh', $result['refresh_token']);
		    $userInfo       = $this->getWxUserInfo($refresh_result['access_token']);
			//这里进行微信用户登录或注册操作
			if(!empty($userInfo)) {
					//取出cookie中保存的上级用户
                    $superior = cookie('superior');
					//重新授权时，清空所有session
					session(null);
					$model = model('Member');
                    $userInfo['superior'] = $superior;
                    //登录验证
					$user = $model->checkLogin($userInfo);
					
					//用户信息
			        $member = array();
			        $member['uid']                           = $user['uid'];
			        $member['userPhoto']                     = $user['userPhoto'];
			        $member['userName']                      = $user['userName'];
			        $member['realName']                      = $user['realName'];
			        $member['company_jc']                    = $user['company_jc'];
			        $member['position']                      = $user['position'];
			        $member['openId']                        = $user['openId'];
			        $member['userPhone']                     = $user['userPhone'];
			        $member['userType']                      = $user['userType'];
			        $member['role_type']                     = $user['role_type'];
			        $member['qr_code']                       = $user['qr_code'];
			        $member['service_num']                   = $user['service_num'];
	                $member['require_num']                   = $user['require_num'];
	                $member['my_fans_num']                   = $user['my_fans_num'];
	                $member['view_me_num']                   = $user['view_me_num'];
	                $member['msg_num']                       = $user['msg_num'];
			        $member['superior']                      = $user['superior'];
			        $member['collection']                    = $user['collection'];
			        $member['collection_zfmxes']             = $user['collection_zfmxes'];
			        $member['collection_zryxes']             = $user['collection_zryxes'];
			        $member['collection_activity']           = $user['collection_activity'];
			        $member['collection_study']              = $user['collection_study'];
			        $member['pay_activity']                  = $user['pay_activity'];
			        $member['last_month']                    = $user['last_month'];
			        $member['last_dividend']                 = $user['last_dividend'];
			        $member['balance']                       = $user['balance'];
			        $member['road_show']                     = $user['road_show'];
			        $member['forward_project']               = $user['forward_project'];
			        $member['customerService']               = $user['customerService'];
			        $member['is_first_match']                = $user['is_first_match'];
			        $member['company']                       = $user['company'];
			        $member['weixin']                        = $user['weixin'];
			        $member['email']                         = $user['email'];
			        $member['company_address']               = $user['company_address'];
			        $member['website']                       = $user['website'];
			        $member['person_label']                  = $user['person_label'];
			        $member['collection_project_require']    = $user['collection_project_require'];
			        $member['collection_organize']           = $user['collection_organize'];
			        $member['collection_organize_require']   = $user['collection_organize_require'];
			        $member['pullback']                      = $user['pullback'];
			        $member['is_follow']                     = $user['is_follow'];
			        $this->jsGlobal['member']                = $member;
			        session('FINANCE_USER',$member);
			        header("Location: ".$referer_url);
					
    			        if($user['pullback']==1){
    			            return  $this->result('',999,'用户已禁用','json');
    			        }
  
			}else{

				return  $this->result('',237,'获取微信用户信息失败','json');
			}
            
		}else{

			return $this->result('',236,'获取或刷新微信网页授权accessToken失败','json');
		}
	}
	
	/**
     *
     *
     * FA財4.0API
     */

	//文件上传接口
    public function upload(){
        $origin = input('origin');
        $file   = request()->file("file");
        if($file){
            $info = $file->getInfo();
            $name = $info['name'];
            if (count(explode(',', $name)) > 1) {
                $this->result('', 219, '上传文件名称不规范', 'json');
            }
            $type = strtolower(substr($name,strrpos($name,'.')+1));
            if(in_array(strtolower($type), ['jpg' , 'jpeg' , 'gif' , 'png'])){
                $path           = $origin . DS . 'img';
                $path_cut       = $origin . DS . 'img_cut';
                $path_compress  = $origin . DS . 'img_compress';
            }elseif (in_array($type, ['pdf'])) {
                $path = $origin . DS . 'pdf';
            }elseif (in_array($type, ['mkv','rmvb','mp4'])) {
                $path = $origin . DS . 'video';
            }
            $dir = ROOT_PATH . 'public' . DS . 'uploads' . DS . $path;
            if(!is_dir($dir)){
                mkdir($dir , 0777, true);
            }
            $result = $file->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . $path);
            if(isset($path_cut)){
                $dir = ROOT_PATH . 'public' . DS . 'uploads' . DS . $path_cut;
                if(!is_dir($dir.DS.substr($result->getSaveName(), 0 , 8))){
                    mkdir($dir.DS.substr($result->getSaveName(), 0 , 8) , 0777, true);
                }
                $image = Image::open($result);
                $width = $image->width();
                $height = $image->height();
                if($width > $height){
                    $image->crop(floor($height/2)*2, floor($height/2)*2, $width/2-floor($height/2), $height%2)->save($dir . DS . $result->getSaveName());
                } else {
                    $image->crop(floor($width/2)*2, floor($width/2)*2, $width%2, $height/2-floor($width/2))->save($dir . DS . $result->getSaveName());
                }
            }
            if(isset($path_compress)){
                $dir = ROOT_PATH . 'public' . DS . 'uploads' . DS . $path_compress;
                if(!is_dir($dir.DS.substr($result->getSaveName(), 0 , 8))){
                    mkdir($dir.DS.substr($result->getSaveName(), 0 , 8) , 0777, true);
                }
                $image = Image::open($result);
                $image->thumb(150,150)->save($dir . DS . $result->getSaveName());
            }
            if($result){
                $model = model("material_library");
                $uid = session('FINANCE_USER.uid');
                if(in_array($type, ['jpg' , 'jpeg' , 'gif' , 'png'])){
                    $id = $model->createMaterial($path , $path_cut , $path_compress , $result,$uid);
                } else {
                    $id = $model->createMaterial($path , '' , '' , $result,$uid);
                }
                if($id){
                    $data['id'] = $id;
                    $data['url'] = $path . DS . $result->getSaveName();
                    $this->result($data ,200 , '上传成功' , 'json');
                } else {
                    $error = $model->getError() ? $model->getError() : '保存上传材料失败';
                    $this->result('', 220, $error, 'json');
                }
            }else{
                $this->result('', 221, '上传失败', 'json');
            }
        } else {
            $this->result('', 222, '上传文件不存在', 'json');
        }
    }

    //将指定网页通过微信一键分享转发
    public function readyWxJs(){
    	    $link          = input('link');
            $AccessToken   = getAccessTokenFromFile();
            $readyWxJs['appId']     = $this->WXConfig['appid'];
            $readyWxJs['timestamp'] = time();
            $readyWxJs['noncestr']  = 'Wm3WZYTPz0wzccnW';

            $weObj  = new Wechat($this->WXConfig);
            $result = $weObj->checkAuth('', '', $AccessToken);
            if($ticket = $weObj->getJsTicket()){
                $string = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $ticket, $readyWxJs['noncestr'], $readyWxJs['timestamp'], $link);
                $readyWxJs['signature'] = sha1($string);
                return $this->result($readyWxJs,200,'请求成功','json');
            }

            return $this->result('',282,'ticket获取失败','json');
    }


    //判断用户是否关注公众号
    public function isFollow()
    {   
 
    	$url    = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . getAccessTokenFromFile() . '&openid='.$this->jsGlobal['member']['openId'].'&lang=zh_CN';
        $result = httpGet($url);
        $result = json_decode($result, true);
        
        //更新关注公众号的状态
        $model = model("Member");
        $model->updateFollow($this->jsGlobal['member']['openId'],$result['subscribe']);
        
        
        return $result['subscribe'];
 
    }
    
	/**
	 * 获取或刷新accessToken
	 * @param string $type
	 * @param $param
	 * @return array|mixed
	 */
	private function getAccessToken($type='get', $param) {
		$appid  = $this->WXConfig['appid'];
		$secret = $this->WXConfig['appsecret'];
		//获取access_token
		if($type == 'get') {
			$url    = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$param.'&grant_type=authorization_code';
			$result = $this->httpGet($url);
		}elseif ($type == 'refresh') {
			$url    = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$appid.'&grant_type=refresh_token&refresh_token='.$param;
			$result = $this->httpGet($url);
		}
		$result = json_decode($result, true);
		return $result == isset($result['errcode']) ? array() : $result;
	}
	
    /**
	 * 取得微信用户信息
	 * @param $access_token
	 * @return array|mixed
	 */
	private function getWxUserInfo($access_token) {
		$url    = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$this->WXConfig['appid'].'&lang=zh_CN';
		$result = $this->httpGet($url);
		$result = json_decode($result, true);
		return $result == isset($result['errcode']) ? array() : $result;
	}

	
	private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
//		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
//		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	} 
}
