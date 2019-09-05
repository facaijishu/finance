<?php
namespace app\home\controller;

class Activity extends Base
{
    public function index(){
        $model = model("Activity");
        $list  = $model->getActivityList();
        
        $this->assign('list' , $list);
        $this->assign('title' , ' - 活动中心');
        $this->assign('img' , '');
        $this->assign('des' , '精选活动，推荐给你');
        
        //客服logo
        $model   = model('Project');
        $project = $model->getProjectLimitOne();
        $this->assign('project',$project);
        
        return view();
    }
    public function judge(){
        $id = $this->request->param('id/d');
        $url = url('ActivityInfo/index',['id' => $id]);
        $this->result($url);
    }
    
}
