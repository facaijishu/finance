<?php
namespace app\home\controller;

use \common\Sms;

class Test extends Base
{   
    //首页
    public function index()
    {
        
        $aa = ["高端制造","先进轨道交通","半导体及元件","小金属","先进钢铁材料","新能源汽车整车"];
        
        $desc = implode(',', $aa);
        $len  = strlen($desc);
        if($len>30){
            $desc = substr($desc,30)."...";
        }
        
        echo $desc;
         
    }   
        
    public function wechat(){
        $list = model("aa_uid")->limit(0,10)->select();
        foreach ($list as $key => $value) {
            $isFollow = isFollow($value['openId'],getAccessTokenFromFile());
            if($isFollow){
                $isFollow = $value['uid'].",已关注";
            }else{
                $isFollow = $value['uid'].",未关注";
            }
            echo $isFollow;
        }
    }
    
    //获取手机验证码
    public function getNumber(){
        
        $mobile  = "15021031142";
        $partten = '/^(0|86|17951)?(13[0-9]|15[012356789]|17[0-9]|18[0-9]|14[57])[0-9]{8}$/';
        if(empty($mobile) || preg_match($partten, $mobile) == 0){
            $this->result('', -1, '手机号码不正确', 'json');
        }

        
        $apikey = "b4d4e18d335a960c008deca958c37326";
        $number = rand(100000, 999999);
        $text   = "【FA財】您的验证码是".$number;
        
        //插入短信发送表
        $model   = model("SendSms");
        $smsId   = $model->addSms($mobile,$text);
        
        $modelM   = model("MemberCode");
        $data['uid']        = 5119;
        $data['reg_code']   = $number;
        $modelM->createCode($data);
        
        /*vendor("Passport.Sms");
        $sms    = new \Sms('ac67816e0f384393545628fc31dc3b17');
        $array  = $sms->send($mobile, $text, null);
        $array  = json_decode(json_encode($array) , true);
        faLog(json_encode($array));
        if($array['code'] == 0){
            if($array['data'][0]['sendState'] == 'SUCCESS'){
                $model->setStatus($smsId,1,"");
                $this->result($array, 0, '发送成功', 'json');
            } else {
                $model->setStatus($smsId,2,$array['data'][0]['reason']);
                $this->result($array, 1, $array['data'][0]['reason'], 'json');
            }
        } else {
            $model->setStatus($smsId,2,$array['msg']);
            $this->result($array, 1, $array['msg'], 'json');
        }*/
    }


    public function aa(){
        
        /*$uid  = 5119;
        $code = 622015;
        $modelM   = model("MemberCode");
        $res = $modelM->isValidCode($uid,$code);
        */
        
        $number = rand(100000, 999999);
        
        //插入用户验证码表
        $modelM   = model("MemberCode");
        $data['uid']        = 5119;
        $data['reg_code']   = $number;
        $modelM->createCode($data);
        
        
        $sms    = new \common\Sms();
        $mobile = "15021031142";
        $array  = $sms->sendSms($mobile,$number);
        $array = json_decode(json_encode($array) , true);
        if($array['Code'] == "OK"){
            //插入短信发送表
            $text    = "您的验证码".$number."，该验证码15分钟内有效，请勿泄漏于他人！您正在注册成为新用户，感谢您的支持！";
            $model   = model("SendSms");
            $smsId   = $model->addSms($mobile,$text,1,"");
            echo "AAAAAAA----".$array['Message'];
        }else{
            //插入短信发送表
            $text    = "您的验证码".$number."，该验证码15分钟内有效，请勿泄漏于他人！您正在注册成为新用户，感谢您的支持！";
            $model   = model("SendSms");
            $smsId   = $model->addSms($mobile,$text,2,$array['Message']);
            
            echo $array['Message'];
        }
        /*vendor("AliSms.Sms");
        $sms   = new \Sms();
        $array = $sms->sendSms($mobile, $text);
        echo $array."<br/>";
        print_r($array);*/
       
        //验证成功
        /*if($res['code']==200){
            print_r($res);
        }else{
            print_r($res);
        }*/
        
    }

}
