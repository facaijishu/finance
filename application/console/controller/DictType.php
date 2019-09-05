<?php
namespace app\console\controller;

class DictType extends ConsoleBase{
    public function index(){
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $name = $this->request->get('name', '');

        $dict_type = model('dict_type');
        //搜索条件
        if('' !== $name) {
            $dict_type->where('name','like','%'.$name.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $dict_type->order($_order);
        }
        $dict_type->field('SQL_CALC_FOUND_ROWS *');
        $list = $dict_type->limit($start, $length)->select();
        $result = $dict_type->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $model = model("User");
        foreach ($list as $key => $item) {
            $info = $model->getUserById($item['create_uid']);
            $list[$key]['create_uid'] = $info['real_name'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function add(){
        $model = model("dict_type");
        if($model->createDictType(input())){
            $data['url'] = url('DictType/index');
            $this->result($data, 1, '添加数据类型成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '添加数据类型失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function edit(){
        $model = model("dict_type");
        if($model->createDictType(input())){
            $data['url'] = url('DictType/index');
            $this->result($data, 1, '修改数据类型成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '修改数据类型失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function find(){
        $id = $this->request->param('id/d', 0);
        $model = model("dict_type");
        $info = $model->getDictTypeInfoById($id);
        if(!empty($info)){
            $this->result($info, 1, '查看成功！', 'json');
        }else{
            $error = $sheet->getError() ? $sheet->getError() : '查看失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('dict_type');
        if($model->stopDictType($id)){
            $this->result('', 1, '停止成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '停止失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('dict_type');
        if($model->startDictType($id)){
            $this->result('', 1, '开启成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '开启失败!';
            $this->result('', 0, $error, 'json');
        }
    }
}