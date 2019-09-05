<?php
namespace app\home\controller;

class Project extends Base
{
    public function index()
    {
        $model = model("dict");
        $sign = $model->getDictByType('sign');
        $n = array_pop($sign);
        $this->assign('sign' , $sign);
        $capital_plan = $model->getDictByType('capital_plan');
        $this->assign('capital_plan' , $capital_plan);
        $transfer_type = $model->getDictByType('transfer_type');
        $this->assign('transfer_type' , $transfer_type);
        $hierarchy = $model->getDictByType('hierarchy');
        $this->assign('hierarchy' , $hierarchy);
        $model = model("project");
        $project = $model->getProjectList(2);
        $this->assign('project' , $project);
        $arr = '';
        if(session('FINANCE_USER.pro_search') != ''){
            $arr = explode(',', session('FINANCE_USER.pro_search'));
            krsort($arr);
        }
        $this->assign('pro_search' , $arr);
        $model = model("ProSearchHistory");
        $hot_search = $model->getProSearchHistory();
        $this->assign('search_hot' , $hot_search);
        $this->assign('title' , '- 精品项目');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最新的金融合伙人项目!');
        return view();
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $url = url('ProjectInfo/index',['id' => $id]);
        $this->result($url);
    }

    public function csign(){
        $id = $this->request->param('sign/d');
        $model = model("project");
        if($id == ''){
            $project = $model->getProjectList(2);
        } else {
            $project = $model->getProjectList(2 , $id);
        }
        $this->result($project);
    }
    public function ctext(){
        $text = $this->request->param('text');
        $model = model("project");
        $project = $model->getProjectListByName($text);
        $model = model("Member");
        $result = $model->setMemberProSearch($text);
        $model = model("ProSearchHistory");
        $result1 = $model->addSearchHistory($text);
        if($project && $result && $result1){
            $this->result($project);
        } else {
            $this->result(array());
        }
    }

    public function sproject(){
        $data = input();
        $model = model('project');
        //搜索条件
        if('' !== $data['enterprise']){
            $model->where("pro_name like '%".$data['enterprise']."%'");
        }
        if('' !== $data['capital_plan']) {
            $model->where('capital_plan','eq',$data['capital_plan']);
        }
        if('' !== $data['financing_amount']){
            if(1 == $data['financing_amount']){
                $model->where('financing_amount',['lt',1000],['eq',1000],'or');
            }elseif (2 == $data['financing_amount']) {
                $model->where('financing_amount',['gt',1000],['eq',1000],'or');
                $model->where('financing_amount',['lt',3000],['eq',3000],'or');
            }elseif (3 == $data['financing_amount']) {
                $model->where('financing_amount',['gt',3000],['eq',3000],'or');
                $model->where('financing_amount',['lt',5000],['eq',5000],'or');
            } else {
                $model->where('financing_amount',['gt',5000],['eq',5000],'or');
            }
        }
        if('' !== $data['corporate_valuation']){
            if(1 == $data['corporate_valuation']){
                $model->where('corporate_valuation',['lt',10000],['eq',10000],'or');
            }elseif (2 == $data['corporate_valuation']) {
                $model->where('corporate_valuation',['gt',10000],['eq',10000],'or');
                $model->where('corporate_valuation',['lt',50000],['eq',50000],'or');
            }elseif (3 == $data['corporate_valuation']) {
                $model->where('corporate_valuation',['gt',50000],['eq',50000],'or');
                $model->where('corporate_valuation',['lt',100000],['eq',100000],'or');
            } else {
                $model->where('corporate_valuation',['gt',100000],['eq',100000],'or');
            }
        }
        if('' !== $data['net_profit']){
            if(1 == $data['net_profit']){
                $model->where('net_profit',['lt',500],['eq',500],'or');
            }elseif (2 == $data['net_profit']) {
                $model->where('net_profit',['gt',500],['eq',500],'or');
                $model->where('net_profit',['lt',2000],['eq',2000],'or');
            }elseif (3 == $data['net_profit']) {
                $model->where('net_profit',['gt',2000],['eq',2000],'or');
                $model->where('net_profit',['lt',5000],['eq',5000],'or');
            } else {
                $model->where('net_profit',['gt',5000],['eq',5000],'or');
            }
        }
        if('' !== $data['annual_revenue']){
            if(1 == $data['annual_revenue']){
                $model->where('annual_revenue',['lt',500],['eq',500],'or');
            }elseif (2 == $data['annual_revenue']) {
                $model->where('annual_revenue',['gt',500],['eq',500],'or');
                $model->where('annual_revenue',['lt',2000],['eq',2000],'or');
            }elseif (3 == $data['annual_revenue']) {
                $model->where('annual_revenue',['gt',2000],['eq',2000],'or');
                $model->where('annual_revenue',['lt',5000],['eq',5000],'or');
            }elseif (4 == $data['annual_revenue']) {
                $model->where('annual_revenue',['gt',5000],['eq',5000],'or');
                $model->where('annual_revenue',['lt',10000],['eq',10000],'or');
            }elseif (5 == $data['annual_revenue']) {
                $model->where('annual_revenue',['gt',10000],['eq',10000],'or');
                $model->where('annual_revenue',['lt',20000],['eq',20000],'or');
            } else {
                $model->where('annual_revenue',['gt',20000],['eq',20000],'or');
            }
        }
        if('' !== $data['transfer_type']) {
            $model->where('transfer_type','eq',$data['transfer_type']);
        }
        if('' !== $data['hierarchy']) {
            $model->where('hierarchy','eq',$data['hierarchy']);
        }
        $model->where('status','eq',2);
        $model->where('flag','eq',1);
        $result = $model->select();
        foreach ($result as $key => $value) {
            $inc_industry = explode(',', $value['inc_industry']);
            $model = model("dict");
            $transfer_type = $model->getDictInfoById($value['transfer_type']);
            $result[$key]['transfer_type'] = $transfer_type['value'];
            $hierarchy = $model->getDictInfoById($value['hierarchy']);
            $result[$key]['hierarchy'] = $hierarchy['value'];
            foreach ($inc_industry as $key1 => $value1) {
                $info = $model->getDictInfoById($value1);
                $inc_industry[$key1] = $info['value'];
            }
            $result[$key]['inc_industry'] = $inc_industry;
            $model = model("material_library");
            $top_img = $model->getMaterialInfoById($value['top_img']);
            $result[$key]['top_img'] = $top_img['url'];
            $company_video = $model->getMaterialInfoById($value['company_video']);
            $result[$key]['company_video'] = $company_video['url'];
        }
        if($result){
            $this->result($result , 1 , '搜索成功' , 'json');
        } else {
            $this->result('' , 0 , '不存在数据' , 'json');
        }
    }
}
