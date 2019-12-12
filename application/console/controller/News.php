<?php
namespace app\console\controller;

class News extends ConsoleBase{
    public function index(){
        return view();
    }
    
    public function read(){
        $return = [];
        $order  = $this->request->get('order/a', []);
        $start  = $this->request->get('start', 0);
        $length = $this->request->get('length', config('paginate.list_rows'));

        $create_date1 = $this->request->get('create_date1', '');
        $create_date2 = $this->request->get('create_date2', '');
        $status       = $this->request->get('status', '');
        $title        = $this->request->get('title', '');

        $news = model('News');
        //搜索条件
        if('' !== $create_date1 && '' !== $create_date2) {
            $news->where('create_time','between',[strtotime($create_date1.'00:00:00') , strtotime($create_date2.'23:59:59')]);
        }elseif ('' !== $create_date1 && '' == $create_date2) {
            $news->where('create_time','>',strtotime($create_date1.'00:00:00'));
        }elseif ('' == $create_date1 && '' !== $create_date2) {
            $news->where('create_time','<',strtotime($create_date2.'23:59:59'));
        }
        
        if('' !== $title) {
            $consultation->where('title','like','%'.$title.'%');
        }
        
        if('' !== $status) {
            $news->where(['status'=>$status]);
        }
     
        $news->where('is_show','eq',1);
        
        //排序
        if(!empty($order)) {
            foreach ($order as $item) {
                $_order = [];
                $_order[$item['column']] = $item['dir'];
            }
            $news->order($_order);
        }
        
        $news->field('SQL_CALC_FOUND_ROWS *');
        $list   = $news->limit($start, $length)->select();
        $result = $news->query('SELECT FOUND_ROWS() as count');
        $total  = $result[0]['count'];
        $data   = [];
        $dict   = model("dict");
        $user   = model("User");
        
        foreach ($list as $key => $item) {
            $type = $dict->getDictInfoById($item['type']);
            $list[$key]['type'] = $type['value'];
            $create_user = $user->getUserById($item['create_uid']);
            $list[$key]['create_uid'] = $create_user['real_name'];
            $data[] = $item->toArray();
        }
        $return['data']             = $data;
        $return['recordsFiltered']  = $total;
        $return['recordsTotal']     = $total;

        echo json_encode($return);
    }
    
    public function add(){
        if(request()->isPost()){
            $news = model('News');
            if($news->createNews(input())){
                $data['url'] = url('News/index');
                $this->result($data, 1, '添加资讯成功', 'json');
            }else{
                $error = $news->getError() ? $news->getError() : '添加资讯失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            //一级行业类
            $model        = model("dict");
            $top_industry = $model->getTopIndustry();
            if (!empty($top_industry)) {
                foreach ($top_industry as $key => $each_top_industry) {
                    //二级行业
                    $son_industry = $model->getIndustry($each_top_industry['id']);
                    if (!empty($son_industry)) {
                        $top_industry[$key]['son'] = $son_industry;
                    } else {
                        $top_industry[$key]['son'] = [];
                    }
                    
                }
            }
            $this->assign('inc_industry' , $top_industry);
            
            $type = config('news_type');
            $this->assign('typeList' , $type);
            
            $this->assign('time' , date('Y-m-d', time()));
            return view();
        }
    }
    
    public function edit(){
        if(request()->isPost()){
            $news = model('News');
            if($news->createNews(input())){
                $data['url'] = url('News/index');
                $this->result($data, 1, '修改资讯成功', 'json');
            }else{
                $error = $news->getError() ? $news->getError() : '修改资讯失败';
                $this->result('', 0, $error, 'json');
            }
        }else{
            $id = $this->request->param('id/d');
            $model        = model("dict");
            $top_industry = $model->getTopIndustry();
            if (!empty($top_industry)) {
                foreach ($top_industry as $key => $each_top_industry) {
                    //二级行业
                    $son_industry = $model->getIndustry($each_top_industry['id']);
                    if (!empty($son_industry)) {
                        $top_industry[$key]['son'] = $son_industry;
                    } else {
                        $top_industry[$key]['son'] = [];
                    }
                    
                }
            }
            $this->assign('inc_industry' , $top_industry);
            
            $type = config('news_type');
            $this->assign('typeList' , $type);
            
            $model = model("News");
            $info  = $model->getNewsInfoById($id);
            $this->assign('info' , $info);
            return view();
        }
    }
    
    public function detail(){
        $id = $this->request->param('id/d');
        $model  = model("News");
        $info   = $model->getNewsInfoById($id);
        
        $model               = model("dict");
        $industry_pro_name   = $model->getidList($info['industry_pro']);
        $industry_org_name   = $model->getidList($info['industry_org']);
        $info['pro_name']    = $industry_pro_name;
        $info['org_name']    = $industry_org_name;
        
        $type   = config('news_type');
        $info['type_name'] = $type[$info['type']];
        
        $this->assign("info" , $info);
        return view();
    }
    
    public function stop(){
        $id = $this->request->param('id/d', 0);
        $model = model('News');
        if($model->stopNews($id)){
            $this->result('', 1, '取消发布成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消发布失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function start(){
        $id = $this->request->param('id/d', 0);
        $model = model('News');
        if($model->startNews($id)){
            $this->result('', 1, '发布成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '发布失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function hotTrue(){
        $id = $this->request->param('id/d', 0);
        $model = model('News');
        if($model->hotTrueNews($id)){
            $this->result('', 1, '置顶资讯成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '置顶资讯失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function hotFalse(){
        $id = $this->request->param('id/d', 0);
        $model = model('News');
        if($model->hotFalseNews($id)){
            $this->result('', 1, '取消置顶资讯成功！', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '取消置顶资讯失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function delete(){
        $id = $this->request->param('id/d');
        $model = model("News");
        if($result = $model->deleteNews($id)){
            $data['url'] = url('News/index');
            $this->result($data, 1, '删除资讯成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '删除资讯失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function uploads(){
        $file = request()->file('file');
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'pc_consultation' . DS . 'img';
        if(!is_dir($path)){
            mkdir($path , 0777, true);
        }
        $result = $file->move($path);
        $date = substr($result->getSaveName(), 0 , 8);
        if($result){
            $data['name'] = $result->getFilename();
            $data['url'] = 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . 'pc_consultation' . DS . 'img' . DS . $date . DS . $data['name'];
            $this->result($data ,1 , '上传成功' , 'json');
        }else{
            $this->result('', 0, $file->getError(), 'json');
        }
    }
}