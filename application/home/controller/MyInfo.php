<?php

namespace app\home\controller;

class MyInfo extends Base{
    public function index(){
        $RequestAddress = ['controller' => $this->Request['controller'] , 'action' => $this->Request['action']];
        session('RequestAddress' , $RequestAddress);
        $info = $this->jsGlobal;
        if(empty($info['member']['userPhone']) || $info['member']['userType'] == 0){
            $url = url('Binding/index');
            header("Location: $url");
            die;
        } else {
            $this->assign('title' , '- 我的认证信息');
            $this->assign('img' , $info['member']['userPhoto']);
            $this->assign('des' , '我的认证信息');
            $this->assign('info' , $info);
            return view();
        }
    }
}
