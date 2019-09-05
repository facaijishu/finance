<?php
namespace app\console\controller;

class Conversation extends ConsoleBase{
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
        $openId = $this->request->get('openId', '');
        $kf_account = $this->request->get('kf_account', '');

        $conversation = model('Conversation');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $conversation->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $conversation->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $conversation->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $openId) {
            $conversation->where('openId','like','%'.$openId.'%');
        }
        if('' !== $kf_account) {
            $conversation->where('kf_account','like','%'.$kf_account.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $conversation->order($_order);
        }
        $conversation->field('SQL_CALC_FOUND_ROWS *');
        $list = $conversation->limit($start, $length)->select();
        $result = $conversation->query('SELECT FOUND_ROWS() as count');
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
}