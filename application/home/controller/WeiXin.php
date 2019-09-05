<?php
namespace app\home\controller;
class WeiXin {
    protected $WXConfig = array();
    public function __construct(){
        //获取公众号配置信息
        $this->WXConfig = config('WeiXinCg');
    }
    /*1 第一步：用户同意授权，获取code
    2 第二步：通过code换取网页授权access_token
    3 第三步：刷新access_token（如果需要）
    4 第四步：拉取用户信息(需scope为 snsapi_userinfo)*/

    
    /**
     * 获取用户授权, 返回用户信息
     */
    public function getAuthorize() {
        $appid = $this->WXConfig['appid'];
        $userInfo = array();
        //1：网页获取用户授权
        if (!isset($_GET['code'])){
            //触发微信返回code码
            
            $redirect_uri = 'http://' . $_SERVER['SERVER_NAME'] . '/home/wei_xin/getauthorize';
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.urlencode($redirect_uri).
            '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
            Header("Location: $url");
            die;
        }else{
            //获取code码，以获取access_token
            $code = $_GET['code'];
            //包含有access_token的数组
            $result = $this->getAccessToken('get', $code);
            if(!empty($result)) {
                $userInfo = $this->getWxUserInfo($result['access_token']);
                //这里进行微信用户登录或注册操作
                if(!empty($userInfo)) {
                    //检查用户是否登录
                    $user = session('FINANCE_USER');
                    //如果没有登录，则进行登录或注册
                    if(empty($user)) {
                        $superior = cookie('superior');
                        //重新授权时，清空所有session
                        session(null);
                        $model = model('Member');
                        $userInfo['superior'] = $superior;
                        $user = $model->checkLogin($userInfo);
                        session('FINANCE_USER',$user);
                    }
                }
                $add = cookie('Request');
                if(in_array($add['controller'], ['ProjectInfo','RoadShowInfo','ActivityInfo','ActivitySign','ZryxesEffectInfo','ZryxesIntention','YanbaoInfo']) || ($add['controller'] == 'Consultation' && $add['action'] == 'index')){
                    $url = url($add['controller'].'/'. $add['action'] , ['id' => cookie('id')]);
                } else if($add['controller'] == 'InstantMessaging'){
                    $url = url($add['controller'].'/'. $add['action'] , ['uid' => cookie('uid')]);
                }elseif (in_array($add['controller'], ['ZfmxesInfo','ZfmxesIntention'])) {
                    $url = url($add['controller'].'/'. $add['action'] , ['neeq' => cookie('neeq') , 'plannoticeddate' => cookie("plannoticeddate")]);
                }elseif ($add['controller'] == 'GonggaoInfo') {
                    $url = url($add['controller'].'/'. $add['action'] , ['ggid' => cookie('ggid')]);
                }else{
                    $url = url($add['controller'].'/'. $add['action']);
                }
                header("Location: $url");
                die;
            }
        }
    }
    
	/**
	 * 获取用户授权, 返回用户信息
	 */
	public function getAuthorize111() {
		$appid = $this->WXConfig['appid'];
		$userInfo = array();
		//1：网页获取用户授权
		if (!isset($_GET['code'])){
			//触发微信返回code码
            
            $redirect_uri = 'http://' . $_SERVER['SERVER_NAME'] . '/home/wei_xin/getauthorize';
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.urlencode($redirect_uri).
					'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
			Header("Location: $url");
			die;
		}else{
			//获取code码，以获取access_token
			$code = $_GET['code'];
			//包含有access_token的数组
			$result = $this->getAccessToken('get', $code);
			if(!empty($result)) {
				$userInfo = $this->getWxUserInfo($result['access_token']);
				//这里进行微信用户登录或注册操作
				if(!empty($userInfo)) {
					//检查用户是否登录
					$user = session('FINANCE_USER');
					//如果没有登录，则进行登录或注册
					if(empty($user)) {
                        $superior = cookie('superior');
						//重新授权时，清空所有session
						session(null);
						$model = model('Member');
                        $userInfo['superior'] = $superior;
						$user = $model->checkLogin($userInfo);
						session('FINANCE_USER',$user);
					}
				}
                $add = cookie('Request');
                if(in_array($add['controller'], ['ProjectInfo','RoadShowInfo','ActivityInfo','ActivitySign','ZryxesEffectInfo','ZryxesIntention','YanbaoInfo']) || ($add['controller'] == 'Consultation' && $add['action'] == 'index')){
                    $url = url($add['controller'].'/'. $add['action'] , ['id' => cookie('id')]);
                } else if($add['controller'] == 'InstantMessaging'){
                    $url = url($add['controller'].'/'. $add['action'] , ['uid' => cookie('uid')]);
                }elseif (in_array($add['controller'], ['ZfmxesInfo','ZfmxesIntention'])) {
                    $url = url($add['controller'].'/'. $add['action'] , ['neeq' => cookie('neeq') , 'plannoticeddate' => cookie("plannoticeddate")]);
                }elseif ($add['controller'] == 'GonggaoInfo') {
                    $url = url($add['controller'].'/'. $add['action'] , ['ggid' => cookie('ggid')]);
                }else{
                    $url = url($add['controller'].'/'. $add['action']);
                }
                header("Location: $url");
                die;
			}
		}
	}
    /**
	 * 获取或刷新accessToken
	 * @param string $type
	 * @param $param
	 * @return array|mixed
	 */
	private function getAccessToken($type='get', $param) {
		$appid = $this->WXConfig['appid'];
		$secret = $this->WXConfig['appsecret'];
		//获取access_token
		if($type == 'get') {
			$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.
				'&secret='.$secret.
				'&code='.$param.
				'&grant_type=authorization_code';
			$result = $this->httpGet($url);
		}elseif ($type == 'refresh') {
			$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$appid.
					'&grant_type=refresh_token&refresh_token='.$param;
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
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.
				'&openid='.$this->WXConfig['appid'].
				'&lang=zh_CN';
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

