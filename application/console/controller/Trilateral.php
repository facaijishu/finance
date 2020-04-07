<?php
namespace app\console\controller;

class Trilateral extends ConsoleBase{
    
    /**
     * 三方演绎首页
     * @return unknown
     */
    public function index(){
        return view();
    }
    
    /**
     * 三方演绎一览
     */
    public function read(){
        $return     = [];
        $order      = $this->request->get('order/a', []);
        $start      = $this->request->get('start', 0);
        $length     = $this->request->get('length', config('paginate.list_rows'));
        $title      = $this->request->get('title', '');

        $trilateral = model('Trilateral');
        //搜索条件
        if('' !== $title) {
            $trilateral->where('title','like','%'.$title.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $trilateral->order($_order);
        }
        $trilateral->field('SQL_CALC_FOUND_ROWS *');
        $list   = $trilateral->limit($start, $length)->select();
        
        $result = $trilateral->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        
        $data   = [];
        foreach ($list as $key => $item) {
            $data[] = $item->toArray();
        }
        $return['data']             = $data;
        $return['recordsFiltered']  = $total;
        $return['recordsTotal']     = $total;

        echo json_encode($return);
    }
    
    /**
     * 添加路演
     * @return unknown
     */
    public function add(){
        if(request()->isPost()){
            $trilateral = model('Trilateral');
            if($trilateral->createTrilateral(input())){
                $data['url'] = url('Trilateral/index');
                $this->result($data, 1, '添加路演成功', 'json');
            }else{
                $error = $trilateral->getError() ? $trilateral->getError() : '添加路演失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            return view();
        }
    }
    
    /**
     * 编辑页面信息
     * @return unknown
     */
    public function edit(){
        if(request()->isPost()){
            $trilateral = model('Trilateral');
            if($trilateral->createTrilateral(input())){
                $data['url'] = url('Trilateral/index');
                $this->result($data, 1, '修改路演成功', 'json');
            }else{
                $error = $trilateral->getError() ? $trilateral->getError() : '修改路演失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d');
            $model = model("Trilateral");
            $info  = $model->getTrilateralById($id);
            $this->assign('info' , $info);
            return view();
            
        }
    }
    
    /**
     * 详细页面信息
     * @return unknown
     */
    public function detail(){
        $id    = $this->request->param('id/d');
        $model = model("Trilateral");
        $info  = $model->getTrilateralById($id);
        $this->assign("info" , $info);
        return view();
    }
    
    /**
     * 下架
     */
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('Trilateral');
        if($model->stopTrilateral($id)){
            $this->result('', 1, '停止成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '停止失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 上架
     */
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('Trilateral');
        if($model->startTrilateral($id)){
            $this->result('', 1, '开启成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '开启失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    
    /**
     * 上传图片
     */
    public function uploads(){
        $file = request()->file('file');
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'trilateral' . DS . 'img';
        if(!is_dir($path)){
            mkdir($path , 0777, true);
        }
        $result = $file->move($path);
        $date = substr($result->getSaveName(), 0 , 8);
        if($result){
            $data['name'] = $result->getFilename();
            $data['url']  = 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . 'trilateral' . DS . 'img' . DS . $date . DS . $data['name'];
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
        $model = model("Trilateral");
        if($model->setOrder($data)){
            $data['url'] = url('Trilateral/index');
            $this->result($data, 1, '设置排序成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '设置排序失败';
            $this->result('', 0, $error, 'json');
        }
    }
}