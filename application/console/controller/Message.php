<?php
namespace app\console\controller;

class Message extends ConsoleBase{
    public function index(){
        $model = model("project");
        $result = $model->select();
        $this->assign('list' , $result);
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $keyname = $this->request->get('keyname', '');
        $pro_id = $this->request->get('pro_id', 0);
        $message = model('message');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $message->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $message->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $message->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $keyname) {
            $message->where('content','like','%'.$keyname.'%');
        }
        if('0' !== $pro_id) {
            $message->where('pro_id','eq',$pro_id);
        }
        $message->where('pid','eq',0);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $message->order($_order);
        }
        $message->field('SQL_CALC_FOUND_ROWS *');
        $list = $message->limit($start, $length)->select();
        $result = $message->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $member = model("member");
        $project = model("project");
        foreach ($list as $key => $item) {
            $user_info = $member->getMemberInfoById($item['create_uid']);
            $pro_info = $project->getProjectInfoById($item['pro_id']);
            $list[$key]['create_uid'] = $user_info['userName'];
            $list[$key]['pro_id'] = $pro_info['pro_name'];
            $list[$key]['last_time'] = date('Y-m-d H:i:s' , $item['last_time']);
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("message");
        $info = $model->getMessageInfoById($id);
        $this->assign('info' , $info);
        return view();
    }
    public function add(){
        $data = input();
        $news = model('message');
        if($news->createMessage($data)){
            $data['url'] = url('Message/detail' , ['id' => $data['id']]);
            $this->result($data, 1, '添加备注成功', 'json');
        }else{
            $error = $news->getError() ? $news->getError() : '添加备注失败';
            $this->result('', 0, $error, 'json');
        }
    }
}