<?php
namespace app\home\controller;

class GonggaoInfo extends Base
{
    public function index(){
        $ggid = $this->request->param('ggid/s');
        $info = db("st_gonggaos")->where(['ggid' => $ggid])->find();
        $info1 = db("st_sceus")->where(['ggid' => $ggid])->find();
        $info['neeq'] = $info1['secu_link_code'];
        $info['name'] = $info1['secu_s_name'];
        $info['text'] = trim($info['text'], '\r\t\s\n');
        $path = substr($info['file_path'], 36);
        $info['file_path'] = $path;
        $text = str_replace(array("\r\n"), "<br>", $info['text']); 
        $info['text'] = $text;
        $this->assign('info' , $info);
        $this->assign('title' , '公告详情 -');
        $this->assign('img' , '');
        $this->assign('des' , '这里有详细的公告内容!');
        return view();
    }
}
