<?php
namespace app\console\controller;

class StYanbaos extends ConsoleBase{
    public function index(){
        return view();
    }
    public function sucai(){
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        $date1 = $this->request->get('date1', '');
        $date2 = $this->request->get('date2', '');
        $title = $this->request->get('title', '');
        $org = $this->request->get('org', '');
        $yanbaos = model('StYanbaosEffect');
        //搜索条件
        if('' !== $date1 && '' !== $date2) {
            $yanbaos->where('report_date','between',[$date1.'00:00:00' , $date2.'23:59:59']);
        }elseif ('' !== $date1 && '' == $date2) {
            $yanbaos->where('report_date','>',$date1.'00:00:00');
        }elseif ('' == $date1 && '' !== $date2) {
            $yanbaos->where('report_date','<',$date2.'23:59:59');
        }
        //搜索条件
        if('' !== $title) {
            $yanbaos->where('title','like','%'.$title.'%');
        }
        if('' !== $org) {
            $yanbaos->where('org','like','%'.$org.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $yanbaos->order($_order);
        }
        $yanbaos->field('SQL_CALC_FOUND_ROWS *');
        $list = $yanbaos->limit($start, $length)->select();
        $result = $yanbaos->query('SELECT FOUND_ROWS() as count');
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
    public function sucairead(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));
        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $date1 = $this->request->get('date1', '');
        $date2 = $this->request->get('date2', '');
        $title = $this->request->get('title', '');
        $org = $this->request->get('org', '');
        $yanbaos = model('StYanbaos');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $yanbaos->where('created_at','between',[$create_date1.'00:00:00' , $create_date2.'23:59:59']);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $yanbaos->where('created_at','>',$create_date1.'00:00:00');
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $yanbaos->where('created_at','<',$create_date2.'23:59:59');
        }
        if('' !== $date1 && '' !== $date2) {
            $yanbaos->where('report_date','between',[$date1.'00:00:00' , $date2.'23:59:59']);
        }elseif ('' !== $date1 && '' == $date2) {
            $yanbaos->where('report_date','>',$date1.'00:00:00');
        }elseif ('' == $date1 && '' !== $date2) {
            $yanbaos->where('report_date','<',$date2.'23:59:59');
        }
        //搜索条件
        if('' !== $title) {
            $yanbaos->where('title','like','%'.$title.'%');
        }
        if('' !== $org) {
            $yanbaos->where('org','like','%'.$org.'%');
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $yanbaos->order($_order);
        }
        $yanbaos->field('SQL_CALC_FOUND_ROWS *');
        $list = $yanbaos->limit($start, $length)->select();
        $result = $yanbaos->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        foreach ($list as $key => $item) {
            $list[$key]['author'] = '';
            $authors = db("st_yb_authors")->where(['yb_id' => $item['yb_id']])->select();
            $author = '';
            foreach ($authors as $key1 => $value) {
                $author .= ','.$value['auth'];
            }
            $author = trim($author, ',');
            $list[$key]['author'] = $author;
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;
        echo json_encode($return);
    }
    public function add(){
        if(request()->isPost()){
            $data = input();
            $project = model('StYanbaosEffect');
            if($project->createStYanbaosEffect($data)){
                $data['url'] = url('StYanbaos/index');
                $this->result($data, 1, '添加研报成功', 'json');
            }else{
                $error = $project->getError() ? $project->getError() : '添加研报失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $gszles = model("Gszls")->field("neeq,zqjc")->select();
            $this->assign('gszles' , $gszles);
            return view();
        }
    }
    public function edit(){
        if(request()->isPost()){
            $data = input();
            $model = model('StYanbaosEffect');
            if($model->createStYanbaosEffect($data)){
                $data['url'] = url('StYanbaos/detail' , ['id' => $data['id']]);
                $this->result($data, 1, '修改研报成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '修改研报失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d', 0);
            $info = model("StYanbaosEffect")->getStYanbaosEffectInfoById($id);
            $info['report_date'] = substr($info['report_date'], 0, 10);
            $info['relation_arr'] = explode(',', $info['relation']);
            $this->assign('info' , $info);
            $gszles = model("Gszls")->field("neeq,zqjc")->select();
            $this->assign('gszles' , $gszles);
            return view();
        }
    }
    public function sucaidetail(){
        $id = $this->request->param('id/d');
        $model = model("StYanbaos");
        $info = $model->getStYanbaosInfoById($id);
        $this->assign("info" , $info);
        return view();
    }
    public function detail(){
        $id = $this->request->param('id/d');
        $model = model("StYanbaosEffect");
        $info = $model->getStYanbaosEffectInfoById($id);
        if($info['relation'] != ''){
            $gszls = explode(',', $info['relation']);
            $gszlstr = '';
            foreach ($gszls as $key => $value) {
                $gg = model("Gszls")->getGszlsInfoByNeeq($value);
                $gszlstr .= ','.$gg['zqjc'].'('.$gg['neeq'].')';
            }
            $info['relation'] = trim($gszlstr, ',');
        }
        $this->assign("info" , $info);
        return view();
    }
    public function release(){
        $id = $this->request->param('id/d');
        $model = model("StYanbaosEffect");
        if($model->releaseStYanbaosEffect($id)){
            $data['url'] = url('StYanbaos/index');
            $this->result($data, 1, '发布成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '发布失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function exhibition_true(){
        $id = $this->request->param('id/d');
        $model = model("StYanbaosEffect");
        if($model->setStYanbaosEffectExhibitionTrue($id)){
            $data['url'] = url('StYanbaos/detail' , ['id' => $id]);
            $this->result($data, 1, '展示成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '展示失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function exhibition_false(){
        $id = $this->request->param('id/d');
        $model = model("StYanbaosEffect");
        if($model->setStYanbaosEffectExhibitionFalse($id)){
            $data['url'] = url('StYanbaos/detail' , ['id' => $id]);
            $this->result($data, 1, '隐藏成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '隐藏失败';
            $this->result('', 0, $error, 'json');
        }
    }
}