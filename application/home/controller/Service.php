<?php

namespace app\home\controller;

class Service extends Base{
    public function index(){
        $this->assign('title' , '- 服务中心');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最全面的服务!');
        return view();
    }
}
