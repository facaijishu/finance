<?php
namespace app\console\controller;

class StGonggaos extends ConsoleBase{
    public function index(){
        return view();
    }
    public function successExamine(){
        return view();
    }
    public function failExamine(){
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $date1 = $this->request->get('date1', '');
        $date2 = $this->request->get('date2', '');
        $title = $this->request->get('title', '');
        $secu_s_name = $this->request->get('secu_s_name', '');
        $secu_link_code = $this->request->get('secu_link_code', '');
        $gonggaos = model('StGonggaos')
                ->alias("sgs")
                ->join("StSceus ss" , "sgs.ggid=ss.ggid" , 'LEFT')
                ->join("StGonggaosStatus sgss" , "sgss.ggid=sgs.ggid" ,"LEFT")
                ->field("SQL_CALC_FOUND_ROWS sgs.id,sgs.title,sgs.date,sgs.created_at,ss.secu_link_code,ss.secu_s_name,sgss.examine");
//                ->where('examine is null');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $gonggaos->where('sgs.created_at','between',[$create_date1.'00:00:00' , $create_date2.'23:59:59']);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $gonggaos->where('sgs.created_at','>',$create_date1.'00:00:00');
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $gonggaos->where('sgs.created_at','<',$create_date2.'23:59:59');
        }
        if('' !== $date1 && '' !== $date2) {
            $gonggaos->where('sgs.date','between',[$date1.'00:00:00' , $date2.'23:59:59']);
        }elseif ('' !== $date1 && '' == $date2) {
            $gonggaos->where('sgs.date','>',$date1.'00:00:00');
        }elseif ('' == $date1 && '' !== $date2) {
            $gonggaos->where('sgs.date','<',$date2.'23:59:59');
        }
        //搜索条件
        if('' !== $title) {
            $gonggaos->where('sgs.title','like','%'.$title.'%');
        }
        if('' !== $secu_s_name) {
            $gonggaos->where('ss.secu_s_name','like','%'.$secu_s_name.'%');
        }
        if('' !== $secu_link_code) {
            $gonggaos->where('ss.secu_link_code','like','%'.$secu_link_code.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $gonggaos->order($_order);
        }
        $list = $gonggaos->limit($start, $length)->select();
        $result = $gonggaos->query('SELECT FOUND_ROWS() as count');
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
    public function successRead(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $date1 = $this->request->get('date1', '');
        $date2 = $this->request->get('date2', '');
        $title = $this->request->get('title', '');
        $secu_s_name = $this->request->get('secu_s_name', '');
        $secu_link_code = $this->request->get('secu_link_code', '');
        $gonggaos = model('StGonggaos')
                ->alias("sgs")
                ->join("StSceus ss" , "sgs.ggid=ss.ggid" ,"LEFT")
                ->join("StGonggaosStatus sgss" , "sgss.ggid=sgs.ggid" ,"LEFT")
                ->field("SQL_CALC_FOUND_ROWS sgs.*,ss.secu_link_code,ss.secu_s_name,sgss.examine")
                ->where('examine','eq',1);
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $gonggaos->where('sgs.created_at','between',[$create_date1.'00:00:00' , $create_date2.'23:59:59']);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $gonggaos->where('sgs.created_at','>',$create_date1.'00:00:00');
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $gonggaos->where('sgs.created_at','<',$create_date2.'23:59:59');
        }
        if('' !== $date1 && '' !== $date2) {
            $gonggaos->where('sgs.date','between',[$date1.'00:00:00' , $date2.'23:59:59']);
        }elseif ('' !== $date1 && '' == $date2) {
            $gonggaos->where('sgs.date','>',$date1.'00:00:00');
        }elseif ('' == $date1 && '' !== $date2) {
            $gonggaos->where('sgs.date','<',$date2.'23:59:59');
        }
        //搜索条件
        if('' !== $title) {
            $gonggaos->where('sgs.title','like','%'.$title.'%');
        }
        if('' !== $secu_s_name) {
            $gonggaos->where('ss.secu_s_name','like','%'.$secu_s_name.'%');
        }
        if('' !== $secu_link_code) {
            $gonggaos->where('ss.secu_link_code','like','%'.$secu_link_code.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $gonggaos->order($_order);
        }
        $list = $gonggaos->limit($start, $length)->select();
        $result = $gonggaos->query('SELECT FOUND_ROWS() as count');
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
    public function failRead(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $date1 = $this->request->get('date1', '');
        $date2 = $this->request->get('date2', '');
        $title = $this->request->get('title', '');
        $secu_s_name = $this->request->get('secu_s_name', '');
        $secu_link_code = $this->request->get('secu_link_code', '');
        $gonggaos = model('StGonggaos')
                ->alias("sgs")
                ->join("StSceus ss" , "sgs.ggid=ss.ggid" ,"LEFT")
                ->join("StGonggaosStatus sgss" , "sgss.ggid=sgs.ggid" ,"LEFT")
                ->field("SQL_CALC_FOUND_ROWS sgs.*,ss.secu_link_code,ss.secu_s_name,sgss.examine")
                ->where('examine','eq',2);
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $gonggaos->where('sgs.created_at','between',[$create_date1.'00:00:00' , $create_date2.'23:59:59']);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $gonggaos->where('sgs.created_at','>',$create_date1.'00:00:00');
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $gonggaos->where('sgs.created_at','<',$create_date2.'23:59:59');
        }
        if('' !== $date1 && '' !== $date2) {
            $gonggaos->where('sgs.date','between',[$date1.'00:00:00' , $date2.'23:59:59']);
        }elseif ('' !== $date1 && '' == $date2) {
            $gonggaos->where('sgs.date','>',$date1.'00:00:00');
        }elseif ('' == $date1 && '' !== $date2) {
            $gonggaos->where('sgs.date','<',$date2.'23:59:59');
        }
        //搜索条件
        if('' !== $title) {
            $gonggaos->where('sgs.title','like','%'.$title.'%');
        }
        if('' !== $secu_s_name) {
            $gonggaos->where('ss.secu_s_name','like','%'.$secu_s_name.'%');
        }
        if('' !== $secu_link_code) {
            $gonggaos->where('ss.secu_link_code','like','%'.$secu_link_code.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $gonggaos->order($_order);
        }
        $list = $gonggaos->limit($start, $length)->select();
        $result = $gonggaos->query('SELECT FOUND_ROWS() as count');
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
        $model = model("StGonggaos");
        $info = $model->getStGonggaosInfoById($id);
        $this->assign("info" , $info);
        return view();
    }
    public function examine(){
        $id = $this->request->param('id/d');
        $model = model("StGonggaos");
        if($model->setStGonggaosExamine($id)){
            $data['url'] = url('StGonggaos/successExamine');
            $this->result($data, 1, '审核成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '审核失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function examinefail(){
        $id = $this->request->param('id/d');
        $model = model("StGonggaos");
        if($model->setStGonggaosExaminefail($id)){
            $data['url'] = url('StGonggaos/failExamine');
            $this->result($data, 1, '审核不通过成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '审核不通过失败';
            $this->result('', 0, $error, 'json');
        }
    }
}