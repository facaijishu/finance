<?php

namespace app\console\controller;
set_time_limit(0);
class WechatSmsTmp extends ConsoleBase{
    
    /**
     * 微信消息模板列表首页
     */
    public function index(){
        faLog("HGFGHFFF-----");
        //$model  = model("wechatSmsTmp");
        //$result = $model->select();
        //$this->assign('list' , $result);
        return view();
    }
}
