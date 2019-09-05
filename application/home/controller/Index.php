<?php
namespace app\home\controller;

class Index extends Base
{   
    public function test()
    {
      phpinfo();
    }
    //首页
    public function index()
    {
        //热门头条
        $model = model("consultation");
        $consultation = $model->getConsultationList(5);
        $this->assign('consultation' , $consultation);
        //公告走马灯
        $notice = $model->getConsultationNoticeList();
        $this->assign('notice' , $notice);
        //轮播图
        $model = model("CarouselFigure");
        $carousel_figure = $model->getCarouselFigureList();
        $this->assign('carousel_figure' , $carousel_figure);
        //精选项目
        $model = model("Project");
        $project = $model->getProjectLimitList(4);
        foreach ($project as $key => $value) {
            $project[$key]['introduction'] = mb_substr($value['introduction'], 0, 25 , 'utf-8');
        }
        $this->assign('project' , $project);
        //增发明细
        $model = model("Zfmxes");
        $zfmxes = $model->getZfmxesLimitList(0 , 5);
        $this->assign('zfmxes' , $zfmxes);
        //大宗转让
        $model = model("ZryxesEffect");
        $zryxes = $model->getZryxesLimitList(0 , 5);
        $this->assign('zryxes' , $zryxes);
        //网页分享配置
        $this->assign('title' , '');
        $this->assign('img' , '');
        $this->assign('des' , '欢迎来到金融合伙人!');
        return view();
    }
    //注册认证协议书
    public function agreenment(){
        //网页分享配置
        $this->assign('title' , '注册认证协议书 -');
        $this->assign('img' , '');
        $this->assign('des' , '欢迎来到金融合伙人!');
        return view();
    }
    //首页搜索
    public function search(){
        $data = input();
        $result = [];
        $project = model("Project")
                ->where("pro_name","like","%".$data['value']."%")
                ->where(['status' => 2 , 'flag' => 1])
                ->whereOr("introduction","like","%".$data['value']."%")
                ->whereOr("stock_code","like","%".$data['value']."%")
                ->select();
        $project = empty($project) ? null : $project;
        $model = model("dict");
        if(!empty($project)){
            foreach ($project as $key => $value) {
                $transfer_type = $model->getDictInfoById($value['transfer_type']);
                $project[$key]['transfer_type'] = $transfer_type['value'];
                $hierarchy = $model->getDictInfoById($value['hierarchy']);
                $project[$key]['hierarchy'] = $hierarchy['value'];
                $project[$key]['url'] = url("ProjectInfo/index" , ['id' => $value['pro_id']]);
            }
        }
        $road_show = model("RoadShow")->where("road_name","like","%".$data['value']."%")
                ->whereOr("road_introduce","like","%".$data['value']."%")
                ->select();
        $road_show = empty($road_show) ? null : $road_show;
        if(!empty($road_show)){
            foreach ($road_show as $key => $value) {
                $road_show[$key]['url'] = url("RoadShowInfo/index" , ['id' => $value['rs_id']]);
            }
        }
        $result['project'] = $project;
        $result['road_show'] = $road_show;
        $this->result($result);
    }
    //已放弃 - 首页没关注跳转页面
    public function code(){
        return view();
    }


    public function search2(){
        /*
         * 1：搜索功能先进行根据输入框的值进行全方位搜索，搜索只需要10个结果进行返回前端排列
         * 2：用户根据搜索框查询的条件内容点击后再次进行查询，查询出来是所有项目中的列
         * 3:input接受得值如果为1,进行特殊处理，
         * 4：将处理的结果进行筛选，符合的进行统一到一个数组中
         * 5：通过搜索出来的结果进行点击项目名称的时候在进行查询的时候不能是模糊查询了
         */
        $data = input();
        $arr = array();
        //精品项目中的结果
        $project = model("Project")
            //->where("pro_name","like","%".$data['value']."%")
            //->whereOr("stock_code","like","%".$data['value']."%")
            ->where(" pro_name like '%".$data['value']."%' or stock_code like '%".$data['value']."%'")
            ->where(['status' => 2 , 'flag' => 1])
            //->whereOr("introduction","like","%".$data['value']."%")
            ->order('last_time desc')
            ->limit(0,5)
            ->select();
        $project = empty($project) ? null : $project;
        if(!empty($project)){
            foreach ($project as $key => $value){
                $t1['name'] = $project[$key]['pro_name'];
                $t1['neeq'] = $project[$key]['stock_code'];
                $t1['msg'] = "项目精选";
                array_push($arr,$t1);
            }
        }
        //大宗转让中的项目
        $dazong = model("ZryxesEffect")->where(['zryx_financing' => 0 , 'zryx_exhibition' => 1])
            ->where(" sec_uri_tyshortname like '%".$data['value']."%' or neeq like '%".$data['value']."%'")
            ->order("stick desc,enddate desc")
            ->limit(0,5)
            ->select();
        $dazong = empty($dazong) ? null : $dazong;
        if(!empty($dazong)){
            foreach ($dazong as $key => $value){
                $t2['name'] = $dazong[$key]['sec_uri_tyshortname'];
                $t2['neeq'] = $dazong[$key]['neeq'];
                $t2['msg'] = "大宗转让";
                array_push($arr,$t2);
            }
        }
        //定增中的项目
        $dingzeng = db('Zfmxes_add')
            ->where(['zfmx_examine' => 1 , 'zfmx_exhibition' => 1 ,'zfmx_financing' => 0])
            ->where("sec_uri_tyshortname like '%".$data['value']."%' or neeq like '%".$data['value']."%'")
            ->limit(0,5)
            ->select();
        $dingzeng = empty($dingzeng) ? null : $dingzeng;
        if(!empty($dingzeng)){
            foreach ($dingzeng as $key => $value){
                $t3['name'] = $dingzeng[$key]['sec_uri_tyshortname'];
                $t3['neeq'] = $dingzeng[$key]['neeq'];
                $t3['msg'] = "定向增发";
                array_push($arr,$t3);
            }
        }
        //企中的项目
        $qiye = db("gszls")
            ->where("zqjc like '%".$data['value']."%' or neeq like '%".$data['value']."%'")
            ->limit(0,5)
            ->select();
        $qiye = empty($qiye) ? null : $qiye;
        if(!empty($qiye)){
            foreach ($qiye as $key => $value){
                $t4['name'] = $qiye[$key]['zqjc'];
                $t4['neeq'] = $qiye[$key]['neeq'];
                $t4['msg'] = "企业";
                array_push($arr,$t4);
            }
        }
        if(!empty($arr)){
            $this->result($arr,200,'数据查询成功','json');
            return false;
        }else{
            $this->result(0,404,'数据查询为空','json');
            return false;
        }
    }

    public function search3(){
        $data = input();
        if($data == ""){
            $open['data'] = '查询数据不能为空';
            $this->result($open,401,'数据查询为空','json');
            return false;
        }
        $arr = explode("(",$data['value']);
        $num = explode(")",$arr[1]);
        //股票名称
        $name = trim($arr[0]);
        //股票代码
        $neeq = trim($num[0]);
        $arr = array();
        //精品项目中的结果
        $project = model("Project")
            ->where("pro_name = '".$name."'")
            ->where(['status' => 2 , 'flag' => 1])
            //->whereOr("introduction","like","%".$data['value']."%")
            ->where("stock_code = '".$neeq."'")
            ->order('last_time desc')
            ->limit(0,10)
            ->select();
        $project = empty($project) ? null : $project;
        if(!empty($project)){
            foreach ($project as $key => $value){
                $t1['name'] = $project[$key]['pro_name'];
                $t1['neeq'] = $project[$key]['stock_code'];
                $t1['type'] = "项目精选";
                $t1['introduction'] = $project[$key]['introduction'];
                $t1['url'] = url("ProjectInfo/index" , ['id' => $project[$key]['pro_id']]);
                array_push($arr,$t1);
            }
        }
        //大宗转让中的项目
        $dazong = model("ZryxesEffect")->where(['zryx_financing' => 0 , 'zryx_exhibition' => 1])
            ->where(" sec_uri_tyshortname = '".$name."' and neeq = '".$neeq."'")
            ->order("stick desc,enddate desc")
            ->limit(0,10)
            ->select();
        $dazong = empty($dazong) ? null : $dazong;
        if(!empty($dazong)){
            foreach ($dazong as $key => $value){
                $t2['name'] = $dazong[$key]['sec_uri_tyshortname'];
                $t2['neeq'] = $dazong[$key]['neeq'];
                $t2['type'] = "大宗转让";
                $t2['introduction'] = array("fee"=>$dazong[$key]['direction'],"number"=>$dazong[$key]['num'],"money"=>$dazong[$key]['price']);
                $t2['url'] = url('zryxesEffectInfo/index',['id' => $dazong[$key]['id']]);
                array_push($arr,$t2);
            }
        }
        //定增中的项目
        $dingzeng = db('Zfmxes_add')
            ->where(['zfmx_examine' => 1 , 'zfmx_exhibition' => 1 ,'zfmx_financing' => 0])
            ->where("sec_uri_tyshortname = '".$name."' and neeq = '".$neeq."'")
            ->limit(0,10)
            ->select();
        $dingzeng = empty($dingzeng) ? null : $dingzeng;
        if(!empty($dingzeng)){
            foreach ($dingzeng as $key => $value){
                $t3['name'] = $dingzeng[$key]['sec_uri_tyshortname'];
                $t3['neeq'] = $dingzeng[$key]['neeq'];
                $t3['type'] = "定向增发";
                $t3['introduction'] = array("resize"=>$dingzeng[$key]['raise_money'],"jiage"=>$dingzeng[$key]['increase_price']);
                $t3['url'] = url('ZfmxesInfo/index',['neeq' => $dingzeng[$key]['neeq'] ,'plannoticeddate' => $dingzeng[$key]['plannoticeddate']]);
                array_push($arr,$t3);
            }
        }
        //企中的项目
        $qiye = db("gszls")
            ->where("zqjc = '".$name."' and neeq = '".$neeq."'")
            ->limit(0,10)
            ->select();
        $qiye = empty($qiye) ? null : $qiye;
        if(!empty($qiye)){
            foreach ($qiye as $key => $value){
                $t4['name'] = $qiye[$key]['zqjc'];
                $t4['neeq'] = $qiye[$key]['neeq'];
                $t4['type'] = "企业";
                $t4['introduction'] = $qiye[$key]['gsqc'];
                $t4['url'] = url('zryxesEffectInfo/company',['neeq' => $qiye[$key]['neeq']]);
                array_push($arr,$t4);
            }
        }
        if(!empty($data)){
            if(!empty($arr)){
                $this->result($arr,200,'数据查询成功','json');
                return false;
            }else{
                $this->result('',404,'数据查询为空','json');
                return false;
            }
        }
    }

}
