<?php

namespace app\console\controller;

class Send extends ConsoleBase{
    public function index() {
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $start_time = $this->request->get('start_time', '');
        $end_time = $this->request->get('end_time', '');
        $type = $this->request->get('type', '');

        $send = model('Send');
        //搜索条件
        if('' !== $start_time && '' !== $end_time) {
            $send->where('created_at','between',[strtotime($start_time.'00:00:00') , strtotime($end_time.'23:59:59')]);
        }elseif ('' !== $start_time && '' == $end_time) {
            $send->where('created_at','>',strtotime($start_time.'00:00:00'));
        }elseif ('' == $start_time && '' !== $end_time) {
            $send->where('created_at','<',strtotime($end_time.'23:59:59'));
        }
        if('' !== $type) {
            $send->where('type','=',$type);
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $send->order($_order);
        }
        $send->field('SQL_CALC_FOUND_ROWS *');
        $list = $send->limit($start, $length)->select();
        $result = $send->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $project = model('Project');
        $zryxes = model('ZryxesEffect');
        foreach ($list as $key => $item) {
            if ($item['type'] == 'project') {
                $info = $project->getProjectInfoById($item['act_id']);
                $list[$key]['pro_name'] = $info['pro_name'];
            }
            if ($item['type'] == 'zryxes') {
                $info = $zryxes->getZryxesEffectInfoById($item['act_id']);
                $list[$key]['pro_name'] = $info['sec_uri_tyshortname'];
            }
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("Send");
        $info = $model->getSendInfoById($id);
        $this->assign("info" , $info);
        //发送成功分页
        /*$model = model("SendList");
        $true_list = $model->getSendListByActId($id, 'true', 0 , 10);
        $true_count = $model->getSendListByActId($id, 'true');
        $this->assign('true_list' , $true_list);
        $num = intval(count($true_count)/10);
        if(count($true_count)%10 > 0){
            $total = $num++;
        } else {
            $total = $num;
        }
        $this->assign('total_true' , $num);*/
        //发送失败分页
        /*$model = model("SendList");
        $false_list = $model->getSendListByActId($id, 'false', 0 , 10);
        $false_count = $model->getSendListByActId($id, 'false');*/
        $model = model('SendError');
        $false_list = $model->getSendErrorByActId($id,0,10);
        if(!empty($false_list)){
            foreach ($false_list as $key => $value) {
                if($value['type'] == 1){
                    $false_list[$key]['type'] = '发送失败';
                }
                if($value['type'] == 2){
                    $false_list[$key]['type'] = '退订用户';
                }
            }
        }
        $this->assign('false_list' , $false_list);
       // $num = intval(count($false_count)/10);
       // if(count($false_count)%10 > 0){
        $total_num = count($false_list);
        $this->assign('total_num' , $total_num);
        $num = intval($total_num/10);
        if(count($false_list)%10 > 0){
            $total = $num++;
        } else {
            $total = $num;
        }
        $this->assign('total_false' , $num);
        return view();
    }
    public function sendRefresh() {
        $id = $this->request->param('id/d');
        $model = model("Send");
        $info = $model->refreshSend($id);
        if(!empty($info)){
            $this->result($info, 1, '推送成功！', 'json');
        }else{
            $error = $sheet->getError() ? $sheet->getError() : '推送失败!';
            $this->result('', 0, $error, 'json');
        }
    }

    public function pages(){
        $total = $this->request->param('total/s');
        $page = $this->request->param('page/s');
        $send_id = $this->request->param('send_id/d');
        $result = $this->request->param('result/s');
        $id = $this->request->param('id/s');
        if(($page == 1 && $id == 'previous') || ($page == $total && $id == 'next')){
            if($id == 'previous'){
                $this->result('', 2, '已到首页', 'json');
            } else {
                $this->result('', 2, '已到尾页', 'json');
            }
        } else {
            if($id == 'previous'){
                $start = (intval($page)-1-1)*10;
                $page = intval($page)-1;
            } else {
                $start = (intval($page)+1-1)*10;
                $page = intval($page)+1;
            }
            $model = model("SendList");
            $news_list = $model->getSendListByActId($send_id , $result ,$start, 10);
            $data = [];
            $data['page'] = $page;
            $data['content'] = $news_list;
            if($news_list){
                $this->result($data, 1, '翻页成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '翻页失败';
                $this->result('', 0, $error, 'json');
            }
        }
    }
}
