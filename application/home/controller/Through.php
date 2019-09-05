<?php
namespace app\home\controller;

use \common\Sms;

class Through extends Base{
    public function index(){
        $info = $this->jsGlobal;
        $this->assign('info' , $info);
        $this->assign('title' , '- 认证中心');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最严谨的认证过程!');
        return view();
    }
    //名片上传
    public function upload(){
        $file = $_FILES;
        if($file){
            $name = explode('.', $file['cardup']['name']);
            $type = $name[count($name)-1];
            $filename = date('ymd').rand(1000, 9999).'.'.$type;
            if(in_array(strtolower($type), ['jpg' , 'jpeg' , 'gif' , 'png'])){
                $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'home' . DS . 'card' . DS;
                if(!is_dir($path)){
                    mkdir($path , 0777 , true);
                }
                $result = move_uploaded_file($file['cardup']['tmp_name'], $path.$filename);
                if($result){
                    $data['name'] = $filename;
                    $this->result($data ,1 , '上传成功' , 'json');
                }else{
                    $this->result('', 0, '上传失败', 'json');
                }
            } else {
                $this->result('' ,0 , '上传文件格式不正确' , 'json');
            }
        } else {
            $this->result('' ,0 , '图片不存在' , 'json');
        }
    }
    
    
    //添加认证
    public function add(){
        $data = input();
        /**
         * @param:先去判断是否关注公众号，若没关注，请先关注公众号
         * @time:2018/07/06
         * @auhtor:allen
         */
		$url        = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . getAccessTokenFromFile() . '&openid=' . $this->jsGlobal['member']['openId'] . '&lang=zh_CN';
        $result     = httpGet($url);
        $result     = json_decode($result, true);
        
        if ($result['subscribe'] == 0) {
            //$this->assign('follow' , false);
			//没关注
			$this->result('警告', 404, '请先关注公众号', 'json');
			exit;
        }
        if(isset($data['code'])){
            $user     = session('FINANCE_USER');
            $modelM   = model("MemberCode");
            $res      = $modelM->isValidCode($user['uid'],$data['code']);
            
            //验证成功
            if($res['code']==200){
            //if($data['code'] == session('code')){
                session('code', null);
                $model = model("through");
                if($model->createThrough($data)){
                    $model1 = model("Member");
                    $info   = $model1->where(['uid' => $user['uid']])->find();
                    if ($info['customerService'] != 0) {
                        $result = model('SelfCustomerService')->getCustomerServiceById($info['customerService']);
                    }else {
                        $result = model('SelfCustomerService')->getRandomCustomerService($user['uid']);
                    }
					
                    if(!empty($info['lastTime'])){
                        $info['lastTime'] = date('Y-m-d H:i:s',$info['lastTime']);
                    }
                    $result_m = model('Binding')->sendProjectInfo($result['real_name'],$info['userPhone'],$info['lastTime'],"绑定用户",$result['kf_openId'],$user['uid']);
					
                    if(!$result_m){
                        $data = [];
                        $this->result($data, 1, '发送消息模板失败', 'json');
                    }
                    session('FINANCE_USER' , $info);
                    $data = [];
                    $data['url'] = url('Through/succeed');
                    $this->result($data, 1, '添加认证信息成功', 'json');
                }else{
                    $error = $model->getError() ? $model->getError() : '添加认证失败';
                    $this->result('', 0, $error, 'json');
                }
            } else {
                //$this->result('', 0, '验证码不正确', 'json');
                $this->result('', 0, $res['msg'], 'json');
            }
        } else {
            $model = model("through");
            if($model->createThrough($data)){
                $model = model("Member");
                $user = session('FINANCE_USER');
                $info = $model->where(['uid' => $user['uid']])->find();

                session('FINANCE_USER' , $info);
                session('code',null);
                $data = [];
                $data['url'] = url('Through/succeed');
                $this->result($data, 1, '添加认证信息成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '添加认证失败';
                $this->result('', 0, $error, 'json');
            }
        }
    }
    
    
    //添加认证成功
    public function succeed(){
        $this->assign('title' , '- 认证中心');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最严谨的认证过程!');
        return view();
    }
    
    
    //获取手机验证码
    public function getNumber(){
        $mobile    = $this->request->param('phone/s');
		$id        = $this->request->param("id");
        $partten   = '/^(0|86|17951)?(13[0-9]|15[012356789]|17[0-9]|18[0-9]|14[57])[0-9]{8}$/';
        if(empty($mobile) || preg_match($partten, $mobile) == 0){
            $this->result('', -1, '手机号码不正确', 'json');
        }
		//判断该手机号是否已经注册过
		if(!empty($id)){
			$sult  = model("through")->where("phone = ".$mobile." and status = 1")->find();
			if(!empty($sult)){
				$t = [];
				$this->result($t, 1, "该手机号已经注册过", 'json');
			}
		}
		
        $number  = rand(100000, 999999);
        //插入用户验证码表
        $modelM             = model("MemberCode");
        $data['uid']        = session('FINANCE_USER.uid');
        $data['reg_code']   = $number;
        $res = $modelM->createCode($data);
        
        if($res){
            $sms    = new \common\Sms();
            $tem_id = "SMS_170180037";
            $array  = $sms->sendSms($mobile,$number,$tem_id);
            $array  = json_decode(json_encode($array) , true);
            if($array['Code'] == "OK"){
                //插入短信发送表
                $text    = "验证码".$number.",您正在注册成为新会员";
                $model   = model("SendSms");
                $smsId   = $model->addSms($mobile,$text,1);
                $this->result('', 200, '发送成功', 'json');
            }else{
                //插入短信发送表
                $text    = "验证码".$number.",您正在注册成为新会员";
                $model   = model("SendSms");
                $smsId   = $model->addSms($mobile,$text,2,$array['Message']);
                $this->result('', 1, "验证码短信异常，请稍后再试", 'json');
            }
        }else{
            $this->result('', 1, "验证码获取失败，请重新获取", 'json');
        }
        
        
        
        
        /*
        $apikey = "b4d4e18d335a960c008deca958c37326"; 
        session('code', $number);
        $text="【FA財】您的验证码是".$number;
        vendor("Passport.Sms");
        $sms = new \Sms('ac67816e0f384393545628fc31dc3b17');
        $array = $sms->send($mobile, $text, null);
        $array = json_decode(json_encode($array) , true);
        if($array['code'] == 0){
            if($array['data'][0]['sendState'] == 'SUCCESS'){
                $this->result($array, 0, '发送成功', 'json');
            } else {
                $this->result($array, 1, $array['data'][0]['reason'], 'json');
            }
        } else {
            $this->result($array, 1, $array['msg'], 'json');
        }
        
        */
    }
}
