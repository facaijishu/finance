<?php
namespace app\console\controller;

class Study extends ConsoleBase{
    public function index(){
//        $model = model("Study");
//        $list = $model->where(['url' => ''])->select();
//        $aa = '';
//        foreach ($list as $key => $value) {
//            $result = $model->addStudyUrl($value);
//            if(!$result){
//                $aa .= $result;
//            }
//        }
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

//        $start_time = $this->request->get('start_time', '');
//        $end_time = $this->request->get('end_time', '');
//        $keyword = $this->request->get('keyword', '');

        $study = model('Study');
        $study->alias("s")
                ->join("Member m" , "s.openId=m.openId")
                ->join("Through t" , "m.uid=t.uid")
                ->field("SQL_CALC_FOUND_ROWS s.*,m.userPhoto,m.userName,t.realName,t.phone,m.userType");
        //搜索条件
//        if('' !== $start_time && '' !== $end_time) {
//            $spoiler->where('create_time','between',[strtotime($start_time.'00:00:00') , strtotime($end_time.'23:59:59')]);
//        }elseif ('' !== $start_time && '' == $end_time) {
//            $spoiler->where('create_time','>',strtotime($start_time.'00:00:00'));
//        }elseif ('' == $start_time && '' !== $end_time) {
//            $spoiler->where('create_time','<',strtotime($end_time.'23:59:59'));
//        }
//        if('' !== $keyword) {
//            $spoiler->where('title','like','%'.$keyword.'%');
//        }
        $study->where('s.status','eq',0);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $study->order($_order);
        }
        $list = $study->limit($start, $length)->select();
        $result = $study->query('SELECT FOUND_ROWS() as count');
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
    public function already(){
        return view();
    }

    public function alreadyread(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $start_time = $this->request->get('start_time', '');
        $end_time = $this->request->get('end_time', '');
        $openId = $this->request->get('openId', '');

        $study = model('Study');
        $study->alias("s")
                ->join("Member m" , "s.openId=m.openId")
                ->join("Through t" , "m.uid=t.uid")
                ->field("SQL_CALC_FOUND_ROWS s.*,m.userPhoto,m.userName,t.realName,t.phone,m.userType");
        //搜索条件
        if('' !== $start_time && '' !== $end_time) {
            $study->where('s.create_time','between',[strtotime($start_time.'00:00:00') , strtotime($end_time.'23:59:59')]);
        }elseif ('' !== $start_time && '' == $end_time) {
            $study->where('s.create_time','>',strtotime($start_time.'00:00:00'));
        }elseif ('' == $start_time && '' !== $end_time) {
            $study->where('s.create_time','<',strtotime($end_time.'23:59:59'));
        }
        if('' !== $openId) {
            $study->where('s.openId','like','%'.$openId.'%');
        }
        $study->where('s.status','eq',1);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $study->order($_order);
        }
        $list = $study->limit($start, $length)->select();
        $result = $study->query('SELECT FOUND_ROWS() as count');
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
    public function deal(){
        $id = $this->request->param('id/d', 0);
        $model = model('Study');
        if($model->dealStudy($id)){
            $this->result('', 1, '处理成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '处理失败!';
            $this->result('', 0, $error, 'json');
        }
    }
}