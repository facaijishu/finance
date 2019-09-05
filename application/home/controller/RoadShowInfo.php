<?php
namespace app\home\controller;

class RoadShowInfo extends Base
{
    public function index()
    {
        $id = $this->request->param('id/d');
        $member = $this->jsGlobal['member'];
        $road_show = explode(',', $member['road_show']);
        if(!in_array($id, $road_show)){
            array_push($road_show, $id);
            $rr = trim(implode(',', $road_show), ',');
            model("Member")->where(['uid' => $member['uid']])->update(['road_show' => $rr]);
            $member['road_show'] = $rr;
            session('FINANCE_USER' , $member);
        }
        $model = model("RoadShowInfo");
        $info = $model->getRoadShowInfoById($id);
        $info['duration'] = changeLongTime($info['duration']);
        $this->assign('info' , $info);
        $this->assign('title' , '- '.$info['road_name']);
        $this->assign('img' , '');
        $this->assign('des' , $info['road_introduce'][0]);
        return view();
    }
}
