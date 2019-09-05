<?php

namespace app\home\controller;

class RoadShow extends Base{
    public function index(){
        $model = model("RoadShow");
        $list  = $model->getRoadShowList(1);
        foreach ($list as $key => $value) {
            $list[$key]['duration'] = changeLongTime($value['duration']);
        }
        $this->assign('list' , $list);
        $model = model("dict");
        $industry = $model->getDictByType("industry");
        $this->assign('industry' , $industry);
        $roadshow_form = $model->getDictByType("roadshow_form");
        $this->assign('roadshow_form' ,$roadshow_form);
        $this->assign('title' , ' - 路演中心');
        $this->assign('img' , '');
       // $this->assign('des' , '这里有最全面的金融路演!');
        $model = model('SystemConfig');
        $system_config = $model->getSystemConfigInfo();
        $this->assign('des' , $system_config['road_show_des']);
         //客服logo
        $model = model('Project');
        $project = $model->getProjectLimitOne();
        $this->assign('project',$project);
        return view();
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $url = url('RoadShowInfo/index',['id' => $id]);
        $this->result($url);
    }
}
