<?php
namespace app\console\controller;

class ProjectQa extends ConsoleBase{
    public function index(){
        return view();
    }
    public function add(){
        $data = input();
        $qa = model('project_qa');
        if($qa->createProjectQa($data)){
            $data['url'] = url('Project/detail' , ['id' => $data['pro_id']]);
            $this->result($data, 1, '添加问答成功', 'json');
        }else{
            $error = $qa->getError() ? $qa->getError() : '添加问答失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function edit(){
        $data = input();
        $qa = model('project_qa');
        if($qa->createProjectQa($data)){
            $data['url'] = url('Project/detail' , ['id' => $data['pro_id']]);
            $this->result($data, 1, '修改问答成功', 'json');
        }else{
            $error = $qa->getError() ? $qa->getError() : '修改问答失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function set(){
        $id = $this->request->param('id/d');
        $pro_id = $this->request->param('pro_id/d');
        $action = $this->request->param('action/s');
        $model = model("project_qa");
        if($model->setProjectQa($id , $action)){
            $data['url'] = url('Project/detail' , ['id' => $pro_id]);
            $this->result($data, 1, '修改成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '修改失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function find(){
        $id = $this->request->param('id/d');
        $model = model("project_qa");
        $info = $model->getProjectQaInfoById($id);
        if(!empty($info)){
            $this->result($info, 1, '查看成功！', 'json');
        }else{
            $error = $sheet->getError() ? $sheet->getError() : '查看失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function pages(){
        $total = $this->request->param('total/s');
        $page = $this->request->param('page/s');
        $pro_id = $this->request->param('pro_id/d');
        $id = $this->request->param('id/s');
        if(($page == 1 && $id == 'previous') || ($page == $total && $id == 'next')){
            if($id == 'previous'){
                $this->result('', 2, '已到首页', 'json');
            } else {
                $this->result('', 2, '已到尾页', 'json');
            }
        } else {
            if($id == 'previous'){
                $start = (intval($page)-1-1)*5;
                $page = intval($page)-1;
            } else {
                $start = (intval($page)+1-1)*5;
                $page = intval($page)+1;
            }
            $model = model("project_qa");
            $qa_list = $model->getProjectListByProId($pro_id , $start , 5);
            foreach ($qa_list as $key => $value) {
                $type = model("dict")->getDictInfoById($value['type']);
                $qa_list[$key]['type'] = $type['value'];
            }
            $data = [];
            $data['page'] = $page;
            $data['content'] = $qa_list;
            if($qa_list){
                $this->result($data, 1, '翻页成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '翻页失败';
                $this->result('', 0, $error, 'json');
            }
        }
    }
}