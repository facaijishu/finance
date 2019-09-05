<?php
namespace app\home\controller;

class ProjectNews extends Base
{
    public function index(){
        $id = $this->request->param('n_id/d');
        $model = model("ProjectNews");
        $info = $model->getProjectNewsInfoById($id);
        $this->assign('info' , $info);
        $this->assign('title' , $info['title'].' -');
        $this->assign('img' , '');
        $this->assign('des' , '这是项目的新闻');
        return view();
    }
}