<?php
namespace app\console\controller;

class CarouselFigure extends ConsoleBase{
    public function index(){
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $title = $this->request->get('title', '');

        $carousel_figure = model('CarouselFigure');
        //搜索条件
        if('' !== $title) {
            $carousel_figure->where('title','like','%'.$title.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $carousel_figure->order($_order);
        }
        $carousel_figure->field('SQL_CALC_FOUND_ROWS *');
        $list = $carousel_figure->limit($start, $length)->select();
        $result = $carousel_figure->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $library = model("MaterialLibrary");
        $user = model("User");
        foreach ($list as $key => $item) {
            $img = $library->getMaterialInfoById($item['img']);
            $list[$key]['img'] = $img['url'];
            $user_info = $user->getUserById($item['create_uid']);
            $list[$key]['create_uid'] = $user_info['real_name'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function add(){
        if(request()->isPost()){
            $carouselfigure = model('CarouselFigure');
            if($carouselfigure->createCarouselFigure(input())){
                $data['url'] = url('CarouselFigure/index');
                $this->result($data, 1, '添加轮播图成功', 'json');
            }else{
                $error = $carouselfigure->getError() ? $carouselfigure->getError() : '添加轮播图失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            return view();
        }
    }
    public function edit(){
        if(request()->isPost()){
            $carouselfigure = model('CarouselFigure');
            if($carouselfigure->createCarouselFigure(input())){
                $data['url'] = url('CarouselFigure/index');
                $this->result($data, 1, '修改轮播图成功', 'json');
            }else{
                $error = $carouselfigure->getError() ? $carouselfigure->getError() : '修改轮播图失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d');
            $model = model("CarouselFigure");
            $info = $model->getCarouselFigureInfoById($id);
            $this->assign('info' , $info);
            return view();
        }
    }
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('CarouselFigure');
        if($model->stopCarouselFigure($id)){
            $this->result('', 1, '停止成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '停止失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('CarouselFigure');
        if($model->startCarouselFigure($id)){
            $this->result('', 1, '开启成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '开启失败!';
            $this->result('', 0, $error, 'json');
        }
    }
}