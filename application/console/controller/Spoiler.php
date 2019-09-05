<?php
namespace app\console\controller;

class Spoiler extends ConsoleBase{
    public function index(){
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $start_time = $this->request->get('start_time', '');
        $end_time = $this->request->get('end_time', '');
        $keyword = $this->request->get('keyword', '');

        $spoiler = model('spoiler');
        //搜索条件
        if('' !== $start_time && '' !== $end_time) {
            $spoiler->where('create_time','between',[strtotime($start_time.'00:00:00') , strtotime($end_time.'23:59:59')]);
        }elseif ('' !== $start_time && '' == $end_time) {
            $spoiler->where('create_time','>',strtotime($start_time.'00:00:00'));
        }elseif ('' == $start_time && '' !== $end_time) {
            $spoiler->where('create_time','<',strtotime($end_time.'23:59:59'));
        }
        if('' !== $keyword) {
            $spoiler->where('title','like','%'.$keyword.'%');
        }
        $spoiler->where('is_show','eq',1);
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $spoiler->order($_order);
        }
        $spoiler->field('SQL_CALC_FOUND_ROWS *');
        $list = $spoiler->limit($start, $length)->select();
        $result = $spoiler->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $user = model("user");
        foreach ($list as $key => $item) {
            $user_info = $user->getUserById($item['create_uid']);
            $list[$key]['create_uid'] = $user_info['real_name'];
            $model = model("material_library");
            if($item['img'] != ''){
                $img = explode(',', $item['img']);
                foreach ($img as $key1 => $value) {
                    $info = $model->getMaterialInfoById($value);
                     if ($info['type'] == 'application/pdf') {
                       $img[$key1] = $info['name']; 
                    } else {
                       $img[$key1] = $info['url_thumb'];
                    }
                }
                $list[$key]['img'] = implode(',', $img);
            }
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function add(){
        if(request()->isPost()){
            $spoiler = model('spoiler');
            if($spoiler->createSpoiler(input())){
                $data['url'] = url('spoiler/index');
                $this->result($data, 1, '添加剧透成功', 'json');
            }else{
                $error = $spoiler->getError() ? $spoiler->getError() : '添加剧透失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $this->assign('time' , date('Y-m-d', time()));
            return view();
        }
    }
    public function edit(){
        if(request()->isPost()){
            $spoiler = model('spoiler');
            if($spoiler->createSpoiler(input())){
                $data['url'] = url('spoiler/index');
                $this->result($data, 1, '修改剧透成功', 'json');
            }else{
                $error = $spoiler->getError() ? $spoiler->getError() : '修改剧透失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d', 0);
            $model = model("spoiler");
            $info = $model->getSpoilerInfoById($id);
            if($info['release_date'] == ''){
                $info['release_date'] = date('Y-m-d', time());
            }
            $this->assign('info' , $info);
            $model = model("material_library");
            $str = '';$url = [];$config = [];
            if($info['img'] != ''){
                $img = explode(',', $info['img']);
                foreach ($img as $key => $value) {
                    $material = $model->getMaterialInfoById($value);
                    $str .= $material['ml_id'].'-init_'.$key.',';
                    array_push($url, 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . $material['url_thumb']);
                    $config[$key]['caption'] = $material['name'];
                    $config[$key]['size'] = $material['filesize'];
                    $config[$key]['key'] = $key;
                    $config[$key]['url'] = url("Spoiler/deleteImg");
                }
            }
            $this->assign('str' , $str);
            $this->assign('url' , json_encode($url));
            $this->assign('config' , json_encode($config));
            return view();
        }
    }
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('Spoiler');
        if($model->stopSpoiler($id)){
            $this->result('', 1, '停止成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '停止失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('Spoiler');
        if($model->startSpoiler($id)){
            $this->result('', 1, '开启成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '开启失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function delete(){
        $id = $this->request->param('id/d');
        $model = model("Spoiler");
        if($result = $model->deleteSpoiler($id)){
            $data['url'] = url('Spoiler/index');
            $this->result($data, 1, '删除剧透成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '删除剧透失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function deleteImg(){
        $data = input();
        $this->result($data['key'], 1, '删除成功！', 'json');
    }
    public function uploads(){
        $file = request()->file('file');
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'spoiler' . DS . 'img';
        if(!is_dir($path)){
            mkdir($path , 0777, true);
        }
        $result = $file->move($path);
        $date = substr($result->getSaveName(), 0 , 8);
        if($result){
            $data['name'] = $result->getFilename();
            $data['url'] = 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . 'spoiler' . DS . 'img' . DS . $date . DS . $data['name'];
            $this->result($data ,1 , '上传成功' , 'json');
        }else{
            $this->result('', 0, $file->getError(), 'json');
        }
    }
}
