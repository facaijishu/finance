<?php
namespace app\console\controller;

class Order extends ConsoleBase{
    public function index(){
        return view();
    }
    public function wait(){
        return view();
    }

    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $type = $this->request->get('type', '');
        $out_trade_no = $this->request->get('out_trade_no', '');

        $orders = model('Order');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $orders->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $orders->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $orders->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $type) {
            $orders->where(['type' , 'eq' , $type]);
        }
        if('' !== $out_trade_no) {
            $orders->where('out_trade_no','like','%'.$out_trade_no.'%');
        }
        $orders->where('is_pay','eq',1);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $orders->order($_order);
        }
        $orders->field('SQL_CALC_FOUND_ROWS *');
        $list = $orders->limit($start, $length)->select();
        $result = $orders->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            $list[$key]['pay_time'] = date('Y-m-d H:i:s' , $item['pay_time']);
            $list[$key]['total_fee'] = floatval($item['total_fee']/100);
            $model = model("Member");
            $user = $model->getMemberInfoById($item['uid']);
            $list[$key]['uid'] = $user['userName'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function readWait(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $type = $this->request->get('type', '');
        $out_trade_no = $this->request->get('out_trade_no', '');

        $orders = model('Order');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $orders->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $orders->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $orders->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $type) {
            $orders->where(['type' , 'eq' , $type]);
        }
        if('' !== $out_trade_no) {
            $orders->where('out_trade_no','like','%'.$out_trade_no.'%');
        }
        $orders->where('is_pay','eq',0);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $orders->order($_order);
        }
        $orders->field('SQL_CALC_FOUND_ROWS *');
        $list = $orders->limit($start, $length)->select();
        $result = $orders->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            $list[$key]['pay_time'] = '-';
            $list[$key]['total_fee'] = floatval($item['total_fee']/100);
            $model = model("Member");
            $user = $model->getMemberInfoById($item['uid']);
            $list[$key]['uid'] = $user['userName'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
}