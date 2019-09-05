<?php

namespace app\home\controller;

class Binding extends Base{
    public function index(){
        $this->assign('title' , '绑定手机 -');
        $this->assign('img' , '');
        $this->assign('des' , '邀请绑定手机账号!');
        $info = $this->jsGlobal;
        $this->assign('info' , $info);
        return view();
    }
    
    public function add(){
        $data = input();
        if($data['code'] == session('code')){
            session('code', null);
            $model = model("Binding");
            if($model->createBinding($data)){
                $model_m = model("Member");
                $user = session('FINANCE_USER');
                $info = $model_m->where(['uid' => $user['uid']])->find();
                if ($info['customerService'] != 0) {
                    $result = model('SelfCustomerService')->getCustomerServiceById($info['customerService']);
                }else {
                    $result = model('SelfCustomerService')->getRandomCustomerService($user['uid']);
                }
                if(!empty($info['lastTime'])){
                    $info['lastTime'] = date('Y-m-d H:i:s',$info['lastTime']);
                }
                $result_m = $model->sendProjectInfo($result['real_name'],$info['userPhone'],$info['lastTime'],"绑定用户",$result['kf_openId'],$user['uid']);
                if(!$result_m){
                    $data = [];
                    $this->result($data, 1, '发送消息模板失败', 'json');
                }
                session('FINANCE_USER' , $info);
                session('code',null);
                $data = [];
                $data['url'] = url('Binding/succeed');
                $this->result($data, 1, '绑定手机成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '绑定手机失败';
                $this->result('', 0, $error, 'json');
            }
        } else {
            $this->result('', 0, '验证码不正确', 'json');
        }
    }
    public function judge(){
        $judge = session('RequestAddress');
        $data = [];
        if(isset($judge['id'])){
            $data['url'] = url($judge['controller'].'/'.$judge['action'], ['id' => $judge['id']]);
        } else {
            $data['url'] = url($judge['controller'].'/'.$judge['action']);
        }
        session('RequestAddress' , null);
        $this->result($data);
    }
    public function succeed(){
        $this->assign('title' , '绑定手机成功 -');
        $this->assign('img' , '');
        $this->assign('des' , '手机账号绑定成功!');
        return view();
    }
}