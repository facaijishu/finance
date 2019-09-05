<?php
namespace app\home\controller;

class ActivitySign extends Base
{
    public function index(){
        
        $id = $this->request->param('id');
        $RequestAddress = [
            'controller' => $this->Request['controller'] ,
            'action'     => $this->Request['action'] ,
            'id'         => $id
        ];
        session('RequestAddress' , $RequestAddress);
        
        $uid  = session('FINANCE_USER.uid');
        $sign = model("ActivitySign")->where(['a_id' => $id,'uid' => $uid])->find();
        if(empty($sign)){
            $this->assign('sign' , 0);
        }else{
            $this->assign('sign' , 1);
        }
        
        $result = model("Activity")->where(['a_id' => $id])->find();
        
        $start = substr($result['start_time'], 12 , 5);
        $end   = substr($result['end_time'], 12 , 5);
        $this->assign('start' , $start);
        $this->assign('end' , $end);
        
        $this->assign('act' , $result);
        $this->assign('a_id' , $id);
        $this->assign('title' , ' - 线上验票');
        $this->assign('img' , '');
        $this->assign('des' , '精选活动，推荐给你');
        
        return view();
    }
    
    /**
     * 签到验票
     */
    public function sign(){
        $data   = input();
        $uid    = session('FINANCE_USER.uid');
        $result = model("Activity")->where(['a_id' => $data['id']])->find();
        if(empty($result)){
            $this->result('', 1, '活动不存在', 'json');
        }
        
        
        $sign = model("ActivitySign")->where(['a_id' => $data['id'],'realName' => $data['name'],'mobile' => $data['phone']])->find();
        if(empty($sign)){
            $signName  = model("ActivitySign")->where(['a_id' => $data['id'],'realName' => $data['name']])->find();
            if(!empty($signName)){
                $this->result('', 1, '填写的手机号码与报名申请提交的不一致', 'json');
            }
            
            $signMobile = model("ActivitySign")->where(['a_id' => $data['id'],'mobile' => $data['phone']])->find();
            if(!empty($signMobile)){
                $this->result('', 1, '填写的姓名与报名申请提交的不一致', 'json');
            }
            if(empty($signName) && empty($signMobile)){
                $this->result('', 2, '请先报名该活动', 'json');
            }
        }else{
            if($sign['status']==1){
                $this->result('', 1, '已经验演过票了', 'json');
            }else{
                $updata['uid']       = $uid;
                $updata['sign_time'] = time();
                $updata['status']    = 1;
                $res = model("ActivitySign")->where(['a_id' => $data['id'],'realName' => $data['name'],'mobile' => $data['phone']])->update($updata);
                if($res){
                    $this->result('', 0, '验票成功', 'json');
                }else{
                    $this->result('', 3, '报名失败请重试', 'json');
                }
            }
        }
    }
    
    
    /**
     * 签到报名注册
     */
    public function reg(){
        $data    = input();
        $uid     = session('FINANCE_USER.uid');
        $modelM  = model("MemberCode");
        $res     = $modelM->isValidCode($uid,$data['regcode']);
        //短信验证成功
        if($res['code']==200){
            $arr = [];
            $arr['a_id']        = $data['id'];
            $arr['uid']         = $uid;
            $arr['mobile']      = $data['phone'];
            $arr['realName']    = $data['name'];
            $arr['company']     = $data['com_jc'];
            $arr['position']    = $data['position'];
            $arr['sign_time']   = time();
            $arr['status']      = 1;
            $arr['channel']     = "现场";
            $res_sign = model("ActivitySign")->createActivitySign($arr);
            
            //签到信息提交成功
            if($res_sign){
                $info   = model("Member")->where(['uid' => $uid])->find();
                if($info['userType']<2){
                    $modelT = model("through");
                    $sult   = $modelT->where("uid = ".$uid)->find();
                    if(empty($sult)){
                        //插入认证表信息
                        $arrT['role_type']   = 2;
                        $arrT['uid']         = $uid;
                        $arrT['realName']    = $data['name'];
                        $arrT['phone']       = $data['phone'];
                        $arrT['company_jc']  = $data['com_jc'];
                        $arrT['position']    = $data['position'];
                        $arrT['createTime']  = time();
                        $arrT['updateTime']  = time();
                        $arrT['throughTime'] = time();
                        $arr['lastTime']     = 0;
                        $arrT['my_head']     = $info['userPhoto'];
                        $arrT['type']        = "through";
                        $arrT['flag']        = 1;
                        $arrT['status']      = 1;
                        $modelT->allowField(true)->save($arrT);
                        
                        //更新用户memeber表信息
                        $save['userType']   = 2;
                        $save['realName']   = $data['name'];
                        $save['userPhone']  = $data['phone'];
                        $save['company_jc'] = $data['com_jc'];
                        $save['position']   = $data['position'];
                        model("Member")->where(['uid' => $uid])->update($save);
                        
                    }else{
                        //插入更新认证表信息
                        
                        $arrT['realName']    = $data['name'];
                        $arrT['phone']       = $data['phone'];
                        $arrT['company_jc']  = $data['com_jc'];
                        $arrT['position']    = $data['position'];
                        $arrT['updateTime']  = time();
                        $arrT['throughTime'] = time();
                        $arrT['type']        = "through";
                        $arrT['flag']        = 1;
                        $arrT['status']      = 1;
                        $modelT->where(['uid' => $uid])->update($arrT);
                        
                        //更新用户memeber表信息
                        $save['userType']   = 2;
                        $save['userPhone']  = $data['phone'];
                        $save['company_jc'] = $data['com_jc'];
                        $save['position']   = $data['position'];
                        model("Member")->where(['uid' => $uid])->update($save);
                    }
                }
                $this->result('', 1, "验票成功", 'json');
            }else{
                $this->result('', 0, "提交信息异常，请重新签到", 'json');
            }
        }else{
            $this->result('', 0, $res['msg'], 'json');
        }
    }
    
    public function voting(){
        $id = $this->request->param('id');
        $RequestAddress = [
            'controller' => $this->Request['controller'] ,
            'action'     => $this->Request['action'] ,
            'id'         => $id
        ];
        session('RequestAddress' , $RequestAddress);
        $this->assign('a_id' , $id);
        $this->assign('title' , ' - 活动项目投票');
        $this->assign('img' , '');
        $this->assign('des' , '精选活动，推荐给你');
        
        $activity = model('Activity')->where(['a_id' => $id])->find();
        if(!empty($activity['pro_ids'])){
            $project  = model('Project')->where(" pro_id in ( ".$activity['pro_ids']." ) ")->select();
            foreach ($project as $key => $value) {
                $project[$key]['id_name'] = "checkId_".$value['pro_id'];
                $project[$key]['id_check'] = "checkkk_".$value['pro_id'];
            }
        }else{
            $project  = [];
        }
        $this->assign('project',$project);
        
        return view();
    }
    /**
     * 项目投票
     */
    public function dovoting(){
        $data    = input();
        $uid     = session('FINANCE_USER.uid');
        if(empty($data)){
            $this->result('', 0, "提交的项目异常，请重新选择提交", 'json');
        }else{
            $sign    = model("ActivitySign")->where(['uid' => $uid, 'a_id' => $data['id']])->find();
            if(empty($sign)){
                $this->result('', 2, "活动还没验票，无法参与项目投票", 'json');
            }else{
                //更新投票的项目
                $save['enjoyed_pro'] = $data['pro_ids'];
                $res = model("ActivitySign")->where(['uid' => $uid, 'a_id' => $data['id']])->update($save);
                if($res){
                    $this->result('', 1, "感兴趣的项目已经提交，活动后会联系您发送BP", 'json');
                }else{
                    $this->result('', 0, "提交异常，请重新选择提交", 'json');
                }
            }
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
