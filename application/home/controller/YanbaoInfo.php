<?php
namespace app\home\controller;

class YanbaoInfo extends Base
{
    public function index(){
        $id = $this->request->param('id/s');
        $info = db("st_yanbaos_effect")->where(['id' => $id])->find();
        $this->assign('info' , $info);
        $this->assign('title' , '研报详情 -');
        $this->assign('img' , '');
        $this->assign('des' , '这里有详细的研报内容!');
        return view();
    }
}