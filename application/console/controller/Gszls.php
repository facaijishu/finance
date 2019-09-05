<?php
namespace app\console\controller;

class Gszls extends ConsoleBase{
    public function index(){
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $neeq = $this->request->get('neeq', '');
        $zqjc = $this->request->get('zqjc', '');

        $gszls = model('Gszls')
                ->alias("g")
                ->join("CaijiStatus cs" , "cs.neeq=g.neeq","LEFT")
                ->field("SQL_CALC_FOUND_ROWS g.*,cs.gszls_status");
        //搜索条件
        if('' !== $neeq) {
            $gszls->where('g.neeq','like','%'.$neeq.'%');
        }
        if('' !== $zqjc) {
            $gszls->where('g.zqjc','like','%'.$zqjc.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $gszls->order($_order);
        }
//        $gszls->field('SQL_CALC_FOUND_ROWS *');
        $list = $gszls->limit($start, $length)->select();
        $result = $gszls->query('SELECT FOUND_ROWS() as count');
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
    public function hide(){
        $id = $this->request->param('id/d', 0);
        $model = model('Gszls');
        if($model->hideGszls($id)){
            $this->result('', 1, '隐藏成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '隐藏失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function show(){
        $id = $this->request->param('id/d', 0);
        $model = model('Gszls');
        if($model->showGszls($id)){
            $this->result('', 1, '展示成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '展示失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("Gszls");
        $info = $model->getGszlsInfoById($id);
        $this->assign("info" , $info);
        return view();
    }
}