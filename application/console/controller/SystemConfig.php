<?php
/**
 * 系统配置控制器
 */

namespace app\console\controller;
use think\Image;
class SystemConfig extends ConsoleBase
{
    public function index()
    {
        $model = model("SystemConfig");
        $info = $model->getSystemConfigInfo();
        $this->assign('info' , $info);
        return view();
    }
    public function edit(){
        $system_config = model('SystemConfig');
        if($system_config->setSystemConfig(input())){
            $data['url'] = url('SystemConfig/index');
            $this->result($data, 1, '修改系统设置成功', 'json');
        }else{
            $error = $system_config->getError() ? $system_config->getError() : '修改系统设置失败';
            $this->result('', 0, $error, 'json');
        }
    }
}