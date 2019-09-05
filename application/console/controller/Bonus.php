<?php
namespace app\console\controller;

class Bonus extends ConsoleBase{
    public function index(){
        $data = input();
        $this->assign('menu' , $data['menu_id']);
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        $bonus = model('Bonus');
        //搜索条件
        $bonus->where('type','eq','day');
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $bonus->order($_order);
        }
        $bonus->field('SQL_CALC_FOUND_ROWS *');
        $list = $bonus->limit($start, $length)->select();
        $result = $bonus->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $user = model("User");
        foreach ($list as $key => $item) {
            $create_user = $user->getUserById($item['create_uid']);
            $list[$key]['create_uid'] = $create_user['real_name'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function reads(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        $bonus = model('Bonus');
        //搜索条件
        $bonus->where('type','eq','month');
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $bonus->order($_order);
        }
        $bonus->field('SQL_CALC_FOUND_ROWS *');
        $list = $bonus->limit($start, $length)->select();
        $result = $bonus->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $user = model("User");
        foreach ($list as $key => $item) {
            $create_user = $user->getUserById($item['create_uid']);
            $list[$key]['create_uid'] = $create_user['real_name'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function addDay(){
        if(request()->isPost()){
            $model = model("Bonus");
            if($model->createBonusDay(input())){
                $data['url'] = url('Bonus/index');
                $this->result($data, 1, '添加分红成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '添加分红失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            return view();
        }
    }
    public function addMonth(){
        if(request()->isPost()){
            $model = model("Bonus");
            if($model->createBonusMonth(input())){
                $data['url'] = url('Bonus/index');
                $this->result($data, 1, '添加分红成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '添加分红失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            return view();
        }
    }
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('Bonus');
        if($model->stopBonus($id)){
            $this->result('', 1, '停止成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '停止失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('Bonus');
        if($model->startBonus($id)){
            $this->result('', 1, '开启成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '开启失败!';
            $this->result('', 0, $error, 'json');
        }
    }
}