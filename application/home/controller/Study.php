<?php
namespace app\home\controller;

class Study extends Base
{
    public function index(){
        $info = $this->jsGlobal;
        $data = cookie("openid");
        $user = $this->jsGlobal;
        if($user['member']['userType'] == 2 || (!empty($user['through']) && $user['through']['status'] == 1)){
            $this->assign('openid' , $data);
            $this->assign('title' , '申请老师 -');
            $this->assign('img' , $info['member']['userPhoto']);
            $this->assign('des' , '欢迎来到金融合伙人!');
            return view();
        } else {
            if(empty($user['through'])){
                $url = url('Through/index');
                header("Location: $url");
                die;
            }else{
                $url = url('MyInfo/index');
                header("Location: $url");
                die;
            }
        }
//        $data = input();
        
//        $aaa = \think\Db::connect(['type' => 'mysql','hostname' =>'localhost','database'=>'finance','username' =>'root','password'=>''])->table("fic_study")->select();
//        var_dump($aaa);
        
        
    }
    public function sendSuccess(){
        $data = input();
        $model = model("Study");
        if($model->createStudyInfo($data)){
            $data['url'] = url('Study/succeed');
            $this->result($data, 1, '报名成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '报名失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function succeed(){
        $this->assign('title' , '报名中心 -');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最简洁的报名过程!');
        return view();
    }
}