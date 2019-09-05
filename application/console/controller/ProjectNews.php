<?php
namespace app\console\controller;

class ProjectNews extends ConsoleBase{
    public function index(){
        return view();
    }
    public function add(){
        $data = input();
        $news = model('project_news');
        if($news->createProjectNews($data)){
            $data['url'] = url('Project/detail' , ['id' => $data['pro_id']]);
            $this->result($data, 1, '添加新闻成功', 'json');
        }else{
            $error = $news->getError() ? $news->getError() : '添加新闻失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function edit(){
        $data = input();
        $news = model('project_news');
        if($news->createProjectNews($data)){
            $data['url'] = url('Project/detail' , ['id' => $data['pro_id']]);
            $this->result($data, 1, '修改新闻成功', 'json');
        }else{
            $error = $news->getError() ? $news->getError() : '修改新闻失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function find(){
        $id = $this->request->param('id/d');
        $model = model("project_news");
        $info = $model->getProjectNewsInfoById($id);
        if(!empty($info)){
            $this->result($info, 1, '查看成功！', 'json');
        }else{
            $error = $sheet->getError() ? $sheet->getError() : '查看失败!';
            $this->result('', 0, $error, 'json');
        }
    }
    public function pages(){
        $total = $this->request->param('total/s');
        $page = $this->request->param('page/s');
        $pro_id = $this->request->param('pro_id/d');
        $id = $this->request->param('id/s');
        if(($page == 1 && $id == 'previous') || ($page == $total && $id == 'next')){
            if($id == 'previous'){
                $this->result('', 2, '已到首页', 'json');
            } else {
                $this->result('', 2, '已到尾页', 'json');
            }
        } else {
            if($id == 'previous'){
                $start = (intval($page)-1-1)*5;
                $page = intval($page)-1;
            } else {
                $start = (intval($page)+1-1)*5;
                $page = intval($page)+1;
            }
            $model = model("project_news");
            $news_list = $model->getProjectListByProId($pro_id , $start , 5);
            $data = [];
            $data['page'] = $page;
            $data['content'] = $news_list;
            if($news_list){
                $this->result($data, 1, '翻页成功', 'json');
            }else{
                $error = $model->getError() ? $model->getError() : '翻页失败';
                $this->result('', 0, $error, 'json');
            }
        }
    }
    public function delete(){
        $id = $this->request->param('id/d');
        $pro_id = $this->request->param('pro_id/d');
        $model = model("project_news");
        if($model->deleteProjectNews($id)){
            $data['url'] = url('Project/detail' , ['id' => $pro_id]);
            $this->result($data, 1, '删除成功', 'json');
        }else{
            $error = $model->getError() ? $model->getError() : '删除失败';
            $this->result('', 0, $error, 'json');
        }
    }

    public function upload(){
        $file = request()->file('file');
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'project_news' . DS . 'img';
        $result = $file->move($path);
        $date = substr($result->getSaveName(), 0 , 8);
        if($result){
            $data['name'] = $result->getFilename();
            $data['url'] = 'http://' . $_SERVER['SERVER_NAME'] . DS . 'uploads' . DS . 'project_news' . DS . 'img' . DS . $date . DS . $data['name'];
            $this->result($data ,1 , '上传成功' , 'json');
        }else{
            $this->result('', 0, $file->getError(), 'json');
        }
    }
}