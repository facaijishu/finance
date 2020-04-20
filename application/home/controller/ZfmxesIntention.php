<?php
namespace app\home\controller;

class ZfmxesIntention extends Base
{
    public function index(){
        $neeq               = $this->request->param('neeq/s');
        $plannoticeddate    = $this->request->param('plannoticeddate/s');
        $info               = model("Zfmxes")->getZfmxesSimpleInfo($neeq , $plannoticeddate);
        $info['num']        = intval($info['add']['additional_num'])*10000;
        $info['real_num']   = $info['num'];
        
        if($info['num']>=10000&&$info['num']<100000000){
            $info['num'] = (($info['num']/10000).'万');
        }else if($info['num']>=100000000){
            $info['num'] = (($info['num']/100000000).'亿');
        }
        
        $user = $this->jsGlobal;
        $this->assign('info' , $info);
        $this->assign('title' , '- 增发意向');
        $this->assign('img' , '');
        $this->assign('des' , '这里您可以提交您的增发意向!');
        return view();
    }
    
    
    public function add(){
        $data  = input();
        $user  = $this->jsGlobal;
        $data['company']         = $user['member']['company'];
        $data['phone']           = $user['member']['userPhone'];
        $data['create_uid']      = $user['member']['uid'];
        
        $model = model("ZfmxesIntention");
        if($model->createZfmxesIntention($data)){
            $data['url'] = url('ZfmxesIntention/succeed');
            $this->result($data, 1, '提交意向成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '提交意向失败';
            $this->result('', 0, $error, 'json');
        }
       
    }
    
    //添加认证成功
    public function succeed(){
        $this->assign('title' , '意向中心 -');
        $this->assign('img' , '');
        $this->assign('des' , '这里您可以提交您的增发意向!');
        return view();
    }
}
