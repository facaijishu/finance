<?php

namespace app\console\controller;
class OrganizePlus extends ConsoleBase
{       
    //机构中心首页
    public function index()
    {
        return view();
    }
    //读取机构中心列表
    public function read()
    {
        $return         = [];
        $order          = $this->request->get('order/a', []);
        $start          = $this->request->get('start', 0);
        $length         = $this->request->get('length', config('paginate.list_rows'));
        $create_date1   = $this->request->get('create_date1', '');
        $create_date2   = $this->request->get('create_date2', '');
        $flag           = $this->request->get('flag', '');
        $org_name       = $this->request->get('org_name', '');

        $organize       = model('Organize')->alias("o")
                                           ->join("QrcodeDirection qd","o.qr_code=qd.id","LEFT")
                                           ->field("SQL_CALC_FOUND_ROWS o.*,qd.qrcode_path");

        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $organize->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $organize->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $organize->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $flag) {
            $organize->where(['flag'=>$flag]);
        }
        if('' !== $org_name) {
            $organize->where('org_name','like','%'.$org_name.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order                  = [];
                $_order[$item['column']] = $item['dir'];
            }
            $organize->order($_order);
        }
        $list   = $organize->limit($start, $length)->select();
        $result = $organize->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        $data   = [];
        foreach ($list as $key => $item) {
            $list[$key]['last_time'] = date('Y-m-d H:i:s', $item['last_time']);
            $data[] = $item->toArray();
        }
        $return['data']             = $data;
        $return['recordsFiltered']  = $total;
        $return['recordsTotal']     = $total;

        echo json_encode($return);

    }
        
    //机构详情
    public function detail(){
        $id = $this->request->param('id/d');
        $model  = model("organize");
        $info   = $model->getOrganizeInfoById($id);
       
        $model  = model("dict");
        //单位
        $unit   = $model->getDictInfoById($info['unit']);
        $info['unit'] = $unit['value'];
        //投资企业+所属行业
        $arr_target = [];
        if(strpos($info['inc_target'], '-')){
            $inc_targets = explode('-', $info['inc_target']);
            foreach ($inc_targets as $key => $value) {
               
               $inc_targets2 = explode('+', $value);
               if(count($inc_targets2)==2){
                      $arr_target[] = array(
                      'target_company'=>$inc_targets2[0],
                      'target_industry'=>$inc_targets2[1]
                    );
               }  
            }
        }else{
            if(strpos($info['inc_target'],'+') !== false){
                $inc_targets2 = explode('+', $info['inc_target']);
                if(count($inc_targets2) == 2){
                    $arr_target[] = array(
                      'target_company'=>$inc_targets2[0],
                      'target_industry'=>$inc_targets2[1]
                   );
                }
            }
        }
        $info['inc_target'] = $arr_target;
        
        //投资方向
        $model              = model("Dict");
        $result             = $model->getSecDictList($info['inc_industry']);
        $info['dict_list']  = $result;
        
        //投资阶段
        $stage              = $model->getDictStr($info['invest_stage']);
        $info['stage']      = $stage;
        
        //业务类型
        $type               = $model->getDictStr($info['inc_type']);
        $info['inc_type']   = $type;
        
        
        //投资方向
        $industry           = explode(',', $info['inc_industry']);
        $inc_industry       = '';
        foreach ($industry as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_industry .= $dict['value'].' 、 ';
        }
        $info['inc_industry'] = trim($inc_industry , ' 、 ');
        
        //所属区域
        $area = explode(',', $info['inc_area']);
        $inc_area = '';
        foreach ($area as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_area .= $dict['value'].' 、 ';
        }
        $info['inc_area'] = trim($inc_area , ' 、');
        
        //资金性质
        $funds = explode(',', $info['inc_funds']);
        $inc_funds = '';
        foreach ($funds as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_funds .= $dict['value'].' , ';
        }
        $info['inc_funds'] = trim($inc_funds , ' , ');
        
        //资本类型
        $capital = explode(',', $info['inc_capital']);
        $inc_capital = '';
        foreach ($capital as $key => $value) {
            $dict = $model->getDictInfoById($value);
            $inc_capital .= $dict['value'].' , ';
        }
        $info['inc_capital'] = trim($inc_capital , ' , ');
        
        
        $model = model("MaterialLibrary");
        //头像
        $top_img = $model->getMaterialInfoById($info['top_img']);
        $info['top_img'] = $top_img['url'];
        //微信头像
        $info['wx_img'] = '';
        if(!empty($info['wx_uid'])){
            $member = model('member')->getMemberInfoById($info['wx_uid']);
            $info['wx_img'] = $member['userPhoto']; 
        }
        $this->assign('info',$info);
        return view();
    }
    
    //保存机构为草稿
    public function save()
    {
        $data   = input();
        $con    = model('Organize');
        if(isset($data['org_id'])){
            if ($con->createOrganizeDraft($data)) {
                $data['url'] = url('OrganizePlus/index');
                $this->result($data, 1, '修改项目成功', 'json');
            } else {
                $error = $con->getError() ? $con->getError() : '修改项目失败';
                $this->result('', 0, $error, 'json');
            }
        } else {

            if ($con->createOrganizeDraft($data)) {
                $data['url'] = url('OrganizePlus/index');
                $this->result($data, 1, '添加机构成功', 'json');
            } else {
                $error = $con->getError() ? $con->getError() : '添加机构失败';
                $this->result('', 0, $error, 'json');
            }
        }
    }
        
    //添加机构
    public function add()
    {
        if(request()->isPost()){
            $organize = model('Organize');
            if($organize->createOrganize(input())){
                $data['url'] = url('OrganizePlus/index');
                $this->result($data, 1, '添加机构成功', 'json');
            }else{
                $error = $organize->getError() ? $organize->getError() : '添加机构失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $model = model("dict");

            $management_scale = $model->getDictByType('management_scale');//管理规模
            $this->assign('management_scale' , $management_scale);

            // $inc_industry = $model->getDictByType('industry');//投资方向
            // $this->assign('inc_industry' , $inc_industry);
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
            $inc_area = $model->getDictByType('to_area');//所属区域
            $this->assign('inc_area' , $inc_area);

            $investment_mode = $model->getDictByType('investment_mode');//投资方式
            $this->assign('investment_mode' , $investment_mode);

            $funds_nature = $model->getDictByType("funds_nature");//资金性质
            $this->assign('funds_nature' , $funds_nature);

            $capital_type = $model->getDictByType("capital_type");//资本类型
            $this->assign('capital_type' , $capital_type);
            
            $invest_stage = $model->getDictByType("invest_stage");//投资阶段
            $this->assign('invest_stage' , $invest_stage);
            
            
            $type         = $model->getDictByType("business_type");//机构业务类型
            $this->assign('type' , $type);

            return view();
        }
    }
        
    //隐藏机构
    public function stop()
    {
        $id = $this->request->param('id/d');
        $model = model("organize");
        if($model->stopOrganize($id)){
            $data['url'] = url('OrganizePlus/index');
            $this->result($data, 1, '隐藏成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '隐藏失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    //显示机构
    public function start(){
        $id     = $this->request->param('id/d');
        $model  = model("organize");
        $re     = $model->startOrganize($id);
        if($re){
            $data['url'] = url('OrganizePlus/index');
            $this->result($data, 1, '显示成功', 'json');
        }elseif($re === null){
            $error = $model->getError() ? $model->getError() : '请先填写完整信息再显示';
            $this->result('', 2, $error, 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '显示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    
    //机构认领
    public function claim(){
        $id     = $this->request->param('id/d');
        $model  = model("organize");
        $re     = $model->claimOrganize($id);
        if($re){
            $data['url'] = url('OrganizePlus/index');
            $this->result($data, 1, '显示成功', 'json');
        }elseif($re === null){
            $error = $model->getError() ? $model->getError() : '请先填写完整信息再显示';
            $this->result('', 2, $error, 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '显示失败';
            $this->result('', 0, $error, 'json');
        }
        
    }
    
    //修改机构
    public function edit()
    {
       if(request()->isPost()){
            $organize = model('Organize');
            if($organize->createOrganize(input())){
                $data['url'] = url('OrganizePlus/index');
                $this->result($data, 1, '修改项目成功', 'json');
            }else{
                $error = $organize->getError() ? $project->getError() : '修改项目失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $dict = model("dict");
            
            //管理规模
            $management_scale = $dict->getDictByType('management_scale');
            $this->assign('management_scale' , $management_scale);
            
            // //投资方向
            // $inc_industry = $dict->getDictByType('industry');
            // $this->assign('inc_industry' , $inc_industry);
            
            //一级行业类+二级行业类
            $top_industry = $dict->getTopIndustry();
            if (!empty($top_industry)) {
                foreach ($top_industry as $key => $each_top_industry) {
                    //二级行业
                    $son_industry = $dict->getIndustry($each_top_industry['id']);
                    if (!empty($son_industry)) {
                        $top_industry[$key]['son'] = $son_industry; 
                    } else {
                        $top_industry[$key]['son'] = [];
                    }
                    
                }
            }
            $this->assign('inc_industry' , $top_industry);
            //选中的行业标签
            
            //所属区域
            $inc_area       = $dict->getDictByType('to_area');
            $this->assign('inc_area' , $inc_area);
            
            //资金性质
            $funds_nature   = $dict->getDictByType("funds_nature");
            $this->assign('funds_nature' , $funds_nature);
            
            //投资阶段
            $invest_stage   = $dict->getDictByType("invest_stage");
            $this->assign('invest_stage' , $invest_stage);
            
            //机构业务类型
            $type           = $dict->getDictByType("business_type");
            $this->assign('type' , $type);
            
            //资本类型
            $capital_type   = $dict->getDictByType("capital_type");
            $this->assign('capital_type' , $capital_type);
            
            //机构名称
            $id         = $this->request->param('org_id/d');
            $organize   = model("organize");
            $info       = $organize->getOrganizeInfoById($id);
            
            $arr_target = [];
            if(strpos($info['inc_target'], '-')){
                $inc_targets = explode('-', $info['inc_target']);
                foreach ($inc_targets as $key => $value) {
                   
                   $inc_targets2 = explode('+', $value);
                   if(count($inc_targets2) == 2){
                          $arr_target[] = array(
                          'target_company'=>$inc_targets2[0],
                          'target_industry'=>$inc_targets2[1]
                       );
                   }  
                }
            }else{
                if(strpos($info['inc_target'],'+') !== false){
                    $inc_targets2 = explode('+', $info['inc_target']);
                    if(count($inc_targets2) == 2){
                             $arr_target[] = array(
                          'target_company'=>$inc_targets2[0],
                          'target_industry'=>$inc_targets2[1]
                       );
                    }
                }
            }
            $info['inc_target'] = $arr_target; 
            
            $num = count($info['inc_target']);
            $this->assign('num',$num);
            
            //已选中的投资方向
            $info['industry']   = explode(',', $info['inc_industry']);
            
            //已选中的所属区域
            $info['area']       = explode(',', $info['inc_area']);
            
            //已选中的资金性质
            $info['funds']      = explode(',', $info['inc_funds']);
            
            //已选中的所属区域
            $info['stage']       = explode(',', $info['invest_stage']);
            
            //已选中的资本类型
            $info['capital']    = explode(',', $info['inc_capital']);
            
            //已选中的头图
            $material = model('material_library');
            $top_img = $material->getMaterialInfoById($info['top_img']);
            $info['top_img_url'] = $top_img['name'];
            //微信头像
            $info['wx_img'] = '';
            if(!empty($info['wx_uid'])){
                $member = model('member')->getMemberInfoById($info['wx_uid']);
                $info['wx_img'] = $member['userPhoto']; 
            } 
            $this->assign('info',$info);
            return view();
        }
    }
        
    //导出机构中心Excel表格
    public function org_client()
    {
        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $org_name     = $this->request->get('org_name', '');
        $flag = $this->request->get('flag', '');
        $organize = model('Organize');
            
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $organize->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $organize->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $organize->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $org_name){
            $organize->where('org_name','like','%'.$org_name.'%');
        }
        if('' !== $flag){
            $organize->where('flag','eq',$flag);
        }
        //降序
        $organize->order('org_id desc');
        //执行查询
        $list = $organize->select();
        
        $dict = model('dict');
        foreach ($list as $k => $v) {
            //处理unit(万/亿)
             $unit = $dict->getDictInfoById($v['unit']);
             $list[$k]['unit'] = $unit->value;
            //处理inc_target(未名药业+医药-赛弗网络+互联网-蓝天燃气+生物)
             $str_target = '';
            if(strpos($v['inc_target'], '-')){
                $inc_targets = explode('-', $v['inc_target']);
                foreach ($inc_targets as $key => $value) {
                   $inc_targets2 = explode('+', $value);
                   $str_target .= $inc_targets2[0].',';     
                }    
                
            }else{
                if(strpos($v['inc_target'],'+') !== false){
                    $inc_targets2 = explode('+', $v['inc_target']);
                    $str_target .= $inc_targets2[0].',';
                      
                }
            }
            $list[$k]['inc_target'] = $str_target;
            
            //处理inc_industry(1,76,77,78,79,80,82,110)
            $str_industry = '';
            $industry = $dict->getDictInfoByIdSet($v['inc_industry']);
            if(empty($industry)){
                $list[$k]['inc_industry'] = $str_industry;
            }else{
                $industry = $industry[0];
                foreach ($industry as $key => $value) {
                    $str_industry.=$value['value'].',';
                }
                $list[$k]['inc_industry'] = $str_industry;
            }
            
            //处理inc_are a
            $str_area = '';
            $area = $dict->getDictInfoByIdSet($v['inc_area']);
            if(empty($area)){
                $list[$k]['inc_area'] = $str_area;
            }else{
                $area = $area[0];
                foreach ($area as $key => $value) {
                    $str_area.=$value['value'].',';
                }
                $list[$k]['inc_area'] = $str_area;
            } 
            
        }
        if(organize_show($list)){
            return "ok";
        }else{
            return "no";
        }
    }
}