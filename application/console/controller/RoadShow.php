<?php
namespace app\console\controller;

class RoadShow extends ConsoleBase{
    public function index(){
        return view();
    }
    public function read(){
        $return = [];
        $order  = $this->request->get('order/a', []);
        $start  = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $road_name = $this->request->get('road_name', '');

        $roadshow = model('RoadShow');
        //搜索条件
        if('' !== $road_name) {
            $roadshow->where('road_name','like','%'.$road_name.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $roadshow->order($_order);
        }
        $roadshow->field('SQL_CALC_FOUND_ROWS *');
        $list   = $roadshow->limit($start, $length)->select();
        
        $result = $roadshow->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        
        $data   = [];
        $model  = model("dict");
        foreach ($list as $key => $item) {
            $type = $model->getDictInfoById($item['type']);
            $list[$key]['type'] = $type['value'];
            $list[$key]['duration'] = changeLongTime($item['duration']);
            $data[] = $item->toArray();
        }
        $return['data']             = $data;
        $return['recordsFiltered']  = $total;
        $return['recordsTotal']     = $total;

        echo json_encode($return);
    }
    
    public function add(){
        if(request()->isPost()){
            $roadshow = model('RoadShow');
            if($roadshow->createRoadShow(input())){
                $data['url'] = url('RoadShow/index');
                $this->result($data, 1, '添加路演成功', 'json');
            }else{
                $error = $roadshow->getError() ? $roadshow->getError() : '添加路演失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $model = model("dict");
            $roadshow_form = $model->getDictByType('roadshow_form');
            $this->assign('roadshow_form' , $roadshow_form);
            return view();
        }
    }
    public function edit(){
        if(request()->isPost()){
            $roadshow = model('RoadShow');
            if($roadshow->createRoadShow(input())){
                $data['url'] = url('RoadShow/index');
                $this->result($data, 1, '修改路演成功', 'json');
            }else{
                $error = $roadshow->getError() ? $roadshow->getError() : '修改路演失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('rs_id/d');
            $model = model("dict");
            $roadshow_form = $model->getDictByType('roadshow_form');
            $this->assign('roadshow_form' , $roadshow_form);
            $model = model("RoadShow");
            $info = $model->getRoadShowInfoById($id);
            $this->assign('info' , $info);
            return view();
        }
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("RoadShow");
        $info = $model->getRoadShowInfoById($id);
        $model = model("dict");
        $type = $model->getDictInfoById($info['type']);
        $info['type'] = $type['value'];
        $this->assign("info" , $info);
        return view();
    }
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('RoadShow');
        if($model->stopRoadShow($id)){
            $this->result('', 1, '停止成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '停止失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('RoadShow');
        if($model->startRoadShow($id)){
            $this->result('', 1, '开启成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '开启失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function uploads(){
        $file = request()->file('file');
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'roadshow' . DS . 'img';
        if(!is_dir($path)){
            mkdir($path , 0777, true);
        }
        $result = $file->move($path);
        $date = substr($result->getSaveName(), 0 , 8);
        if($result){
            $data['name'] = $result->getFilename();
            $data['url'] = 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . 'roadshow' . DS . 'img' . DS . $date . DS . $data['name'];
            $this->result($data ,1 , '上传成功' , 'json');
        }else{
            $this->result('', 0, $file->getError(), 'json');
        }
    }
    
    /**
     * 设置排序
     */
    public function set(){
        
        $data       = [];
        $id         = $this->request->post('id/d', 0);
        $list_order = $this->request->post('list_order/d', 0);
        
        $data['id']         = $id;
        $data['list_order'] = $list_order;
        $model = model("RoadShow");
        if($model->setOrder($data)){
            $data['url'] = url('RoadShow/index');
            $this->result($data, 1, '设置排序成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '设置排序失败';
            $this->result('', 0, $error, 'json');
        }
    }
}