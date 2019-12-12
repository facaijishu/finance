<?php
namespace app\console\controller;
set_time_limit(0);
class ProjectPlus extends ConsoleBase{
    
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
        $project      = model('Project')->alias("p")
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
            if($status==1){
                $project->where(['status'=>0]);
            }
            if($status==2){
                $project->where(['status'=>2,'flag'=>1]);
            }
            if($status==3){
                $project->where(['status'=>2,'flag'=>0]);
            }
            
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
            if($project->saveProject(input())){
                $data['url'] = url('ProjectPlus/index');
                $this->result($data, 1, '添加项目成功', 'json');
            }else{
                $error = $project->getError() ? $project->getError() : '添加项目失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $dict          = model("dict");
            
            //行业细分一级
            $topindustry   = $dict->getTopIndustry();
            $this->assign('topindustry' , $topindustry);
            
            //投资阶段
            $stage   = $dict->getStage();
            $this->assign('stage' , $stage);
            
            //所属区域
            $area   = $dict->getArea();
            $this->assign('area' , $area);
            
            return view();
        }
    }
    
    /**
     * 项目编辑页面
     */
    public function edit(){
        if(request()->isPost()){
            $project    = model('Project');
            if($project->createProject(input())){
                $data['url'] = url('ProjectPlus/index');
                $this->result($data, 1, '修改项目成功', 'json');
            }else{
                $error = $project->getError() ? $project->getError() : '修改项目失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id    = $this->request->param('pro_id/d');
            $model = model("project");
            $info  = $model->getProjectInfoById($id);
            
            $dict          = model("dict");
            
            //行业细分一级
            $topindustry   = $dict->getTopIndustry();
            faLog(json_encode($topindustry));
            $this->assign('topindustry' , $topindustry);
            
            //行业细分二级
            $secindustry   = $dict->getIndustry($info['inc_top_industry']);
            $this->assign('secindustry' , $secindustry);
            
            //投资阶段
            $stage   = $dict->getStage();
            $this->assign('stage' , $stage);
            
            //所属区域
            $area   = $dict->getArea();
            $this->assign('area' , $area);
            
            
            $model      = model("material_library");
            $top_img    = $model->getMaterialInfoById($info['top_img']);
            $info['top_img_url'] = $top_img['name'];
            
            //获取首页头图地址
            $index_img                  = $model->getMaterialInfoById($info['index_img']);
            $info['index_img_url']      = $index_img['name'];
            $business_plan              = $model->getMaterialInfoById($info['business_plan']);
            $info['business_plan_url']  = $business_plan['name'];
            
            $this->assign('info' , $info);
      
            return view();
        }
    }
    
    /**
     * 项目详细页面
     */
    public function detail(){
        $id             = $this->request->param('id/d');
        $model          = model("project");
        $info           = $model->getProjectInfoById($id);
        
        $model          = model("dict");
        $dict                       = $model->getDictList($info['inc_industry']);
        $info['inc_industry']       = $dict['dict_name'];
        
        $dict                       = $model->getDictList($info['inc_area']);
        $info['inc_area']           = $dict['dict_name'];
        
        $dict                       = $model->getDictList($info['invest_stage']);
        $info['invest_stage']       = $dict['dict_name'];
        
        $capital_plan               = $model->getDictInfoById($info['capital_plan']);
        $info['capital_plan']       = $capital_plan['value'];
        $info['build_time']         = date('Y-m-d' , $info['build_time']);
        
        $transfer_type              = $model->getDictInfoById($info['transfer_type']);
        $info['transfer_type']      = $transfer_type['value'];
        
        $info['enterprise_des']     = $this->getTextAreaValue($info['enterprise_des']);
        $info['product_des']        = $this->getTextAreaValue($info['product_des']);
        $info['analysis_des']       = $this->getTextAreaValue($info['analysis_des']);
        
        $model                      = model("material_library");
        $top_img                    = $model->getMaterialInfoById($info['top_img']);
        $info['top_img']            = $top_img['name'];
        
        $business_plan              = $model->getMaterialInfoById($info['business_plan']);
        $info['business_plan']      = $business_plan['name'];
        
        //首页头图
        $index_img                  = $model->getMaterialInfoById($info['index_img']);
        $info['index_img']          = $index_img['name'];
        if($info['status']==2){
            if($info['flag']==1){
                $info['status_name']    = "已上架";
            }else{
                $info['status_name']    = "下架中";
            }
            
        }else{
            $info['status_name']    = "待审核";
        }
        
        $this->assign('info' , $info);
        
        return view();
    }
    
    /**
     * 项目保存操作（新增与修改）
     */
    public function save(){
        $data = input();
        $con  = model('project');
        if(isset($data['pro_id'])){
            if ($con->saveProject(input())) {
                $data['url'] = url('ProjectPlus/index');
                $this->result($data, 1, '修改项目成功', 'json');
            } else {
                $error = $con->getError() ? $con->getError() : '修改项目失败';
                $this->result('', 0, $error, 'json');
            }
        } else {
            if ($con->saveProject(input())) {
                $data['url'] = url('ProjectPlus/index');
                $this->result($data, 1, '添加项目成功', 'json');
            } else {
                $error = $con->getError() ? $con->getError() : '添加项目失败';
                $this->result('', 0, $error, 'json');
            }
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
    
    
    /**
     * 项目审核通过操作
     */
    public function proApprove(){
        $id    = $this->request->param('id/d');
        $model = model("project");
        if($model->approveProject($id)){
            $data['url'] = url('ProjectPlus/index');
            $this->result($data, 1, '显示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '显示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 项目上架操作
     */
    public function proUp(){
        $id    = $this->request->param('id/d');
        $model = model("project");
        if($model->changeProject($id,1)){
            $data['url'] = url('ProjectPlus/index');
            $this->result($data, 1, '显示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '显示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    
    /**
     * 项目下架操作
     */
    public function proDown(){
        $id    = $this->request->param('id/d');
        $model = model("project");
        if($model->changeProject($id,0)){
            $data['url'] = url('ProjectPlus/index');
            $this->result($data, 1, '显示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '显示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 项目二维码生成
     */
    public function proCreatedQr(){
        $data  = input();
        $model = model("project");
        if($model->createdQrCode($data['id'])){
            $data['url'] = url('ProjectPlus/index');
            $this->result($data, 1, '显示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '显示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 获取二级分类
     */
    public function getSubDict(){
        $data  = input();
        $dict  = model("Dict");
        $list  = $dict->getSubDict($data['id']);
        $this->result($list, 1, '', 'json');
    }
}