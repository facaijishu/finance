<?php
namespace app\console\controller;

class ProjectHot extends ConsoleBase{
    public function index(){
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $pro_name = $this->request->get('pro_name', '');

        $project = model('Project');
        //搜索条件
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
        $project->where('status','eq','2');
        $project->field('SQL_CALC_FOUND_ROWS *');
        $list = $project->limit($start, $length)->select();
        $result = $project->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function set(){
//        $id = $this->request->get('id/d', 0);
//        $list_order = $this->request->get('list_order/d', 0);
        $model = model("Project");
        if($model->setProjectOrderList(input())){
            $data['url'] = url('ProjectHot/index');
            $this->result($data, 1, '设置排序成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '设置排序失败';
            $this->result('', 0, $error, 'json');
        }
    }
}