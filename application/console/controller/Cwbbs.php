<?php
namespace app\console\controller;

class Cwbbs extends ConsoleBase{
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
        $zqjc = $this->request->get('zqjc', '');
        $neeq = $this->request->get('neeq', '');

        $cwbbs = model('Cwbbs')
                ->alias("c")
                ->join("Gszls gs" , "gs.neeq=c.neeq")
//                ->join("CaijiStatus cs" , "cs.neeq=c.neeq","LEFT")
                ->field("SQL_CALC_FOUND_ROWS c.id,c.neeq,c.type,c.reportdate,c.created_at,gs.zqjc");
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $cwbbs->where('created_at','between',[$create_date1 , $create_date2]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $cwbbs->where('created_at','>',$create_date1);
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $cwbbs->where('created_at','<',$create_date2);
        }
        //搜索条件
        if('' !== $zqjc) {
            $cwbbs->where('c.zqjc','like','%'.$zqjc.'%');
        }
        if('' !== $neeq) {
            $cwbbs->where('c.neeq','like','%'.$neeq.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $cwbbs->order($_order);
        }
//        $gszls->field('SQL_CALC_FOUND_ROWS *');
        $list = $cwbbs->limit($start, $length)->select();
        $result = $cwbbs->query('SELECT FOUND_ROWS() as count');
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
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("Cwbbs");
        $info = $model->getCwbbsInfoById($id);
        $this->assign("info" , $info);
        return view();
    }
}