<?php
namespace app\console\controller;

class KfMessage extends ConsoleBase{
    public function index(){
        $member = model("Member");
        $customer = model("SelfCustomerService");
        $list1 = $member->select();
        $list2 = $customer->where(['status' => 1])->select();
        $this->assign('list1' , $list1);
        $this->assign('list2' , $list2);
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $m_uid = $this->request->get('m_uid', '');
        $scs_id = $this->request->get('scs_id', '');
        $openId = $this->request->get('openId', '');
        $type = $this->request->get('type', '');

        $kf_message = model('KfMessage');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $kf_message->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $kf_message->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $kf_message->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $m_uid) {
            $kf_message->where('m_uid','eq',$m_uid);
        }
        if('' !== $scs_id) {
            $kf_message->where('scs_id','eq',$scs_id);
        }
        if('' !== $openId) {
            $kf_message->where('openId','like','%'.$openId.'%');
        }
        if('' !== $type) {
            $kf_message->where('type','eq',$type);
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $kf_message->order($_order);
        }
        $kf_message->field('SQL_CALC_FOUND_ROWS *');
        $list = $kf_message->limit($start, $length)->select();
        $result = $kf_message->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $member = model("Member");
        $customer = model("SelfCustomerService");
        foreach ($list as $key => $item) {
            $member_info = $member->getMemberInfoById($item['m_uid']);
            $list[$key]['m_uid'] = $member_info['userName'];
            $customer_info = $customer->getSelfCustomerServiceInfoById($item['scs_id']);
            $list[$key]['scs_id'] = $customer_info['kf_nick'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
}