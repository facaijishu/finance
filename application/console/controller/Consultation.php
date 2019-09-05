<?php
namespace app\console\controller;

class Consultation extends ConsoleBase{
    public function index(){
        $model = model("dict");
        $list = $model->getDictByType('consultation');
        $this->assign('list' , $list);
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
        $status = $this->request->get('status', '');
        $keyword = $this->request->get('keyword', '');

        $consultation = model('Consultation');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $consultation->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $consultation->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $consultation->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        if('' !== $type) {
            $consultation->where(['type'=>$type]);
        }
        if('' !== $status) {
            $consultation->where(['status'=>$status]);
        }
        if('' !== $keyword) {
            $consultation->where('title','like','%'.$keyword.'%');
        }
        $consultation->where('is_show','eq',1);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $consultation->order($_order);
        }
        $consultation->field('SQL_CALC_FOUND_ROWS *');
        $list = $consultation->limit($start, $length)->select();
        $result = $consultation->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
//        $library = model("MaterialLibrary");
        $dict = model("dict");
        $user = model("User");
        foreach ($list as $key => $item) {
//            $top_img = $library->getMaterialInfoById($item['top_img']);
//            $list[$key]['top_img'] = $top_img['url_thumb'];
            $type = $dict->getDictInfoById($item['type']);
            $list[$key]['type'] = $type['value'];
            $create_user = $user->getUserById($item['create_uid']);
            $list[$key]['create_uid'] = $create_user['real_name'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function add(){
        if(request()->isPost()){
            $consultation = model('Consultation');
            if($consultation->createConsultation(input())){
                $data['url'] = url('Consultation/index');
                $this->result($data, 1, '添加资讯成功', 'json');
            }else{
                $error = $consultation->getError() ? $consultation->getError() : '添加资讯失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $model = model("dict");
            $consultation = $model->getDictByType('consultation');
            $this->assign('consultation' , $consultation);
            $this->assign('time' , date('Y-m-d', time()));
            return view();
        }
    }
    public function edit(){
        if(request()->isPost()){
            $consultation = model('Consultation');
            if($consultation->createConsultation(input())){
                $data['url'] = url('Consultation/index');
                $this->result($data, 1, '修改资讯成功', 'json');
            }else{
                $error = $consultation->getError() ? $consultation->getError() : '修改资讯失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('con_id/d');
            $model = model("dict");
            $consultation = $model->getDictByType('consultation');
            $this->assign('consultation' , $consultation);
            $model = model("Consultation");
            $info = $model->getConsultationInfoById($id);
            if($info['release_date'] == ''){
                $info['release_date'] = date('Y-m-d', time());
            }
            $this->assign('info' , $info);
            return view();
        }
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("Consultation");
        $info = $model->getConsultationInfoById($id);
        $model = model("dict");
        $type = $model->getDictInfoById($info['type']);
        $info['type'] = $type['value'];
        $this->assign("info" , $info);
        return view();
    }
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('Consultation');
        if($model->stopConsultation($id)){
            $this->result('', 1, '取消发布成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消发布失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('Consultation');
        if($model->startConsultation($id)){
            $this->result('', 1, '发布成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '发布失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function hotTrue(){
        $id = $this->request->param('id/d', 0);
        $model = model('Consultation');
        if($model->hotTrueConsultation($id)){
            $this->result('', 1, '置顶资讯成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '置顶资讯失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function hotFalse(){
        $id = $this->request->param('id/d', 0);
        $model = model('Consultation');
        if($model->hotFalseConsultation($id)){
            $this->result('', 1, '取消置顶资讯成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消置顶资讯失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function delete(){
        $id = $this->request->param('id/d');
        $model = model("Consultation");
        if($result = $model->deleteConsultation($id)){
            $data['url'] = url('Consultation/index');
            $this->result($data, 1, '删除资讯成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '删除资讯失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function uploads(){
        $file = request()->file('file');
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'consultation' . DS . 'img';
        if(!is_dir($path)){
            mkdir($path , 0777, true);
        }
        $result = $file->move($path);
        $date = substr($result->getSaveName(), 0 , 8);
        if($result){
            $data['name'] = $result->getFilename();
            $data['url'] = 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . 'consultation' . DS . 'img' . DS . $date . DS . $data['name'];
            $this->result($data ,1 , '上传成功' , 'json');
        }else{
            $this->result('', 0, $file->getError(), 'json');
        }
    }
}