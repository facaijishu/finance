<?php
namespace app\console\controller;

class Dict extends ConsoleBase{
    public function index(){
        $model = model("dict_type");
        $dict_type_list = $model->getDictTypeList();
        $this->assign('dict_type_list' , $dict_type_list);
        return view();
    }
    public function read(){
        $return = [];
        $order = $this->request->get('order/a', []);
        $start = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $name = $this->request->get('name', '');
        $type = $this->request->get('type', '');
        //所属行业1级标签选择
        $firstTypeSelect = $this->request->get('firstTypeSelect', '');
        $dict = model('dict');
        //搜索条件
        if('' !== $name) {
            $dict->where('value','like','%'.$name.'%');
        }
        if('' !== $type) {
            $dict->where('dt_id','eq',$type);
        }
         if('' !== $firstTypeSelect) {
            $dict->where('fid','eq',$firstTypeSelect);
        }
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $dict->order($_order);
        }
        $dict->field('SQL_CALC_FOUND_ROWS *');
        $list = $dict->limit($start, $length)->select();
        $result = $dict->query('SELECT FOUND_ROWS() as count');
        $total = $result[0]['count'];
        $data = [];
        $model = model("dict_type");
        $user = model("user");
        foreach ($list as $key => $item) {
            $type_info = $model->getDictTypeInfoById($item['dt_id']);
            $user_info = $user->getUserById($item['create_uid']);
            $list[$key]['dt_id'] = $type_info['name'];
            $list[$key]['create_uid'] = $user_info['real_name'];
            $data[] = $item->toArray();
        }
        $return['data'] = $data;
        $return['recordsFiltered'] = $total;
        $return['recordsTotal'] = $total;

        echo json_encode($return);
    }
    public function add(){
        $model = model("dict");
        if($model->createDict(input())){
            $data['url'] = url('Dict/index');
            $this->result($data, 1, '添加数据成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '添加数据失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function edit(){
        $model = model("dict");
        if($model->createDict(input())){
            $data['url'] = url('Dict/index');
            $this->result($data, 1, '修改数据成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '修改数据失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function find(){
        $id = $this->request->param('id/d', 0);
        $model = model("dict");
        $info = $model->getDictInfoById($id);
        if(!empty($info)){
            $this->result($info, 1, '查看成功！', 'json');
        }else{
            $error = $sheet->getError() ? $sheet->getError() : '查看失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('dict');
        if($model->stopDict($id)){
            $this->result('', 1, '停止成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '停止失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('dict');
        if($model->startDict($id)){
            $this->result('', 1, '开启成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '开启失败!';
            $this->result('', 0, $error, 'json');
        }
    }
     //返回所属行业的一级标签
    public function industry()
    {
        $dt_id = input();
        $dict = model('Dict');
        $re = $dict->getParentIndustry($dt_id['dt_id']);
        if($re){
          $this->result($re,1,'','json');  
        } else {
          $this->result('',0,'','json');
        }
        
    }
    //根据前端传入的标签判断是否是所属行业或业务类型的二级标签，如果是，返回对应的一级标签
    public function industryEdit()
    {
        $data = input();
        $dict = model('Dict');
        $re = $dict->getSingleDict($data['value'],$data['dt_id']);
        if($re){
          $this->result($re['fid'],1,'','json');
        } else {
          $this->result('',0,'','json');
        }
    }
    //返回业务类型的一级标签
     public function serviceType()
    {
        $dt_id = input();
        $dict = model('Dict');
        $re = $dict->getParentServiceType($dt_id['dt_id']);
        if($re){
          $this->result($re,1,'','json');  
        } else {
          $this->result('',0,'','json');
        }
        
    }
    //根据前端传入的标签判断是否是业务类型的二级标签，如果是，返回对应的一级标签
    public function serviceTypeEdit()
    {
        $data = input();
        $dict = model('Dict');
        $re = $dict->getSingleDict($data['value'],$data['dt_id']);
        if($re){
          $this->result($re['fid'],1,'','json');
        } else {
          $this->result('',0,'','json');
        }
    }
}
