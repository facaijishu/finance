<?php
namespace app\console\controller;

class Index extends ConsoleBase
{
    public function index()
    {
        $this->assign('title', lang('system_manage'));
        if($this->request->isAjax()){
            return view('welcome');
        }else{

            return view('index');
        }

    }
    
}