<?php
namespace app\home\controller;

class ActivityInfo extends Base
{
    public function index(){
        $id = $this->request->param('id');
        $RequestAddress = [
            'controller' => $this->Request['controller'] , 
            'action'     => $this->Request['action'] , 
            'id'         => $id
                ];
        session('RequestAddress' , $RequestAddress);
        $model  = model("Activity");
        $info   = $model->getActivityInfo($id);
        $this->assign('info' , $info);
        
        $model  = model("ActivityComment");
        $list   = $model->getActivityCommentListByAcID($id);
        $this->assign('list' , $list);
        
        $user   = session('FINANCE_USER');
        if(in_array($id, explode(',', $user['collection_activity']))){
            $this->assign('sign' , 'true');
        } else {
            $this->assign('sign' , 'false');
        }
        
        $modelT = model("through");
        $sult   = $modelT->where("uid = ".$user['uid'])->find();
        $this->assign('sult' , $sult);
        
        $this->assign('title' , '- '.$info['act_name']);
        $this->assign('img' , $info['top_img_url']);
        $this->assign('des' , $info['introduce']);
        return view();
    }
    
    /**
     * 活动关注
     */
    public function collection(){
        $sign   = $this->request->param('sign/s');
        $id     = $this->request->param('id/d');
        $model  = model("Activity");
        if($model->setCollection($id , $sign)){
            if($sign == 'true'){
                $this->result('', 1, '收藏成功', 'json');
            } else {
                $this->result('', 1, '取消收藏成功', 'json');
            }
        } else {
            $error = $model->getError() ? $model->getError() : '系统出错,请联系管理员';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 报名申请
     */
    public function readySignUp(){
        $data   = input();
        $user   = session('FINANCE_USER');
		$result = model("Activity")->where(['a_id' => $data['id']])->find();
		
		
		$now        = time();
		$sign_start = $result['sign_start_time'];
		$sign_end   = $result['sign_end_time'];
		
		
		if($now>$sign_end){
			$this->result($data, 0, '活动已停止报名', 'json');
		}
        if($user['userType'] > 0){
            $this->result('', 1, '角色可以报名', 'json');
        } else {
            $data['url'] = url('Binding/index');
            $this->result($data, 0, '角色不可报名', 'json');
        }
    }
    
    /**
     * 提交报名
     */
    public function signUp(){
        $data       = input();
        $errormsg   = "";
        $errorCode  = "";
        if($data['regcode'] == "noCode"){
            $errorCode = 200;
            $errormsg  = '验证码验证成功！';
        }else{
            $uid        = session('FINANCE_USER.uid');
            $modelM     = model("MemberCode");
            $res        = $modelM->isValidCode($uid,$data['regcode']);
            $errorCode  = $res['code'];
            $errormsg   = $res['msg'];
        }
        //短信验证成功
        if($errorCode==200){
            $jsWxApi = $this->getJsApiPay($data);
            if($jsWxApi['code'] == 0){
                $this->result('', 0, $jsWxApi['msg'], 'json');
            }elseif ($jsWxApi['code'] == 3) {
                //免费
                $data['url'] = url('ActivityInfo/judge');
                $this->result($data, 3, $jsWxApi['msg'], 'json');
            } else {
                $this->result($jsWxApi['msg'], 1, '角色可以报名', 'json');
            }
        }else{
            $this->result('', 0, $errormsg, 'json');
        }
        
    }
    
    public function message(){
        $data = input();
        $model = model("ActivityComment");
        if($result = $model->createActivityComment($data)){
            $this->result($result, 1, '评论成功', 'json');
        } else {
            $this->result('', 0, '评论失败', 'json');
        }
    }

    /**
     * 
     * @param unknown $datas
     * @return string[]|number[]|string[]|number[]|NULL[]
     */
    public function getJsApiPay($datas){
        $user   = session('FINANCE_USER');
        $openId = $user['openId'];
        //$address = session("RequestAddress");
        $model  = model("Activity");
        $info   = $model->getActivityInfo($datas['id']);
        $fee    = intval($info['fee']*100);
        
        //判断是否已有订单
        $result = model("Order")->where(['type' => 'activity' , 'id' => $datas['id'] , 'is_pay' => 1 , 'status' => 1 , 'phone' => $datas['phone']])->find();
        $data = [];
        if(!empty($result)){
            $data['msg'] = '该活动用户已报名';
            $data['code'] = 0;
            return $data;
        }
        //判断活动人数限制
        $num = intval($info['max_people']) - intval($info['num_people']);
        if($num < 1){
            $data['msg'] = '该活动用户已满,无法报名';
            $data['code'] = 0;
            return $data;
        }
        vendor("WxPay.WxPayJsApiPay");
        $tools = new \JsApiPay();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($info['act_name']);//商品描述
        $input->SetAttach("购买门票");//附加数据
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($fee);
        $time = time();
        $input->SetTime_start(date("YmdHis" , $time));
        $input->SetTime_expire(date("YmdHis", $time+600));
        $input->SetGoods_tag("活动门票");
        $input->SetNotify_url("http://fin.yingtongkeji.com/home/activity_info/paySuccess");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $input->SetSign();
        
        //判断费用是否大于0
        if($fee == 0){
            $arr = [];
            $arr['uid']             = $user['uid'];
            $arr['type']            = 'activity';
            $arr['id']              = $datas['id'];
            $arr['name']            = $datas['name'];
            $arr['phone']           = $datas['phone'];
            $arr['company_jc']      = $datas['company_jc'];
            $arr['position']        = $datas['position'];
            //$arr['introduce']       = $datas['introduce'];
            $arr['appId']           = \WxPayConfig::APPID;
            $arr['mchId']           = \WxPayConfig::MCHID;
            $arr['noncestr']        = "nopay";
            $arr['ip']              = get_client_ip();
            $arr['body']            = "test";
            $arr['attach']          = $input->GetAttach();
            $arr['out_trade_no']    = $input->GetOut_trade_no();
            $arr['total_fee']       = $input->GetTotal_fee();
            $arr['start_time']      = $input->GetTime_start();
            $arr['expire_time']     = $input->GetTime_expire();
            $arr['goods_tag']       = $input->GetGoods_tag();
            $arr['notify_url']      = $input->GetNotify_url();
            $arr['trade_type']      = $input->GetTrade_type();
            $arr['openId']          = $input->GetOpenid();
            $arr['sign']            = $input->GetSign();
            $arr['is_pay']          = 1;
            $arr['create_time']     = time();
            $arr['pay_time']        = time();
            $arr['status']          = 1;
            
            
            $result = model("Order")->allowField(true)->save($arr);
            $pay_activity = explode(',', $user['pay_activity']);
            if(!in_array($datas['id'], $pay_activity)){
                array_push($pay_activity, $datas['id']);
            }
            
            //$user['pay_activity'] = trim(implode(',', $pay_activity), ',');
            //$result1 = model("Member")->where(['uid' => $user['uid']])->update(['pay_activity' => $user['pay_activity']]);
            //session('FINANCE_USER' , $user);
            if($result){
                $data['msg'] = '报名成功';
                $data['code'] = 3;
                return $data;
            } else {
                $data['msg'] = '报名失败';
                $data['code'] = 0;
                return $data;
            }
        }else{
            $order = \WxPayApi::unifiedOrder($input);
            //判断统一下单成功,生成订单
            if($order['return_code'] == 'SUCCESS'){
                if($order['result_code'] == 'SUCCESS'){
                    $arr = [];
                    $arr['uid']             = $user['uid'];
                    $arr['type']            = 'activity';
                    $arr['id']              = $datas['id'];
                    $arr['name']            = $datas['name'];
                    $arr['phone']           = $datas['phone'];
                    $arr['company_jc']      = $datas['company_jc'];
                    $arr['position']        = $datas['position'];
                    //$arr['introduce']       = $datas['introduce'];
                    $arr['appId']           = \WxPayConfig::APPID;
                    $arr['mchId']           = \WxPayConfig::MCHID;
                    $arr['noncestr']        = $input->GetNonce_str();
                    $arr['ip']              = $input->GetSpbill_create_ip();
                    $arr['body']            = $input->GetBody();
                    $arr['attach']          = $input->GetAttach();
                    $arr['out_trade_no']    = $input->GetOut_trade_no();
                    $arr['total_fee']       = $input->GetTotal_fee();
                    $arr['start_time']      = $input->GetTime_start();
                    $arr['expire_time']     = $input->GetTime_expire();
                    $arr['goods_tag']       = $input->GetGoods_tag();
                    $arr['notify_url']      = $input->GetNotify_url();
                    $arr['trade_type']      = $input->GetTrade_type();
                    $arr['openId']          = $input->GetOpenid();
                    $arr['sign']            = $input->GetSign();
                    $arr['is_pay']          = 0;
                    $arr['create_time']     = time();
                    $arr['pay_time']        = 0;
                    $arr['status']          = 0;
                    $result = model("Order")->allowField(true)->save($arr);
                    if(!$result){
                        $data['msg'] = '订单储存失败';
                        $data['code'] = 0;
                        return $data;
                    }
                } else {
                    $data['msg']    = $order['err_code'].':'.$order['err_code_des'];
                    $data['code']   = 0;
                    return $data;
                }
            } else {
                $data['msg']    = $order['return_code'].':'.$order['return_msg'];
                $data['code']   = 0;
                return $data;
            }
            
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $data['code']   = 1;
            $data['msg']    = json_decode($jsApiParameters);
            return $data;
            
        }
    }
    public function judge(){
        $this->assign('title' , '报名活动 -');
        $this->assign('img' , '');
        $this->assign('des' , '大家快一起来报名啊!');
        return view();
    }
    
    
    public function updateOrder(){
        $data   = input();
        $user   = session('FINANCE_USER');
        $info   = model("Order")->where(['uid' => $user['uid'] , 'id' => $data['id'] , 'type' => 'activity'])->order("start_time desc")->limit(1)->select();
        $id     = $info[0]['o_id'];
        $arr    = [];
        $arr['is_pay']      = 1;
        $arr['pay_time']    = time();
        $arr['status']      = 1;
        $result = model("Order")->where(['o_id' => $id])->update($arr);
        
        $pay_activity = explode(',', $user['pay_activity']);
        $result1 = true;
        if(!in_array($data['id'], $pay_activity)){
            array_push($pay_activity, $data['id']);
            $user['pay_activity'] = trim(implode(',', $pay_activity), ',');
            $result1 = model("Member")->where(['uid' => $user['uid']])->update(['pay_activity' => $user['pay_activity']]);
            session('FINANCE_USER' , $user);
        }
        $activity = model("Activity")->where(['a_id' => $data['id']])->find();
        $num = $activity['num_people']+1;
        $result2 = model("Activity")->where(['a_id' => $data['id']])->update(['num_people' => $num]);
        if($result && $result1){
            $this->result('', 1, '订单更新成功', 'json');
        } else {
            $this->result('', 0, '订单更新失败', 'json');
        }
    }
    
    //获取手机验证码
    public function getNewNumber(){
        $data    = input();
        $mobile  = $data['phone'];
        $partten = '/^(0|86|17951)?(13[0-9]|15[012356789]|17[0-9]|18[0-9]|14[57]|16[0-9])[0-9]{8}$/';
        if(empty($mobile) || preg_match($partten, $mobile) == 0){
            return $this->result('', 244, '手机号码不正确', 'json');
        }
        
        $result = model("Order")->where(['type' => 'activity' , 'id' => $data['id'] , 'is_pay' => 1 , 'status' => 1 , 'phone' => $data['phone']])->find();
        if(!empty($result)){
            return $this->result('', 244, '该手机号码已报名', 'json');
        }
        
        $uid    = session('FINANCE_USER.uid');
        $number = rand(100000, 999999);
        //验证码插入数据库验证表中
        $modelM             = model("MemberCode");
        $dsms['uid']        = $uid;
        $dsms['reg_code']   = $number;
        $res = $modelM->createCode($dsms);
        
        if($res){
            $sms    = new \common\Sms();
            $tem_id = "SMS_173190063";
            $array  = $sms->sendSms($mobile,$number,$tem_id);
            $array  = json_decode(json_encode($array) , true);
            if($array['Code'] == "OK"){
                //插入短信发送表
                $text    = "您的验证码".$number."，该验证码5分钟内有效，请勿泄漏于他人！";
                $model   = model("SendSms");
                $smsId   = $model->addSms($mobile,$text,1);
                $this->result('', 0, '发送成功', 'json');
            }else{
                //插入短信发送表
                $text    = "您的验证码".$number."，该验证码5分钟内有效，请勿泄漏于他人！";
                $model   = model("SendSms");
                $smsId   = $model->addSms($mobile,$text,2,$array['Message']);
                $this->result('', 1, "验证码短信异常，请稍后再试", 'json');
            }
        }else{
            $this->result('', 1, '验证码获取失败，请重新获取', 'json');
        }
    }
}
