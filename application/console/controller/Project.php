<?php
namespace app\console\controller;
set_time_limit(0);
class Project extends ConsoleBase{
    
    /**
     * 项目列表首页
     */
    public function index(){
        return view();
    }
    
    /**
     * 项目列表集合获取
     */
    public function read(){
        $return       = [];
        $order        = $this->request->get('order/a', []);
        $start        = $this->request->get('start', 0);
        $length       = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $status       = $this->request->get('status', '');
        $pro_name     = $this->request->get('pro_name', '');

        $project = model('Project')
                ->alias("p")
                ->join("QrcodeDirection qd","p.qr_code=qd.id","LEFT")
                ->field("SQL_CALC_FOUND_ROWS p.*,qd.qrcode_path");
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $project->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $project->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $project->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $status) {
            $project->where(['status'=>$status]);
        }
        if('' !== $pro_name) {
            $project->where('pro_name','like','%'.$pro_name.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $project->order($_order);
        }
        $list   = $project->limit($start, $length)->select();
        $result = $project->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        $data   = [];
        foreach ($list as $key => $item) {
            $list[$key]['last_time'] = date('Y-m-d H:i:s', $item['last_time']);
            $data[] = $item->toArray();
        }
        $return['data']            = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal']    = $total;

        echo json_encode($return);
    }
    
    /**
     * 项目添加页面
     */
    public function add(){
        if(request()->isPost()){
            $project = model('Project');
            if($project->createProject(input())){
                $data['url'] = url('Project/index');
                $this->result($data, 1, '添加项目成功', 'json');
            }else{
                $error = $project->getError() ? $project->getError() : '添加项目失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $model = model("dict");
            
            //一级行业类
            $top_industry = $model->getTopIndustry();
            if (!empty($top_industry)) {
                foreach ($top_industry as $key => $each_top_industry) {
                    //二级行业
                    $son_industry = $model->getIndustry($each_top_industry['id']);
                    if (!empty($son_industry)) {
                        $top_industry[$key]['son'] = $son_industry; 
                    } else {
                        $top_industry[$key]['son'] = [];
                    }
                    
                }
            }
            $this->assign('inc_industry' , $top_industry);
            
            //业务细分get
            $inc_service    = $model->getServiceTypeDict();
            $this->assign('inc_service' , $inc_service);
            
            $inc_area       = $model->getDictByType('to_province');//所属区域
            $this->assign('inc_area' , $inc_area);
            
            $capital_plan   = $model->getDictByType('capital_plan');
            $this->assign('capital_plan' , $capital_plan);
            
            $transfer_type  = $model->getDictByType("transfer_type");
            $this->assign('transfer_type' , $transfer_type);
            
            $hierarchy      = $model->getDictByType("hierarchy");
            $this->assign('hierarchy' , $hierarchy);
            
            $sign           = $model->getDictByType("sign");
            $this->assign('sign' , $sign);
            
            $display_type   = array();
            $display_type   = array("不锁定"=>0,"锁定"=>1);
            
            //$display_type   = array( "id" =>0, "id" =>"锁定");
            $this->assign('display_type' , $display_type);
            
            return view();
        }
    }
    
    /**
     * 项目编辑页面
     */
    public function edit(){
        if(request()->isPost()){
            $project = model('Project');
            if($project->createProject(input())){
                $data['url'] = url('Project/index');
                $this->result($data, 1, '修改项目成功', 'json');
            }else{
                $error = $project->getError() ? $project->getError() : '修改项目失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('pro_id/d');
            $model = model("project");
            $info = $model->getProjectInfoById($id);
            $info['industry'] = explode(',', $info['inc_industry']);
            $info['area'] = explode(',', $info['inc_area']);
            $info['sign'] = explode(',', $info['inc_sign']);
            $info['build_time'] = date('Y-m-d' , $info['build_time']);
            
            $model = model("material_library");
            $top_img = $model->getMaterialInfoById($info['top_img']);
            $info['top_img_url'] = $top_img['name'];
            //获取首页头图地址
            $index_img = $model->getMaterialInfoById($info['index_img']);
            $info['index_img_url'] = $index_img['name'];
            $business_plan = $model->getMaterialInfoById($info['business_plan']);
            $info['business_plan_url'] = $business_plan['name'];
            $factor_table = $model->getMaterialInfoById($info['factor_table']);
            $info['factor_table_url'] = $factor_table['name'];
            $company_video = $model->getMaterialInfoById($info['company_video']);
            $info['company_video_url'] = $company_video['name'];
            
            $model = model("material_library");
            
            $str = '';$url = [];$config = [];$arr = [];
            if($info['enterprise_url'] != ''){
                $enterprise_url = explode(',', $info['enterprise_url']);
                foreach ($enterprise_url as $key => $value) {
                    $material = $model->getMaterialInfoById($value);
                    array_push($arr, $material['url']);
                    $str .= $material['ml_id'].'-init_'.$key.',';
                    array_push($url, 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . $material['url']);
                    $config[$key]['caption'] = $material['name'];
                    $config[$key]['size'] = $material['filesize'];
                    $config[$key]['key'] = $key;
                    $config[$key]['url'] = url("Project/deleteImg");
                }
                $info['enterprise_url'] = $str;
                
            }
            $info['enterprise_url_arr'] = $arr;
            $this->assign('enterprise_url_url' , json_encode($url));
            $this->assign('enterprise_url_config' , json_encode($config));
            $str = '';$url = [];$config = [];$arr = [];
            if($info['company_url'] != ''){
                $company_url = explode(',', $info['company_url']);
                foreach ($company_url as $key => $value) {
                    $material = $model->getMaterialInfoById($value);
                    array_push($arr, $material['url']);
                    $str .= $material['ml_id'].'-init_'.$key.',';
                    array_push($url, 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . $material['url']);
                    $config[$key]['caption'] = $material['name'];
                    $config[$key]['size'] = $material['filesize'];
                    $config[$key]['key'] = $key;
                    $config[$key]['url'] = url("Project/deleteImg");
                }
                $info['company_url'] = $str;
                
            }
            $info['company_url_arr'] = $arr;
            $this->assign('company_url_url' , json_encode($url));
            $this->assign('company_url_config' , json_encode($config));
            $str = '';$url = [];$config = [];$arr = [];
            if($info['product_url'] != ''){
                $product_url = explode(',', $info['product_url']);
                foreach ($product_url as $key => $value) {
                    $material = $model->getMaterialInfoById($value);
                    array_push($arr, $material['url']);
                    $str .= $material['ml_id'].'-init_'.$key.',';
                    array_push($url, 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . $material['url']);
                    $config[$key]['caption'] = $material['name'];
                    $config[$key]['size'] = $material['filesize'];
                    $config[$key]['key'] = $key;
                    $config[$key]['url'] = url("Project/deleteImg");
                }
                $info['product_url'] = $str;
                
            }
            $info['product_url_arr'] = $arr;
            $this->assign('product_url_url' , json_encode($url));
            $this->assign('product_url_config' , json_encode($config));
            $str = '';$url = [];$config = [];$arr = [];
            if($info['analysis_url'] != ''){
                $analysis_url = explode(',', $info['analysis_url']);
                foreach ($analysis_url as $key => $value) {
                    $material = $model->getMaterialInfoById($value);
                    array_push($arr, $material['url']);
                    $str .= $material['ml_id'].'-init_'.$key.',';
                    array_push($url, 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . $material['url']);
                    $config[$key]['caption'] = $material['name'];
                    $config[$key]['size'] = $material['filesize'];
                    $config[$key]['key'] = $key;
                    $config[$key]['url'] = url("Project/deleteImg");
                }
                $info['analysis_url'] = $str;
                
            }
            $info['analysis_url_arr'] = $arr;
            $this->assign('analysis_url_url' , json_encode($url));
            $this->assign('analysis_url_config' , json_encode($config));
            
            $this->assign('info' , $info);
            $model = model("dict");
            
            //get业务细分list
            $inc_service = $model->getServiceTypeDict();
            $this->assign('inc_service' , $inc_service);
            
            //一级行业类
            $top_industry = $model->getTopIndustry();
            if (!empty($top_industry)) {
                foreach ($top_industry as $key => $each_top_industry) {
                    //二级行业
                    $son_industry = $model->getIndustry($each_top_industry['id']);
                    if (!empty($son_industry)) {
                        $top_industry[$key]['son'] = $son_industry; 
                    } else {
                        $top_industry[$key]['son'] = [];
                    }
                    
                }
            }
            $this->assign('inc_industry' , $top_industry);
            
            $inc_area       = $model->getDictByType('to_province');
            $this->assign('inc_area' , $inc_area);
            
            $capital_plan   = $model->getDictByType('capital_plan');
            $this->assign('capital_plan' , $capital_plan);
            
            $transfer_type = $model->getDictByType("transfer_type");
            $this->assign('transfer_type' , $transfer_type);
            
            $hierarchy     = $model->getDictByType("hierarchy");
            $this->assign('hierarchy' , $hierarchy);
            
            $inc_sign      = $model->getDictByType("sign");
            $this->assign('inc_sign' , $inc_sign);
            return view();
        }
    }
    
    /**
     * 项目详细页面
     */
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("project");
        $info = $model->getProjectInfoById($id);
        $model = model("dict");
        $industry = explode(',', $info['inc_industry']);
        $inc_industry = '';
        foreach ($industry as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_industry .= $dict['value'].' , ';
        }
        $info['inc_industry'] = trim($inc_industry , ' , ');
        $area = explode(',', $info['inc_area']);
        $inc_area = '';
        foreach ($area as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_area .= $dict['value'].' , ';
        }
        $info['inc_area'] = trim($inc_area , ' , ');
        $sign = explode(',', $info['inc_sign']);
        $inc_sign = '';
        foreach ($sign as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_sign .= $dict['value'].' , ';
        }
        $info['inc_sign'] = trim($inc_sign , ' , ');
        $capital_plan = $model->getDictInfoById($info['capital_plan']);
        $info['capital_plan'] = $capital_plan['value'];
        $info['build_time'] = date('Y-m-d' , $info['build_time']);
        $transfer_type = $model->getDictInfoById($info['transfer_type']);
        $info['transfer_type'] = $transfer_type['value'];
        $hierarchy = $model->getDictInfoById($info['hierarchy']);
        $info['hierarchy'] = $hierarchy['value'];
        $info['investment_lights'] = $this->getTextAreaValue($info['investment_lights']);
        $info['enterprise_des'] = $this->getTextAreaValue($info['enterprise_des']);
        $info['company_des'] = $this->getTextAreaValue($info['company_des']);
        $info['product_des'] = $this->getTextAreaValue($info['product_des']);
        $info['analysis_des'] = $this->getTextAreaValue($info['analysis_des']);
        
        $model = model("material_library");
        $top_img = $model->getMaterialInfoById($info['top_img']);
        $info['top_img'] = $top_img['name'];
        $business_plan = $model->getMaterialInfoById($info['business_plan']);
        //首页头图
        $index_img = $model->getMaterialInfoById($info['index_img']);
        $info['index_img'] = $index_img['name'];
        $info['business_plan'] = $business_plan['name'];
        $factor_table = $model->getMaterialInfoById($info['factor_table']);
        $info['factor_table'] = $factor_table['name'];
        $company_video = $model->getMaterialInfoById($info['company_video']);
        $info['company_video'] = $company_video['name'];
        
        $arr = [];
        if($info['enterprise_url'] != ''){
            $enterprise_url = explode(',', $info['enterprise_url']);
            foreach ($enterprise_url as $key => $value) {
                $material = $model->getMaterialInfoById($value);
                array_push($arr, $material['url']);
            }
        }
        $info['enterprise_url_arr'] = $arr;
        $arr = [];
        if($info['company_url'] != ''){
            $company_url = explode(',', $info['company_url']);
            foreach ($company_url as $key => $value) {
                $material = $model->getMaterialInfoById($value);
                array_push($arr, $material['url']);
            }
        }
        $info['company_url_arr'] = $arr;
        $arr = [];
        if($info['product_url'] != ''){
            $product_url = explode(',', $info['product_url']);
            foreach ($product_url as $key => $value) {
                $material = $model->getMaterialInfoById($value);
                array_push($arr, $material['url']);
            }
        }
        $info['product_url_arr'] = $arr;
        $arr = [];
        if($info['analysis_url'] != ''){
            $analysis_url = explode(',', $info['analysis_url']);
            foreach ($analysis_url as $key => $value) {
                $material = $model->getMaterialInfoById($value);
                array_push($arr, $material['url']);
            }
        }
        $info['analysis_url_arr'] = $arr;
            
        $this->assign('info' , $info);
        $model = model("dict");
        $qa_type = $model->getDictByType("qa_type");
        $this->assign('qa_type' , $qa_type);
        //问答分页
        $model = model("project_qa");
        $qa_list = $model->getProjectListByProId($id , 0 , 5);
        $count = $model->getProjectListByProId($id);
        foreach ($qa_list as $key => $value) {
            $type = model("dict")->getDictInfoById($value['type']);
            $qa_list[$key]['type'] = $type['value'];
        }
        $this->assign('qa_list' , $qa_list);
        $num = intval(count($count)/5);
        if(count($count)%5 > 0){
            $total = $num++;
        } else {
            $total = $num;
        }
        $this->assign('total1' , $num);
        //新闻分页
        $model = model("project_news");
        $news_list = $model->getProjectListByProId($id , 0 , 5);
        $count = $model->getProjectListByProId($id);
        $this->assign('news_list' , $news_list);
        $num = intval(count($count)/5);
        if(count($count)%5 > 0){
            $total = $num++;
        } else {
            $total = $num;
        }
        $this->assign('total2' , $num);
        return view();
    }
    
    /**
     * 项目保存操作（新增与修改）
     */
    public function save(){
        $data = input();
        $con  = model('project');
        if(isset($data['pro_id'])){
            if ($con->createProjectDraft(input())) {
                $data['url'] = url('Project/index');
                $this->result($data, 1, '修改项目成功', 'json');
            } else {
                $error = $con->getError() ? $con->getError() : '修改项目失败';
                $this->result('', 0, $error, 'json');
            }
        } else {
            if ($con->createProjectDraft(input())) {
                $data['url'] = url('Project/index');
                $this->result($data, 1, '添加项目成功', 'json');
            } else {
                $error = $con->getError() ? $con->getError() : '添加项目失败';
                $this->result('', 0, $error, 'json');
            }
        }
    }
    
    /**
     * 项目结束操作
     */
    public function end(){
        $id = $this->request->param('id/d');
        $model = model("project");
        if($model->setProjectStatus($id , 3)){
            $data['url'] = url('Project/index');
            $this->result($data, 1, '设置成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '设置失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 项目隐藏操作
     */
    public function stop(){
        $id = $this->request->param('id/d');
        $model = model("project");
        if($model->stopProject($id)){
            $data['url'] = url('Project/index');
            $this->result($data, 1, '隐藏成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '隐藏失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 项目显示操作
     */
    public function start(){
        $id = $this->request->param('id/d');
        $model = model("project");
        if($model->startProject($id)){
            $data['url'] = url('Project/index');
            $this->result($data, 1, '显示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '显示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 
     */
    public function deleteImg(){
        $data = input();
        $this->result($data['key'], 1, '删除成功！', 'json');
    }
    
    /**
     * 发送微信消息推送（单个群发）
     */
    public function sendinfo(){
        $id         = $this->request->param('id/d');
        $uid        = $this->request->post('uid');
        $userType   = $this->request->post('userType');
        $name       = $this->request->post('name');
        
        $model = model("project");
        $res   = $model->sendProjectInfo($id,$uid,$userType,$name);
        if($res){
            $data['url'] = url('Project/index');
            $this->result($data, 1, '推送成功', 'json');
        }else{
            $error = "微信推送对象为空";
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 微信消息推送页面
     */
    public function detail2(){
        $id     = $this->request->param('id/d');
        $model  = model("Project");
        $info   = $model->getProjectInfoById($id);
        $this->assign("info" , $info);
        $this->assign("id" , $id);
        return view();
    }
}