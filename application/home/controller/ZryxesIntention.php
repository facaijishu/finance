<?php
namespace app\home\controller;

class ZryxesIntention extends Base
{
    public function index(){
        $id = $this->request->param('id/d');
        $info = model("ZryxesEffect")->getZryxesEffectSimpleInfo($id);
//        $info['num'] = intval($info['num'])*10000;
        $info['real_num'] = $info['num'];
        if($info['num']>=10000&&$info['num']<100000000){
//            $info['real_num'] = $info['num'];
            $info['num'] = (($info['num']/10000).'万');
        }else if($info['num']>=100000000){
//            $info['danwei'] = 10000;
            $info['num'] = (($info['num']/100000000).'亿');
        }
        $this->assign('info' , $info);
        $this->assign('title' , '大宗意向 -');
        $this->assign('img' , '');
        $this->assign('des' , '这里您可以提交您的大宗意向!');
        return view();
    }
    public function add(){
        $data = input();
        if($data['code'] == session('code')){
//            session('code', null);
            $model = model("ZryxesIntention");
            if($model->createZryxesIntention($data)){
                $data['url'] = url('ZryxesIntention/succeed');
                $this->result($data, 1, '提交意向成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '提交意向失败';
                $this->result('', 0, $error, 'json');
            }
        } else {
            $this->result('', 0, '验证码不正确哦', 'json');
        }
    }
    //添加认证成功
    public function succeed(){
        $this->assign('title' , '意向中心 -');
        $this->assign('img' , '');
        $this->assign('des' , '这里您可以提交您的大宗意向!');
        return view();
    }
}