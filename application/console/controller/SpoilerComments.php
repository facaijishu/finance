<?php
namespace app\console\controller;

class SpoilerComments extends ConsoleBase{
    public function index(){
        $model = model("Member");
        $member = $model->select();
        $this->assign('member' , $member);
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $start_time = $this->request->get('start_time', '');
        $end_time = $this->request->get('end_time', '');
        $sp_id = $this->request->get('sp_id', '');
        $pid = $this->request->get('pid', '');
        $uid = $this->request->get('uid', '');

        $spoiler_comments = model('SpoilerComments');
        //搜索条件
        if('' !== $start_time && '' !== $end_time) {
            $spoiler_comments->where('create_time','between',[strtotime($start_time.'00:00:00') , strtotime($end_time.'23:59:59')]);
        }elseif ('' !== $start_time && '' == $end_time) {
            $spoiler_comments->where('create_time','>',strtotime($start_time.'00:00:00'));
        }elseif ('' == $start_time && '' !== $end_time) {
            $spoiler_comments->where('create_time','<',strtotime($end_time.'23:59:59'));
        }
        if('' !== $sp_id) {
            $spoiler_comments->where('sp_id','eq',$sp_id);
        }
        if('' !== $pid) {
            $spoiler_comments->where('pid','eq',$pid);
        }
        if('' !== $uid) {
            $spoiler_comments->where('uid','eq',$uid);
        }
        $spoiler_comments->where('status','eq',1);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $spoiler_comments->order($_order);
        }
        $spoiler_comments->field('SQL_CALC_FOUND_ROWS *');
        $list = $spoiler_comments->limit($start, $length)->select();
        $result = $spoiler_comments->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $model = model("Member");
        foreach ($list as $key => $item) {
            if($item['pid'] == 0){
                $list[$key]['pid'] = '-';
            }
            $comment = $model->getMemberInfoById($item['uid']);
            $list[$key]['uid'] = $comment['userName'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function delete(){
        $id = $this->request->param('id/d');
        $model = model("SpoilerComments");
        if($model->deleteSpoilerComments($id)){
            $data['url'] = url('SpoilerComments/index');
            $this->result($data, 1, '删除成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '删除失败';
            $this->result('', 0, $error, 'json');
        }
    }
}
