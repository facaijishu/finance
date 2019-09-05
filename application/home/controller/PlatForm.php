<?php

namespace app\home\controller;

class PlatForm extends Base{
    public function index(){
        $info = $this->jsGlobal;
        if((!empty($info['through']) && $info['through']['status'] == 1) || cookie('sign') != null){
            $model = model("project");
            $project = $model->getProjectList(2);
            if($info['through']['status'] == 1){
                $info = $info;
                $this->assign('show' , 'true');
            } else {
                $info = [];
                $uid = cookie('superior');
                $info['member'] = model("Member")->where(['uid' => $uid])->find();
                $info['through'] = model("Through")->where(['uid' => $uid])->find();
                $this->assign('show' , 'false');
            }
            $this->assign('project' , $project);
            $this->assign('info' , $info);
            $this->assign('title' , '- 我的平台');
            $this->assign('img' , $info['member']['userPhoto']);
            $this->assign('des' , '我的平台我做主!');
            return view();
        } elseif (!empty ($info['through']) && $info['through']['status'] != 1) {
            $url = url('MyInfo/index');
            header("Location: $url");
            die;
        }else {
            $url = url('Through/index');
            header("Location: $url");
            die;
        }
    }
}
