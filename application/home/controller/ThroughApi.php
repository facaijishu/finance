<?php 
namespace app\home\controller;

use \common\Sms;
use app\home\validate\Through;


class ThroughApi extends BaseApi
{   
    //用户登录/身份前置判断
    public function __construct()
    {  
       parent::__construct();
       if (!session('FINANCE_USER')) {
           return $this->result('',201,'请先登录','json');   
        } 
     
    }
	//用户认证成为绑定用户
    public function through()
	{    
        //当前用户
	    $user = session('FINANCE_USER');//临时测试用户登录后获取的微信信息,最终上线后替换
        //当前用户uid
        $data['uid'] = $user['uid']?$user['uid']:0;
	    //当前用户提交的身份选择
	    $data['role_type'] = $this->request->param('role_type')?$this->request->param('role_type'):0;
	    //当前用户提交的姓名
	    $data['real_name'] = $this->request->param('real_name')?$this->request->param('real_name'):'';
	    //当前用户提交的手机号
	    $data['phone']     = $this->request->param('phone')?$this->request->param('phone'):'';
        //当前用户获取的短信验证码
        $data['code'] = $this->request->param('code');
        //参数校验
        $validate = new Through();
        if(!$validate->scene('add')->check($data)){
           $this->code = 217;
           $this->msg = $validate->getError();
           return $this->result('',$this->code,$this->msg,'json');
        }
        
        if(isset($data['code'])){
            $modelM   = model("MemberCode");
            $res      = $modelM->isValidCode($user['uid'],$data['code']);
            //验证成功
            if($res['code']==200){
            //if($data['code'] == session('code')){
                $model = model("through");
                if($model->createThroughApi($data)){
                    $model1 = model("Member");
                    $info   = $model1->where(['uid' => $data['uid']])->find();
                    if ($info['customerService'] != 0) {
                        $result = model('SelfCustomerService')->getCustomerServiceById($info['customerService']);
                    }else {
                        $result = model('SelfCustomerService')->getRandomCustomerService($data['uid']);
                    }
					
                    if(!empty($info['lastTime'])){
                        $info['lastTime'] = date('Y-m-d H:i:s',$info['lastTime']);
                    }
                    session('FINANCE_USER' , $info);
                    session('code', null);
                    $this->result('', 200, '添加认证信息成功', 'json');
                }else{
                    $error = $model->getError() ? '添加认证合伙人失败,'.$model->getError() : '添加认证合伙人失败';
                    $this->result('', 247, $error, 'json');
                }
            } else {
                //$this->result('', 248, '用户输入的验证码不正确', 'json');
                $this->result('', 248, $res['msg'], 'json');
            }
        } else {
            $model = model("through");
 
            if($model->createThroughApi($data)){
                $model = model("Member");
                $info = $model->where(['uid' => $user['uid']])->find();

                session('FINANCE_USER' , $info);
                session('code',null);
                $this->result('', 200, '添加认证信息成功', 'json');
            }else{
                $error = $model->getError() ? '添加认证合伙人失败,'.$model->getError() : '添加认证失败';
                $this->result('', 247, $error, 'json');
            }
        }
	}

	//获取手机验证码
	public function getNumber(){
        $mobile  = $this->request->param('phone/s');
        $partten = '/^(0|86|17951)?(13[0-9]|15[012356789]|17[0-9]|18[0-9]|14[57]|16[0-9])[0-9]{8}$/';
        if(empty($mobile) || preg_match($partten, $mobile) == 0){
            return $this->result('', 244, '手机号码不正确', 'json');
        }
        
		//判断该手机号是否已经注册过
		$sult = model("through")->where("phone = ".$mobile." and status = 1")->find();

		if(!empty($sult)){
			return $this->result('', 245, "该手机号已经注册过", 'json');
		}
		
        $number = rand(100000, 999999);
        //验证码插入数据库验证表中
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
                $text    = "您的验证码".$number."，该验证码15分钟内有效，请勿泄漏于他人！您正在注册成为新用户，感谢您的支持！";
                $model   = model("SendSms");
                $smsId   = $model->addSms($mobile,$text,1);
                $this->result('', 200, '发送成功', 'json');
            }else{
                //插入短信发送表
                $text    = "您的验证码".$number."，该验证码15分钟内有效，请勿泄漏于他人！您正在注册成为新用户，感谢您的支持！";
                $model   = model("SendSms");
                $smsId   = $model->addSms($mobile,$text,2,$array['Message']);
                $this->result('', 246, "验证码短信异常，请稍后再试", 'json');
            }
        }else{
            $this->result('', 246, '验证码获取失败，请重新获取', 'json');
        }
        
        
        //发送短信
        /*$apikey = "b4d4e18d335a960c008deca958c37326";
        session('code', $number);
        $text   = "【FA財】您的验证码是".$number;
        vendor("Passport.Sms");
        $sms = new \Sms('ac67816e0f384393545628fc31dc3b17');
        $array = $sms->send($mobile, $text, null);
        $array = json_decode(json_encode($array) , true);
        if($array['code'] == 0){
            if($array['data'][0]['sendState'] == 'SUCCESS'){
                $this->result('', 200, '发送成功', 'json');
            } else {
                $this->result('', 246, '发送短信验证码失败 '.$array['data'][0]['reason'], 'json');
            }
        } else {
            return $this->result('', 246, '发送短信验证码失败 '.$array['msg'], 'json');
        }*/
    }
    
    
    //修改手机号码时获取手机验证码
    public function getNewNumber(){
        $mobile  = $this->request->param('phone/s');
        $partten = '/^(0|86|17951)?(13[0-9]|15[012356789]|17[0-9]|18[0-9]|14[57]|16[0-9])[0-9]{8}$/';
        if(empty($mobile) || preg_match($partten, $mobile) == 0){
            return $this->result('', 244, '手机号码不正确', 'json');
        }
        
        //判断该手机号是否已经注册过
        $sult = model("through")->where("phone = ".$mobile." and status = 1")->find();
        
        if(!empty($sult)){
            return $this->result('', 245, "该手机号已经注册过", 'json');
        }
        
        $number = rand(100000, 999999);
        //验证码插入数据库验证表中
        $modelM             = model("MemberCode");
        $data['uid']        = session('FINANCE_USER.uid');
        $data['reg_code']   = $number;
        $res = $modelM->createCode($data);
        
        if($res){
            $sms    = new \common\Sms();
            $tem_id = "SMS_170330802";
            $array  = $sms->sendSms($mobile,$number,$tem_id);
            $array  = json_decode(json_encode($array) , true);
            if($array['Code'] == "OK"){
                //插入短信发送表
                $text    = "您正在更换手机号码，验证码为".$number."，该验证码15分钟内有效，请勿泄露于他人。";
                $model   = model("SendSms");
                $smsId   = $model->addSms($mobile,$text,1);
                $this->result('', 200, '发送成功', 'json');
            }else{
                //插入短信发送表
                $text    = "您正在更换手机号码，验证码为".$number."，该验证码15分钟内有效，请勿泄露于他人。";
                $model   = model("SendSms");
                $smsId   = $model->addSms($mobile,$text,2,$array['Message']);
                $this->result('', 246, "验证码短信异常，请稍后再试", 'json');
            }
        }else{
            $this->result('', 246, '验证码获取失败，请重新获取', 'json');
        }
        
        
        //发送短信
        /*$apikey = "b4d4e18d335a960c008deca958c37326";
         session('code', $number);
         $text   = "【FA財】您的验证码是".$number;
         vendor("Passport.Sms");
         $sms = new \Sms('ac67816e0f384393545628fc31dc3b17');
         $array = $sms->send($mobile, $text, null);
         $array = json_decode(json_encode($array) , true);
         if($array['code'] == 0){
         if($array['data'][0]['sendState'] == 'SUCCESS'){
         $this->result('', 200, '发送成功', 'json');
         } else {
         $this->result('', 246, '发送短信验证码失败 '.$array['data'][0]['reason'], 'json');
         }
         } else {
         return $this->result('', 246, '发送短信验证码失败 '.$array['msg'], 'json');
         }*/
    }
    
    //用户初次添加匹配的偏好标签
    public function addDict()
    {   

        $uid = session('FINANCE_USER.uid');
        //所有标签id(业务,行业,投融规模,省份)
        $dict_id = rtrim($this->request->param('id'),',')?rtrim($this->request->param('id'),','):'';//可单选,多选
        model('DictLabel')->addDictApi($uid,$dict_id);
    }
    
    //用户再次精确匹配偏好标签
    public function exactMatchDict()
    {
        $uid = session('FINANCE_USER.uid');
        //所有标签id(业务,行业,投融规模,省份)
        $dict_id = rtrim($this->request->param('id'),',')?rtrim($this->request->param('id'),','):'';
        //可单选,多选
        model('DictLabel')->exactMatchDictApi($uid,$dict_id);
    }
    
    //完善/修改合伙人的个人信息
    public function perfectPersonalInfo()
    {
         $uid = session('FINANCE_USER.uid');
         //头像
         $data['my_head']           = trim($this->request->param('my_head'))?trim($this->request->param('my_head')):'';
         if($this->request->param('role_type')>0){
             //身份
             $data['role_type']         = trim($this->request->param('role_type'))?trim($this->request->param('role_type')):0;
         }
         //姓名
         $data['realName']          = trim($this->request->param('real_name'))?trim($this->request->param('real_name')):'';
         //电话
         $data['phone']             = trim($this->request->param('phone'))?trim($this->request->param('phone')):'';
         //邮箱(选填)
         $data['email']             = trim($this->request->param('email'))?trim($this->request->param('email')):'';       
         //职位
         $data['position']          = trim($this->request->param('position'))?trim($this->request->param('position')):'';
         //公司名称
         $data['company']           = trim($this->request->param('company'))?trim($this->request->param('company')):'';
         //公司简称
         $data['company_jc']        = trim($this->request->param('company_jc'))?trim($this->request->param('company_jc')):'';
         //微信ID(选填)
         $data['weixin_id']         = trim($this->request->param('weixin_id'))?trim($this->request->param('weixin_id')):'';
         //公司地址(选填)
         $data['company_address']   = trim($this->request->param('company_address'))?trim($this->request->param('company_address')):'';
         //网址(选填)
         $data['website']           = trim($this->request->param('website'))?trim($this->request->param('website')):'';
         //当前用户获取的短信验证码(选填)
         $data['code']              = trim($this->request->param('code'))?trim($this->request->param('code')):'';
         //当前用户提交的个性标签(选填)
         $data['tag_name']          = trim(removeMark($this->request->param('tag_name')))?trim(removeMark($this->request->param('tag_name'))):'';
         $validate = new Through();
         if(!$validate->scene('edit')->check($data)){
             $this->code = 217;
             $this->msg = $validate->getError();
             return $this->result('',$this->code,$this->msg,'json');
        }
         model('Through')->perfectPersonalInfoApi($data,$uid); 
    }
    //返回当前用户选中的匹配标签
    public function showMatchDict()
    {
        $uid = session('FINANCE_USER.uid');
        model('DictLabel')->showMatchDictApi($uid);
    }
}
