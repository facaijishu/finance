<?php
namespace app\console\controller;

class ProjectClue extends ConsoleBase{
    public function index(){
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $pro_name = $this->request->get('pro_name', '');
        $pro_origin = $this->request->get('pro_origin','');
        $project_clue = model('project_clue');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $project_clue->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $project_clue->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $project_clue->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $pro_name) {
            $project_clue->where('pro_name','like','%'.$pro_name.'%');
        }
         if('' !== $pro_origin) {
            if($pro_origin == 0){
               $project_clue->where('pro_origin = 0'); 
            }
            if($pro_origin == 1){
                $project_clue->where('pro_origin > 0');
            }
            
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $project_clue->order($_order);
        }
        $project_clue->field('SQL_CALC_FOUND_ROWS *');
        $list = $project_clue->limit($start, $length)->select();
        $result = $project_clue->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $user = model("Member");
        foreach ($list as $key => $item) {
            $user_info = $user->getMemberInfoById($item['create_uid']);
            $list[$key]['create_uid'] = $user_info['userName'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("project_clue");
        $info = $model->getProjectClueInfoById($id);
        if($info['pro_origin'] > 0){
            $org = model('Organize')->getOrganizeInfoById($info['pro_origin']);
            $info['pro_origin'] = $org['org_name'];
        }else{
            $info['pro_origin'] = '项目投递';
        }
        $model = model("dict");
        $capital_plan = $model->getDictInfoById($info['capital_plan']);
        $info['capital_plan'] = $capital_plan['value'];
        if($info['img'] != ''){
            $arr_img = explode(',', $info['img']);
            $img = [];
            if(count($arr_img) < 6){
                $img[0] = $arr_img;
            } else {
                array_push($img , array_slice($arr_img, 0 , 5));
                array_push($img , array_slice($arr_img, 4));
            }
            $info['img'] = $img;
        }
        
        $model = model("ProjectClueInfo");
        $list = $model->where(['pc_id' => $id])->select();
        if(!empty($list)){
            $model = model("User");
            foreach ($list as $key => $value) {
                $userinfo = $model->getUserById($value['create_uid']);
                $list[$key]['create_user'] = $userinfo['real_name'];
            }
        } else {
            $list = [];
        }
        $info['list'] = $list;
        $this->assign("info" , $info);
        return view();
    }
//    public function set(){
//        $id = $this->request->param('id/d');
//        $model = model("project_clue");
//        if($model->setProjectClueStatus($id , 1)){
//            $data['url'] = url('ProjectClue/index');
//            $this->result($data, 1, '设置成功', 'json');
//        }else{
//            $error = $model->getError() ? $model->getError() : '设置失败';
//            $this->result('', 0, $error, 'json');
//        }
//    }
    public function complete(){
        $data = input();
        $model = model("project_clue_info");
        if($model->completeProjectClue($data)){
            $data['url'] = url('ProjectClue/index');
            $this->result($data, 1, '添加成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '添加失败';
            $this->result('', 0, $error, 'json');
        }
    }
    //导出项目线索excel
    public function pro_clue_client()
    {
            $create_date1 = $this->request->get('create_date1', '');
            $create_date2 = $this->request->get('create_date2', '');
            $pro_name = $this->request->get('pro_name', '');
            $pro_origin = $this->request->get('pro_origin', '');
            $ProjectClue = model('ProjectClue');
                
            //搜索条件
            if('' !== $create_date1 && '' !== $create_date2) {
                $ProjectClue->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
            }elseif ('' !== $create_date1 && '' == $create_date2) {
                $ProjectClue->where('create_time','>',strtotime($create_date1.'00:00:00'));
            }elseif ('' == $create_date1 && '' !== $create_date2) {
                $ProjectClue->where('create_time','<',strtotime($create_date2.'23:59:59'));
            }
            if('' !== $pro_name){
                $ProjectClue->where('pro_name','like','%'.$pro_name.'%');
            }
            if('' !== $pro_origin) {
                if($pro_origin == 0){
                   $project_clue->where('pro_origin = 0'); 
                }
                if($pro_origin == 1){
                    $project_clue->where('pro_origin > 0');
                }
                
            }
            //降序
            $ProjectClue->order('clue_id desc');
            //执行查询
            $list = $ProjectClue->select();
            $dict = model('Dict');
            foreach ($list as $key => $value) {
                    $capital_plan = $dict->getDictInfoById($value['capital_plan']);

                    $list[$key]['capital_plan'] = $capital_plan['value'];

                    if ($value['pro_origin'] > 0) {
                        $org = model('Organize')->getOrganizeInfoById($value['pro_origin']);
             
                        $list[$key]['pro_origin'] = $org['org_name'];
                    }else{
                        $list[$key]['pro_origin'] = '项目投递';
                    } 

                    if ($value['status'] == 1) {
                        $list[$key]['status'] = '已处理';
                    }else{
                        $list[$key]['status'] = '未处理';
                    }
            }
            
            if(project_clue_show($list)){
                return "ok";
            }else{
                return "no";
            }
    }    

}
